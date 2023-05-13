<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Requests\PasswordEmailSend;
use App\Http\Requests\PasswordUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
  public function index()
  {
    return view('auth.forgot-password');
  }

  public function emailSend(Request $request, PasswordEmailSend $passwordEmailSend)
  {
    $passwordEmailSend->validated();

    $status = Password::sendResetLink(
      $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
      ? back()->with(['status' => __($status)])
      : back()->withErrors(['email' => __($status)]);
  }

  public function reset($token, Request $request)
  {
    $email = $request->query('email');

    return view('auth.reset-password', ['token' => $token, 'email' => $email]);
  }

  public function update(Request $request, PasswordUpdate $passwordUpdate)
  {
    $passwordUpdate->validated();

    $status = Password::reset(
      $request->only('email', 'password', 'password_confirmation', 'token'),
      function ($user, $password) {
        $user->forceFill([
          'password' => Hash::make($password)
        ])->setRememberToken(Str::random(60));

        $user->save();

        event(new PasswordReset($user));
      }
    );

    return $status === Password::PASSWORD_RESET
      ? redirect()->route('login')->with('status', __($status))
      : back()->withErrors(['email' => [__($status)]]);
  }
}
