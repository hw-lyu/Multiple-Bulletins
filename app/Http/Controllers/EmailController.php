<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailController extends Controller
{
  public function notice(Request $request)
  {
    //초깃값
    $userEmailVerifiedState = empty($request->user()->email_verified_at);

    return view('auth.verify-email', ['userEmailVerifiedState' => $userEmailVerifiedState]);
  }

  public function verify(EmailVerificationRequest $request)
  {
    $request->fulfill();

    return redirect('/');
  }

  public function send(Request $request)
  {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '확인 링크를 보냈습니다!<br>이메일을 확인해주세요!');
  }
}
