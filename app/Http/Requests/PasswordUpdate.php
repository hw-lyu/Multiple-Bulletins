<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordUpdate extends FormRequest
{
  /**
   * Http/Controllers/PasswordController.php | update() rules
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|min:8|confirmed',
    ];
  }
}
