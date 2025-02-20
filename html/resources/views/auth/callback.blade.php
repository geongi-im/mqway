<!DOCTYPE html>
<html>
<head>
    <title>로그인 처리 중...</title>
</head>
<body>
    <script>
        @if(isset($error))
            window.opener.alert('{{ $error }}');
            window.close();
        @else
            // 부모 창 리다이렉트 후 팝업 닫기
            window.opener.location.href = '{{ $redirectUrl ?? '/' }}';
            window.close();
        @endif
    </script>
</body>
</html> 