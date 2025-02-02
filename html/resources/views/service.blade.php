@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">MQWAY 이용약관</h1>
    
    <div class="space-y-8 text-gray-600">
      <!-- 제1조 -->
      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">제1조 (목적)</h2>
        <p class="leading-relaxed">
          본 약관은 MQWAY (이하 "회사"라 함)가 운영하는 웹사이트 (이하 "MQWAY 웹사이트" 또는 "서비스"라 함)를 이용함에 있어 회사와 이용자의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.
        </p>
      </section>

      <!-- 제2조 -->
      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">제2조 (용어의 정의)</h2>
        <div class="space-y-3">
          <p><strong>"이용자"</strong>란 MQWAY 웹사이트에 접속하여 본 약관에 따라 회사가 제공하는 서비스를 이용하는 자를 말합니다.</p>
          <p><strong>"회원"</strong>이란 MQWAY 웹사이트에 회원가입을 하고 회사가 제공하는 서비스를 지속적으로 이용할 수 있는 자를 말합니다.</p>
          <p><strong>"콘텐츠"</strong>란 MQWAY 웹사이트를 통해 회사가 제공하는 텍스트, 이미지, 영상, 정보 등 모든 형태의 유무형의 자료를 의미합니다.</p>
        </div>
      </section>

      <!-- 제3조 -->
      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">제3조 (약관의 효력 및 변경)</h2>
        <div class="space-y-3">
          <p>본 약관은 MQWAY 웹사이트에 게시하거나 기타의 방법으로 이용자에게 공지함으로써 효력이 발생합니다.</p>
          <p>회사는 「약관의 규제에 관한 법률」, 「정보통신망 이용촉진 및 정보보호 등에 관한 법률」 등 관련 법령을 위반하지 않는 범위 내에서 본 약관을 변경할 수 있습니다.</p>
          <p>회사가 약관을 변경할 경우에는 변경된 약관의 내용과 적용일자를 명시하여 시행일 7일 전부터 MQWAY 웹사이트에 공지합니다. 다만, 이용자에게 불리하게 약관 내용을 변경하는 경우에는 시행일 30일 전에 공지합니다.</p>
          <p>이용자가 변경된 약관에 동의하지 않을 경우 회원 탈퇴를 요청할 수 있으며, 약관 변경 공지 후 7일 이내에 거부 의사를 표시하지 아니하거나, 회원 탈퇴를 하지 않는 경우에는 변경된 약관에 동의한 것으로 간주합니다.</p>
        </div>
      </section>

      <!-- 제4조 ~ 제10조는 비슷한 구조로 계속... -->

      <!-- 제11조 -->
      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">제11조 (준거법 및 재판 관할)</h2>
        <p class="leading-relaxed">
          본 약관은 대한민국 법률에 따라 규율되고 해석되며, 회사와 이용자 간에 발생한 분쟁에 대해서는 대한민국 법원을 관할 법원으로 합니다.
        </p>
      </section>

      <!-- 제12조 -->
      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">제12조 (문의)</h2>
        <div class="bg-gray-50 p-6 rounded-lg">
          <p class="mb-4">본 약관에 대한 문의사항은 다음 연락처로 문의해주시기 바랍니다.</p>
          <div class="space-y-2">
            <p><span class="font-bold">회사명:</span> MQWAY</p>
            <p><span class="font-bold">연락처:</span> mqway20@gmail.com</p>
          </div>
        </div>
      </section>

      <!-- 부칙 -->
      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">[부칙]</h2>
        <div class="bg-gray-50 p-4 rounded-lg">
          <p>본 약관은 2025년 1월 1일부터 시행됩니다.</p>
        </div>
      </section>
    </div>
  </div>
</div>
@endsection 