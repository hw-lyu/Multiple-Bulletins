<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;

class UserRegisterController extends Controller
{

  protected $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  public function userRegister(Request $request)
  {
    $data = $request->input();
    $createData = [
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ];

    $this->userService->register($data, $createData);

    return redirect()->route('home');
  }

}
