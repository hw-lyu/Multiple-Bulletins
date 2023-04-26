<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PolicyController extends Controller
{
  function privacy()
  {
    return view('policy.privacy');
  }

  public function service()
  {
    return view('policy.service');
  }
}
