<?php

namespace App\Services;

use App\Repositories\UserRepository;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Illuminate\Auth\Events\Registered;

class UserService
{

  protected $userRepository;

  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }

  public function register(array $data, array $createData)
  {
    $validator = Validator::make($data, [
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:users|ends_with:@naver.com,@gmail.com',
      'password' => 'required|confirmed',
      'terms_check' => 'required|boolean'
    ])->validate();

    $user = $this->userRepository->create(data: $createData);

    Auth::login(user: $user);
    event(new Registered(user: $user));

    return redirect()->route('home');
  }

  public function login(Request $request, array $data)
  {
    $remeberMe = $request->boolean('remember_me');
    $credentials = Validator::make($data, [
      'email' => 'required|email',
      'password' => 'required',
    ])->validate();

    if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remeberMe)) {
      $request->session()->regenerate();

      return redirect()->intended('/');
    }

    return back()->withErrors([
      'email' => '제공된 자격 증명이 기록과 일치하지 않습니다.',
    ]);
  }

  public function logout(Request $request)
  {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
  }

}
