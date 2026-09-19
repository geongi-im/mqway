{{--
    뉴스 링크 중복 검사 (작성 / 수정 공통)

    같은 회원이 같은 기사를 두 번 올리지 못하게 입력 즉시 알려준다.
    최종 판정은 서버(BoardScrapController 의 mq_url 검증 규칙)가 하고,
    여기서는 저장 버튼을 누르기 전에 알려주는 역할만 한다.

    $scrap 이 넘어오면 수정 화면으로 보고 자기 자신을 중복 대상에서 제외한다.
--}}
<p id="urlDuplicateStatus" class="hidden text-sm items-start gap-1.5"></p>

<script>
(function () {
    var input = document.getElementById('mq_url');
    var status = document.getElementById('urlDuplicateStatus');
    var form = document.getElementById('scrapForm');

    if (!input || !status || !form) {
        return;
    }

    var endpoint = '{{ route('board-scrap.check-duplicate') }}';
    var csrfToken = '{{ csrf_token() }}';
    var ignoreIdx = {{ isset($scrap) ? (int) $scrap->idx : 'null' }};

    // 마지막으로 중복 판정을 받은 URL. 이 값과 같으면 저장을 막는다.
    var duplicatedUrl = null;
    var timer = null;

    var BASE_CLASS = 'text-sm flex items-start gap-1.5 mt-1';

    function render(state, message, linkUrl) {
        if (!state) {
            status.className = 'hidden';
            status.innerHTML = '';
            input.classList.remove('border-red-500', 'border-emerald-400');
            return;
        }

        var color = state === 'dup' ? 'text-red-500' : (state === 'ok' ? 'text-emerald-600' : 'text-gray-400');
        status.className = BASE_CLASS + ' ' + color;

        var html = '<span>' + message + '</span>';

        if (linkUrl) {
            html += ' <a href="' + linkUrl + '" class="underline font-semibold shrink-0" target="_blank" rel="noopener">기존 글 보기</a>';
        }

        status.innerHTML = html;

        input.classList.toggle('border-red-500', state === 'dup');
        input.classList.toggle('border-emerald-400', state === 'ok');
    }

    function check() {
        var url = input.value.trim();

        if (url === '') {
            duplicatedUrl = null;
            render(null);
            return;
        }

        render('checking', '중복 여부를 확인하는 중입니다...');

        var payload = { url: url };

        if (ignoreIdx) {
            payload.idx = ignoreIdx;
        }

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, data: data };
            });
        })
        .then(function (result) {
            // 입력이 그 사이에 바뀌었으면 늦게 온 응답은 버린다
            if (input.value.trim() !== url) {
                return;
            }

            if (result.data && result.data.requireLogin) {
                duplicatedUrl = null;
                render(null);
                return;
            }

            if (!result.ok || !result.data || !result.data.success) {
                // URL 형식 오류 등은 서버 검증에 맡기고 조용히 넘어간다
                duplicatedUrl = null;
                render(null);
                return;
            }

            if (result.data.exists) {
                duplicatedUrl = url;
                render('dup', '이미 스크랩한 뉴스입니다. 같은 기사는 한 번만 등록할 수 있습니다.', result.data.url);
                return;
            }

            duplicatedUrl = null;
            render('ok', '아직 스크랩하지 않은 뉴스입니다.');
        })
        .catch(function () {
            duplicatedUrl = null;
            render(null);
        });
    }

    input.addEventListener('input', function () {
        duplicatedUrl = null;
        render(null);

        if (timer) {
            clearTimeout(timer);
        }

        timer = setTimeout(check, 600);
    });

    input.addEventListener('blur', function () {
        if (timer) {
            clearTimeout(timer);
        }

        check();
    });

    form.addEventListener('submit', function (e) {
        if (duplicatedUrl !== null && input.value.trim() === duplicatedUrl) {
            e.preventDefault();
            alert('이미 스크랩한 뉴스입니다. 다른 뉴스 링크를 입력해주세요.');
            input.focus();
            return false;
        }

        return true;
    });

    // 뉴스 목록에서 넘어온 URL 은 페이지 하단 스크립트가 나중에 채우므로 훅을 열어둔다
    window.scrapUrlDuplicateCheck = check;

    // 값이 미리 채워진 채로 열린 경우(수정 화면)도 한 번 확인한다
    if (input.value.trim() !== '') {
        check();
    }
})();
</script>
