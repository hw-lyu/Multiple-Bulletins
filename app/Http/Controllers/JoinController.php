<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class JoinController extends Controller
{
  public function index()
  {
    return view('member.join');
  }
}
