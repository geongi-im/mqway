@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">개인정보처리방침</h1>
    
    <!-- 서문 -->
    <div class="mb-12 text-gray-600">
      <p class="leading-relaxed">
        MQWAY (이하 '회사')는 정보주체의 개인정보의 중요성을 인식하고, 「개인정보 보호법」 및 관련 법령을 준수하여 정보주체의 개인정보를 안전하게 처리하고 있습니다. 회사는 정보주체의 개인정보를 보호하고 권익을 존중하기 위해 개인정보처리방침을 수립하여 공개하며, 정보주체가 언제든지 쉽게 확인할 수 있도록 노력하고 있습니다.
      </p>
    </div>

    <!-- 주요 개인정보 처리 표시 -->
    <div class="bg-gray-50 p-6 rounded-lg mb-12">
      <h2 class="text-xl font-bold text-gray-800 mb-4">주요 개인정보 처리 표시</h2>
      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <h3 class="font-bold text-gray-700 mb-2">수집하는 개인정보 항목</h3>
          <p class="text-gray-600">이름, 이메일 주소, 서비스 이용 기록, 접속 로그, 쿠키</p>
        </div>
        <div>
          <h3 class="font-bold text-gray-700 mb-2">개인정보 이용 목적</h3>
          <p class="text-gray-600">서비스 제공 및 관리, 회원 관리, 맞춤형 서비스 제공, 고충 처리, 마케팅 및 광고 활용</p>
        </div>
        <div>
          <h3 class="font-bold text-gray-700 mb-2">보유 및 이용기간</h3>
          <p class="text-gray-600">회원 탈퇴 시 혹은 관련 법령에 따른 기간까지</p>
        </div>
        <div>
          <h3 class="font-bold text-gray-700 mb-2">제3자 제공</h3>
          <p class="text-gray-600">소셜 로그인 연동 시 (Google, Apple)</p>
        </div>
      </div>
    </div>

    <!-- 개인정보 처리방침 본문 -->
    <div class="space-y-8 text-gray-600">
      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">1. 개인정보의 처리 목적</h2>
        <div class="space-y-4">
          <p>회사는 다음의 목적을 위하여 개인정보를 처리합니다. 처리하는 개인정보는 다음의 목적 이외의 용도로는 이용되지 않으며, 이용 목적이 변경되는 경우에는 「개인정보 보호법」 제18조에 따라 별도의 동의를 받는 등 필요한 조치를 이행할 예정입니다.</p>
          <div>
            <h3 class="font-bold text-gray-700 mb-2">회원 관리</h3>
            <p>회원 가입 및 관리, 서비스 이용 계약 이행 및 서비스 제공, 본인확인, 개인식별, 불량회원의 부정 이용 방지와 비인가 사용 방지, 가입 의사 확인, 연령확인, 만 14세 미만 아동 개인정보 수집 시 법정대리인 동의 여부 확인, 불만처리 등 민원처리, 고지사항 전달</p>
          </div>
          <div>
            <h3 class="font-bold text-gray-700 mb-2">서비스 제공</h3>
            <p>콘텐츠 제공, 맞춤형 서비스 제공, 본인인증, 연령인증, 회원제 서비스 제공</p>
          </div>
          <div>
            <h3 class="font-bold text-gray-700 mb-2">마케팅 및 광고 활용</h3>
            <p>신규 서비스(제품) 개발 및 맞춤 서비스 제공, 이벤트 및 광고성 정보 제공 및 참여 기회 제공, 인구통계학적 특성에 따른 서비스 제공 및 광고 게재, 서비스 이용 기록 및 접속 빈도 분석, 서비스 이용에 대한 통계</p>
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-3">2. 개인정보의 처리 및 보유 기간</h2>
        <p class="leading-relaxed">
          ① MQWAY은(는) 법령에 따른 개인정보 보유·이용기간 또는 정보주체로부터 개인정보를 수집 시에 동의 받은 개인정보 보유·이용기간 내에서 개인정보를 처리·보유합니다.<br>
          ② 각각의 개인정보 처리 및 보유 기간은 다음과 같습니다.
        </p>
        <ul class="list-disc ml-6 mt-2 space-y-1">
          <li>회원 가입 및 관리: 회원 탈퇴 시까지</li>
          <li>서비스 이용 기록: 서비스 종료 시까지</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-3">3. 정보주체의 권리·의무 및 그 행사방법</h2>
        <p class="leading-relaxed">
          이용자는 개인정보주체로서 다음과 같은 권리를 행사할 수 있습니다.
        </p>
        <ul class="list-disc ml-6 mt-2 space-y-1">
          <li>개인정보 열람 요구</li>
          <li>오류 등이 있을 경우 정정 요구</li>
          <li>삭제 요구</li>
          <li>처리정지 요구</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-3">4. 처리하는 개인정보의 항목</h2>
        <p class="leading-relaxed">
          MQWAY은(는) 다음의 개인정보 항목을 처리하고 있습니다.
        </p>
        <ul class="list-disc ml-6 mt-2 space-y-1">
          <li>필수항목: 이메일, 이름</li>
          <li>자동수집항목: 서비스 이용 기록, 접속 로그, 쿠키</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-3">5. 개인정보의 파기</h2>
        <p class="leading-relaxed">
          MQWAY은(는) 원칙적으로 개인정보 처리목적이 달성된 경우에는 지체없이 해당 개인정보를 파기합니다. 파기의 절차, 기한 및 방법은 다음과 같습니다.
        </p>
        <ul class="list-disc ml-6 mt-2 space-y-1">
          <li>파기절차: 불필요한 개인정보는 개인정보의 처리가 불필요한 것으로 인정되는 날로부터 5일 이내에 파기</li>
          <li>파기방법: 전자적 파일 형태의 정보는 기록을 재생할 수 없는 기술적 방법을 사용</li>
        </ul>
      </section>

      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">11. 개인정보 보호책임자</h2>
        <div class="bg-gray-50 p-6 rounded-lg">
          <p class="mb-4">회사는 개인정보 처리에 관한 업무를 총괄해서 책임지고, 개인정보 처리와 관련한 정보주체의 불만처리 및 피해구제 등을 위하여 아래와 같이 개인정보 보호책임자를 지정하고 있습니다.</p>
          <div class="space-y-2">
            <p><span class="font-bold">개인정보 보호책임자:</span> MQWAY (총괄 부서)</p>
            <p><span class="font-bold">개인정보 보호 담당부서:</span> 총괄 부서</p>
            <p><span class="font-bold">연락처:</span> mqway20@gmail.com</p>
          </div>
        </div>
      </section>

      <section>
        <h2 class="text-xl font-bold text-gray-800 mb-4">12. 개인정보 처리방침 변경</h2>
        <div class="space-y-4">
          <p>본 개인정보처리방침은 2025년 1월 1일부터 적용됩니다. 법령 및 정책 또는 회사 내부 정책 변경에 따라 내용의 추가, 삭제 및 수정이 있을 수 있습니다. 변경되는 개인정보처리방침은 시행일 7일 전에 회사 웹사이트를 통해 공지합니다.</p>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p><span class="font-bold">공고일자:</span> 2025년 1월 1일</p>
            <p><span class="font-bold">시행일자:</span> 2025년 1월 1일</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>
@endsection 