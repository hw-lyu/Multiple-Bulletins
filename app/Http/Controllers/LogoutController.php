<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\UserService;

class LogoutController extends Controller
{

  protected UserService $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  public function logout(Request $request)
  {
    $this->userService->logout(request: $request);

    return redirect('/');
  }

}
