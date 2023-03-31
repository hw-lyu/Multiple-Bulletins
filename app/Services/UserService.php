<?php

namespace App\Services;

use App\Repositories\UserRepository;

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

  public function register($data, $createData)
  {
    $validator = Validator::make($data, [
      'name' => 'required|string|max:255',
      'email' => 'required|email|max:255|unique:users|ends_with:@naver.com,@gmail.com',
      'password' => 'required|confirmed',
      'terms_check' => 'required|boolean'
    ])->validate();

    $user = $this->userRepository->create($createData);

    Auth::login($user);
    event(new Registered($user));

    return $user;
  }

}
