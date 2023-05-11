<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

  public function naverLogin()
  {
    $client_id = env('NAVER_CLIENT_ID');
    $redirectURI = urlencode("http://localhost/member/Oauth2C");
    $state = "RAMDOM_STATE";
    $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $redirectURI . "&state=" . $state;

    return redirect()->away($apiURL);
  }

  public function naverCallBack(Response $response)
  {
    $client_id = env('NAVER_CLIENT_ID');
    $client_secret = env('NAVER_CLIENT_SECRET');
    $code = $_GET["code"];;
    $state = $_GET["state"];;
    $redirectURI = urlencode("http://localhost/member/Oauth2C");
    $url = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=" . $client_id . "&client_secret=" . $client_secret . "&redirect_uri=" . $redirectURI . "&code=" . $code . "&state=" . $state;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $res = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_code == 200) {
      $responseArr = json_decode($res, true);
      $encryptedToken = encrypt($responseArr['refresh_token']);

      return $response->withHeaders([
        'Authorization' => $responseArr['token_type'] . ' ' . $responseArr['access_token'],
        'expires' => $responseArr['expires_in']
      ])
        ->withCookie('refresh_token', $encryptedToken, $responseArr['expires_in'], null, null, true, true, false, 'None');

    } else {
      echo "Error 내용:" . $res;
    }
  }
}
