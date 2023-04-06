<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\UserService;

use Exception;

class LoginController extends Controller
{

  protected UserService $userService;

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

    try {
      $result = $this->userService->login(request: $request, data: $data);

      if (gettype($result) === 'array' && !empty($result['email'])) {
        throw new Exception($result['email']);
      }
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }

}
