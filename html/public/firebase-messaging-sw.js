/*
 * 앱 푸시(FCM) 서비스 워커.
 *
 * Firebase 설정값은 등록할 때 주소 뒤에 붙여 넘긴다. (public/js/push.js 참고)
 * 이 파일을 정적 파일로 두고 설정을 .env 한 곳에서만 관리하기 위해서다.
 *
 * 알림 표시는 Firebase SDK 가 한다. 서버가 notification 을 담아 보내면, 사이트 탭이 하나도
 * 보이지 않을 때 SDK 가 알림을 띄우고, 탭이 보이면 페이지(push.js 의 onMessage)로 넘긴다.
 */

// 알림 클릭 처리.
// 서버는 열 주소를 data.link 로 보낸다. SDK 기본 처리는 webpush.fcm_options.link 만 여는데
// 그 값은 https 주소만 받아서 로컬(http)에서 쓸 수 없다. 그래서 SDK 를 불러오기 전에
// 먼저 등록해 SDK 보다 앞서 처리하고, SDK 쪽 처리는 막는다.
self.addEventListener('notificationclick', function (event) {
    var data = event.notification.data || {};
    // SDK 가 띄운 알림은 data.FCM_MSG 에 원본 메시지를, 페이지가 띄운 알림은 data.link 를 담는다.
    var fcm = data.FCM_MSG || {};
    var link = (fcm.data && fcm.data.link) || data.link;

    if (!link) {
        return;
    }

    event.stopImmediatePropagation();
    event.notification.close();
    event.waitUntil(openLink(link));
});

function openLink(link) {
    var target = new URL(link, self.location.origin).href;

    return self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windows) {
        for (var i = 0; i < windows.length; i++) {
            if (windows[i].url === target && 'focus' in windows[i]) {
                return windows[i].focus();
            }
        }
        return self.clients.openWindow(target);
    });
}

var params = new URL(self.location.href).searchParams;

importScripts(
    'https://www.gstatic.com/firebasejs/12.19.0/firebase-app-compat.js',
    'https://www.gstatic.com/firebasejs/12.19.0/firebase-messaging-compat.js'
);

firebase.initializeApp({
    apiKey: params.get('apiKey'),
    authDomain: params.get('authDomain'),
    projectId: params.get('projectId'),
    storageBucket: params.get('storageBucket'),
    messagingSenderId: params.get('messagingSenderId'),
    appId: params.get('appId')
});

firebase.messaging();
