<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class FindInformationController extends Controller
{

  public function index() {
    return view('auth.find-information');
  }
}
