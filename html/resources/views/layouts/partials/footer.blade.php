<footer class="bg-point py-4 mt-auto">
    <div class="container mx-auto text-center">
        <div class="mb-4 space-x-4">
            <a href="{{ route('service') }}" class="text-cdark hover:text-cgray text-sm transition-colors duration-200">서비스이용약관</a>
            <a href="{{ route('privacy') }}" class="text-cdark hover:text-cgray text-sm transition-colors duration-200">개인정보처리방침</a>
        </div>
        <p class="text-cdark text-sm">&copy;{{ date('Y') }} {{ config('app.name', 'MQWAY') }} All rights reserved.</p>
    </div>
</footer> 