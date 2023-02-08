<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Pagination\Paginator;

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
    VerifyEmail::toMailUsing(function ($notifiable, $url) {
      return (new MailMessage)
        ->from(env('MAIL_USERNAME'), '커뮤니티 주인')
        ->subject('이메일 주소 확인 메일 입니다.')
        ->line('이메일 주소를 확인하려면 아래 버튼을 클릭하십시오.')
        ->action('이메일 주소 확인', $url);
    });

    Paginator::useBootstrap();
  }
}
