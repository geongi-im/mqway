/*
 * 앱 푸시 알림 받기/끄기 (헤더 프로필 메뉴의 앱 알림 버튼)
 *
 * 설정은 레이아웃이 window.MQ_PUSH 로 넘긴다. (resources/views/components/push_subscribe.blade.php)
 *
 * 알림은 로그인한 회원 중 마케팅 정보 수신에 동의한 회원만 받는다.
 *   - 비로그인: 버튼이 프로필 메뉴 안에 있어 보이지 않는다. (누르면 로그인 화면으로 안내하는 처리는 남겨 둠)
 *   - 미동의 회원: 마이페이지의 마케팅 정보 수신 동의로 안내
 *   - 동의 회원: 아래 흐름으로 알림을 켠다
 *
 * 흐름
 *   1. 앱 알림 버튼을 누르면 알림 권한을 묻는다. iOS 는 버튼 클릭 안에서 바로 물어야 하므로
 *      다른 비동기 작업보다 권한 요청을 먼저 한다.
 *   2. 서비스 워커를 등록하고 FCM 토큰을 받아 서버(/push/token)에 보낸다.
 *      서버는 토큰을 회원과 함께 저장하고, 발송할 때 이 명단에서 대상을 고른다.
 *   3. 켜 둔 기기는 하루 한 번 토큰을 다시 보내 서버 명단을 갱신한다.
 *      토큰은 브라우저가 바꿀 수 있어서 그때도 새 토큰이 서버로 간다.
 *   4. 같은 브라우저에서 다른 회원이 로그인하면 이전 회원의 알림을 끈다.
 *      새 회원은 직접 앱 알림 버튼을 눌러야 받는다.
 */
import { initializeApp } from 'https://www.gstatic.com/firebasejs/12.19.0/firebase-app.js';
import { getMessaging, getToken, deleteToken, onMessage, isSupported } from 'https://www.gstatic.com/firebasejs/12.19.0/firebase-messaging.js';

const config = window.MQ_PUSH;

// 마지막으로 서버에 보낸 상태 {token, date, member: 회원 idx}
const STORAGE_KEY = 'mq_push_sync';
const SW_SCOPE = '/firebase-cloud-messaging-push-scope';

let messaging = null;
// 알림을 켜고 끄는 중인지. 그동안 버튼을 막고 스피너를 보여 여러 번 누르지 않게 한다.
let busy = false;
// 서비스 워커 등록 결과. 미리 등록해 두고 켤 때 그대로 쓴다.
let swReady = null;

function readSync() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
    } catch (e) {
        return null;
    }
}

function writeSync(value) {
    try {
        if (value) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(value));
        } else {
            localStorage.removeItem(STORAGE_KEY);
        }
    } catch (e) {
        // 저장소를 쓸 수 없는 환경(사생활 보호 모드 등)이면 매번 다시 동기화될 뿐이다.
    }
}

function today() {
    const d = new Date();
    return d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();
}

// 이 브라우저가 지금 로그인한 회원 명의로 알림을 켜 두었는지
function isRegistered() {
    const sync = readSync();
    return Notification.permission === 'granted' && !!sync && sync.member === config.memberIdx;
}

// 실제로 알림을 받는 상태인지 (켜 둠 + 마케팅 정보 수신 동의)
function isOn() {
    return !!config.memberIdx && config.marketingAgree && isRegistered();
}

function buttons() {
    return document.querySelectorAll('[data-push-toggle]');
}

function render() {
    const on = isOn();
    let title = '새 소식 알림 받기';
    if (busy) {
        title = '알림 설정 중';
    } else if (on) {
        title = '알림 받는 중 (누르면 끄기)';
    } else if (!config.memberIdx) {
        title = '새 소식 알림 받기 (로그인 필요)';
    }
    buttons().forEach(function (btn) {
        btn.classList.remove('hidden');
        btn.disabled = busy;
        btn.setAttribute('aria-busy', busy ? 'true' : 'false');
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        btn.title = title;
        btn.querySelector('[data-push-icon-on]').classList.toggle('hidden', busy || !on);
        btn.querySelector('[data-push-icon-off]').classList.toggle('hidden', busy || on);
        const spinner = btn.querySelector('[data-push-busy]');
        if (spinner) {
            spinner.classList.toggle('hidden', !busy);
        }
    });
}

// 화면을 바로 바꾸는 동기 함수다. 권한 요청 앞에서 불러도 iOS 의 클릭 제약에 걸리지 않는다.
function setBusy(value) {
    busy = value;
    render();
}

function toast(message, type) {
    const el = document.createElement('div');
    el.className = 'alert alert-' + (type || 'info');
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(function () {
        el.style.opacity = '0';
        setTimeout(function () { el.remove(); }, 500);
    }, 3000);
}

async function request(method, token) {
    const res = await fetch('/push/token', {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin',
        body: JSON.stringify({ token: token, platform: 'web' })
    });
    let data = {};
    try {
        data = await res.json();
    } catch (e) {
        // 본문이 JSON 이 아니면 아래에서 일반 오류로 처리한다.
    }
    if (!res.ok || !data.success) {
        throw new Error(data.message || '알림 설정에 실패했습니다. 잠시 후 다시 시도해주세요.');
    }
    return data;
}

async function registerServiceWorker() {
    const params = new URLSearchParams(config.firebase).toString();
    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js?' + params, { scope: SW_SCOPE });
    // 처음 설치할 때는 활성화될 때까지 기다려야 getToken 이 구독을 만들 수 있다.
    if (!registration.active) {
        await new Promise(function (resolve, reject) {
            const worker = registration.installing || registration.waiting;
            const check = function () {
                if (worker.state === 'activated') {
                    resolve();
                } else if (worker.state === 'redundant') {
                    // 설치 실패(SDK 를 못 받는 등). 기다리기만 하면 스피너가 멈추지 않는다.
                    reject(new Error('알림을 준비하지 못했습니다. 잠시 후 다시 시도해주세요.'));
                }
            };
            if (!worker) {
                resolve();
                return;
            }
            worker.addEventListener('statechange', check);
            check();
        });
    }
    return registration;
}

// 서비스 워커 등록은 한 번만 하고 결과를 함께 쓴다. 실패하면 다음 호출에서 다시 시도한다.
function serviceWorker() {
    if (!swReady) {
        swReady = registerServiceWorker().catch(function (e) {
            swReady = null;
            throw e;
        });
    }
    return swReady;
}

async function currentToken() {
    const registration = await serviceWorker();
    return getToken(messaging, { vapidKey: config.vapidKey, serviceWorkerRegistration: registration });
}

async function subscribe() {
    setBusy(true);
    // iOS 는 사용자 클릭 안에서 바로 권한을 물어야 한다. 이 호출 앞에 await 를 두지 않는다.
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        toast(permission === 'denied'
            ? '알림이 차단되어 있습니다. 브라우저 설정에서 이 사이트의 알림을 허용해 주세요.'
            : '알림 권한을 허용해야 받을 수 있어요.', 'warning');
        return;
    }

    const token = await currentToken();
    const data = await request('POST', token);
    writeSync({ token: token, date: today(), member: config.memberIdx });
    toast(data.message, 'success');
}

// 서버 명단에서 이 기기를 빼고 브라우저 토큰도 지운다.
async function release() {
    const sync = readSync();
    if (sync && sync.token) {
        await request('DELETE', sync.token);
    }
    try {
        await deleteToken(messaging);
    } catch (e) {
        // 서버에서는 이미 껐다. 브라우저 쪽 토큰 정리 실패는 다음에 켤 때 새 토큰으로 덮인다.
    }
    writeSync(null);
}

async function unsubscribe() {
    if (!confirm('새 소식 알림을 끌까요?')) {
        return;
    }
    setBusy(true);
    await release();
    toast('알림을 껐습니다.', 'info');
}

// 켜 둔 기기의 토큰을 하루 한 번 서버에 다시 보낸다.
// 다른 회원이 로그인했으면 이전 회원의 알림을 끈다. 로그아웃 상태에서는 건드리지 않는다.
async function refresh() {
    const sync = readSync();
    if (!sync || !config.memberIdx) {
        return;
    }
    if (sync.member !== config.memberIdx) {
        await release();
        render();
        return;
    }
    if (Notification.permission !== 'granted') {
        return;
    }
    const token = await currentToken();
    if (sync.token === token && sync.date === today()) {
        return;
    }
    await request('POST', token);
    writeSync({ token: token, date: today(), member: config.memberIdx });
}

async function onClick() {
    if (!config.memberIdx) {
        if (confirm('알림은 로그인한 회원만 받을 수 있어요. 로그인할까요?')) {
            location.href = config.loginUrl;
        }
        return;
    }
    if (isOn()) {
        await unsubscribe();
        return;
    }
    if (!config.marketingAgree) {
        if (confirm('알림은 마케팅 정보 수신에 동의한 회원에게 보내드려요.\n마이페이지에서 동의할까요?')) {
            location.href = config.agreeUrl;
        }
        return;
    }
    await subscribe();
}

async function init() {
    if (!config || !('serviceWorker' in navigator) || !('Notification' in window)) {
        return;
    }
    // iOS 사파리 탭처럼 웹 푸시를 못 쓰는 환경에서는 버튼을 숨긴 채로 둔다.
    if (!(await isSupported())) {
        return;
    }

    messaging = getMessaging(initializeApp(config.firebase));

    // 사이트 탭이 보이는 동안 온 알림은 SDK 가 띄우지 않고 여기로 넘긴다. 직접 띄운다.
    onMessage(messaging, async function (payload) {
        const n = payload.notification || {};
        const registration = await navigator.serviceWorker.getRegistration(SW_SCOPE);
        if (registration && n.title) {
            registration.showNotification(n.title, {
                body: n.body || '',
                icon: n.icon || (payload.data && payload.data.icon) || '/images/pwa/icon-192.png',
                data: { link: payload.data ? payload.data.link : null }
            });
        }
    });

    // 사용자가 브라우저 설정에서 알림을 막았으면 켜 둔 기록을 지운다.
    if (Notification.permission === 'denied' && readSync()) {
        writeSync(null);
    }

    render();

    // 알림을 켤 수 있는 회원이면 서비스 워커를 미리 등록해 둔다.
    // 처음 설치는 Firebase SDK 를 받고 활성화될 때까지 기다려야 해서, 버튼을 누른 뒤에 하면 그만큼 늦어진다.
    // 등록만 하는 것이라 알림 권한을 묻거나 구독을 만들지는 않는다.
    if (config.memberIdx && config.marketingAgree && !isRegistered()) {
        serviceWorker().catch(function (e) {
            console.warn('[push] 서비스 워커 미리 등록 실패', e);
        });
    }

    buttons().forEach(function (btn) {
        btn.addEventListener('click', async function () {
            // 켜고 끄는 데 몇 초씩 걸려서, 그동안 누른 것은 무시한다. (화면은 setBusy 가 막는다)
            if (busy) {
                return;
            }
            try {
                await onClick();
            } catch (e) {
                console.error('[push]', e);
                toast(e.message || '알림 설정에 실패했습니다.', 'error');
            } finally {
                setBusy(false);
            }
        });
    });

    refresh().catch(function (e) {
        console.warn('[push] 토큰 갱신 실패', e);
    });
}

init();
