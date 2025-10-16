<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>임시 비밀번호 안내</title>
</head>
<body style="font-family: 'Noto Sans KR', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; line-height: 1.6; color: #333333; background-color: #f4f4f4; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
        <!-- 헤더 -->
        <div style="background-color: #4F46E5; color: #ffffff; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 700;">MQWAY 임시 비밀번호 안내</h1>
        </div>

        <!-- 본문 -->
        <div style="padding: 40px 30px;">
            <!-- 인사말 -->
            <div style="font-size: 16px; margin-bottom: 20px;">
                <strong>{{ $userName }}</strong>님, 안녕하세요.
            </div>

            <!-- 안내 메시지 -->
            <div style="font-size: 14px; color: #666666; margin-bottom: 30px; line-height: 1.8;">
                요청하신 임시 비밀번호가 발급되었습니다.<br>
                아래의 임시 비밀번호로 로그인하신 후, 반드시 비밀번호를 변경해주세요.
            </div>

            <!-- 임시 비밀번호 박스 -->
            <div style="background-color: #f8f9fa; border: 2px solid #4F46E5; border-radius: 6px; padding: 20px; margin: 30px 0; text-align: center;">
                <div style="font-size: 14px; color: #666666; margin-bottom: 10px;">임시 비밀번호</div>
                <div style="font-size: 24px; font-weight: 700; color: #4F46E5; letter-spacing: 2px; font-family: 'Courier New', monospace;">{{ $temporaryPassword }}</div>
            </div>

            <!-- 보안 주의사항 -->
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; font-size: 13px; color: #856404;">
                <strong style="display: block; margin-bottom: 8px; font-size: 14px;">⚠️ 보안 주의사항</strong>
                임시 비밀번호는 타인에게 노출되지 않도록 주의하시고,<br>
                로그인 후 즉시 새로운 비밀번호로 변경해주시기 바랍니다.
            </div>

            <!-- 비밀번호 변경 방법 -->
            <div style="background-color: #f8f9fa; border-radius: 6px; padding: 20px; margin: 20px 0;">
                <strong style="margin-bottom: 10px; display: block; color: #333;">비밀번호 변경 방법</strong>
                <ol style="margin: 0; padding-left: 20px;">
                    <li style="margin-bottom: 10px; font-size: 14px; color: #555555;">위의 임시 비밀번호로 로그인합니다.</li>
                    <li style="margin-bottom: 10px; font-size: 14px; color: #555555;">마이페이지 → 프로필 설정으로 이동합니다.</li>
                    <li style="margin-bottom: 10px; font-size: 14px; color: #555555;">비밀번호 변경 메뉴에서 새로운 비밀번호를 설정합니다.</li>
                </ol>
            </div>

            <!-- 로그인 버튼 -->
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/login" style="display: inline-block; background-color: #4F46E5; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; margin: 20px 0; font-weight: 600;">로그인하기</a>
            </div>

            <!-- 추가 안내 -->
            <div style="margin-top: 30px; font-size: 13px; color: #999999; line-height: 1.8;">
                본인이 요청하지 않은 비밀번호 변경이라면 즉시 고객센터로 문의해주세요.
            </div>
        </div>

        <!-- 푸터 -->
        <div style="background-color: #f8f9fa; padding: 20px 30px; text-align: center; font-size: 12px; color: #999999; border-top: 1px solid #eeeeee;">
            <p style="margin: 0 0 10px 0;">
                본 메일은 발신전용 메일입니다.<br>
                문의사항은 MQWAY 고객센터를 이용해주세요.
            </p>
            <p style="margin: 10px 0 0 0;">
                &copy; {{ date('Y') }} MQWAY. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
