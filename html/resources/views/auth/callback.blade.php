<!DOCTYPE html>
<html>
<head>
    <title>로그인 처리 중...</title>
</head>
<body>
    <script>
    @if(isset($error))
        window.opener.alert("{{ $error }}");
    @else
        window.opener.location.href = '/';
    @endif
    window.close();
    </script>
</body>
</html> 