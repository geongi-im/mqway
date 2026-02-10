<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\View\Composers\HeaderComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Tailwind CSS 페이지네이션 사용
        Paginator::defaultView('vendor.pagination.tailwind');
        Paginator::defaultSimpleView('vendor.pagination.simple-tailwind');

        // 운영 환경에서 HTTPS 강제
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // 헤더 메뉴 데이터 → HeaderComposer 클래스로 위임
        View::composer('layouts.partials.header', HeaderComposer::class);
    }
}

