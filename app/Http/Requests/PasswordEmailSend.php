<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordEmailSend extends FormRequest
{
  /**
   * Http/Controllers/PasswordController.php | emailSend() rules
   * @return string[]
   */
  public function rules(): array
  {
    return ['email' => 'required|email'];
  }
}
