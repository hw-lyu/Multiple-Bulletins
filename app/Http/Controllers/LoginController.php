<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

  public function index()
  {
    return redirect()->route('home');
  }

  public function authenticate(Request $request)
  {
    //초깃값
    $remeberMe = $request->boolean('remember_me');

    $credentials = $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);

    if (Auth::attempt( ['email' => $credentials['email'], 'password' => $credentials['password']], $remeberMe )) {
      $request->session()->regenerate();

      return redirect()->intended('/');
    }

    return back()->withErrors([
      'email' => '제공된 자격 증명이 기록과 일치하지 않습니다.',
    ]);
  }
}
