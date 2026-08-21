{{--
    업로드 총량(post_max_size) 초과 시 표시되는 페이지.
    ValidatePostSize는 StartSession보다 먼저 실행되는 전역 미들웨어이므로
    이 시점에는 세션/인증을 쓸 수 없다. 따라서 레이아웃을 상속하지 않고
    자체적으로 완결된 페이지로 작성한다.
--}}
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>업로드 용량 초과 - MQWAY</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #F7F8FA;
            color: #2D3047;
            font-family: -apple-system, BlinkMacSystemFont, 'Malgun Gothic', '맑은 고딕', 'Apple SD Gothic Neo', sans-serif;
        }
        .card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 16px;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(45, 48, 71, 0.08);
        }
        .icon {
            width: 56px;
            height: 56px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #FFF1F1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            line-height: 1;
        }
        h1 { margin: 0 0 12px; font-size: 20px; }
        p { margin: 0 0 8px; font-size: 14px; line-height: 1.7; color: #5A5F73; }
        .hint { font-size: 13px; color: #8A8FA0; margin-top: 16px; }
        .actions { margin-top: 28px; }
        .btn {
            display: inline-block;
            padding: 12px 28px;
            border: 0;
            border-radius: 10px;
            background: #4ECDC4;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover { background: #2AA9A0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">!</div>
        <h1>업로드 용량이 너무 큽니다</h1>
        <p>
            첨부한 파일의 전체 크기가 한 번에 보낼 수 있는 용량을 넘었습니다.<br>
            한 번에 총 <strong>{{ \App\Exceptions\Handler::maxPostSizeLabel() }}</strong>까지 업로드할 수 있습니다.
        </p>
        <p class="hint">파일 개수를 줄이거나 이미지 크기를 줄인 뒤 다시 시도해주세요.</p>
        <div class="actions">
            <button type="button" class="btn" onclick="history.back()">이전 화면으로</button>
        </div>
    </div>
</body>
</html>
