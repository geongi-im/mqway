<footer class="bg-dark py-4 mt-auto">
    <div class="container mx-auto text-center">
        <div class="mb-4 space-x-4">
            <a href="{{ route('service') }}" class="text-secondary hover:text-secondary/80 text-sm">서비스 이용약관</a>
            <a href="{{ route('privacy') }}" class="text-secondary hover:text-secondary/80 text-sm">개인정보처리방침</a>
        </div>
        <p class="text-secondary text-sm">&copy;{{ date('Y') }} {{ config('app.name', 'MQWAY') }} All rights reserved.</p>
    </div>
</footer> 