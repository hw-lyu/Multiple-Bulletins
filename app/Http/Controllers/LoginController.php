<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\UserService;

class LoginController extends Controller
{

  protected $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  public function index()
  {
    return redirect()->route('home');
  }

  public function authenticate(Request $request)
  {
    $data = $request->input();

    return $this->userService->login(request: $request, data: $data);
  }

}
