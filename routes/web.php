<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\UserRegisterController;

use App\Http\Controllers\JoinController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\FindInformationController;

use App\Http\Controllers\BoardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CommentController;

use App\Models\Board;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//메인
Route::get('/', function () {
  $auth = Auth::user();

  // $auth['grade'] - 1: 일반회원, 2: 관리자
  if (!empty($auth['grade']) && $auth['grade'] === 2) {
    $listData = Board::orderBy('idx', 'desc')
      ->paginate(3);
  } else {
    $listData = Board::where('board_state', 'n')
      ->orderBy('idx', 'desc')
      ->paginate(3);
  }

  return view('index', ['auth' => $auth, 'listData' => $listData]);
})->name('home');

//로그인
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login-check', [LoginController::class, 'authenticate'])->name('login.check');
//로그아웃
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

//가입
Route::get('/join', [JoinController::class, 'index'])->name('join');
//가입등록
Route::post('/register', [UserRegisterController::class, 'userRegister'])->name('register');

//이메일 인증 관련
Route::get('/email/verify', function (Request $request) {

  //초깃값
  $userEmailVerifiedState = empty($request->user()->email_verified_at);

  return view('auth.verify-email', ['userEmailVerifiedState' => $userEmailVerifiedState]);
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();

  return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();

  return back()->with('message', '확인 링크를 보냈습니다!<br>이메일을 확인해주세요!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 정보찾기 - 비밀번호 찾기
Route::get('/forgot-password', function () {
  return view('auth.forgot-password');
})->middleware('guest')->name('password.request');
Route::post('/forgot-password', function (Request $request) {
  $request->validate(['email' => 'required|email']);

  $status = Password::sendResetLink(
    $request->only('email')
  );

  return $status === Password::RESET_LINK_SENT
    ? back()->with(['status' => __($status)])
    : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function ($token, Request $request) {

  $email = $request->query('email');

  return view('auth.reset-password', ['token' => $token, 'email' => $email]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {
  $request->validate([
    'token' => 'required',
    'email' => 'required|email',
    'password' => 'required|min:8|confirmed',
  ]);

  $status = Password::reset(
    $request->only('email', 'password', 'password_confirmation', 'token'),
    function ($user, $password) {
      $user->forceFill([
        'password' => Hash::make($password)
      ])->setRememberToken(Str::random(60));

      $user->save();

      event(new PasswordReset($user));
    }
  );

  return $status === Password::PASSWORD_RESET
    ? redirect()->route('login')->with('status', __($status))
    : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

// --- (완성) 비로그인시 - 글쓰기, 파일업로드 --- auth 미들웨어 추가 -> 라우터 내에 초깃값으로 미들웨어 only 추가
// --- (완성) 뷰 템플릿 조각화(o), 글리스트 (o), 글수정 (p) , 글삭제(게시글에 board_state 추가) 페이지 추가 (o),
// --- (완성) 일반 유저에겐 안보이게 할것(등급 추가 해야할듯) / 운영자는 보이게 변경
// --- (보류) 컨트롤러 모델 쿼리 불러오는 부분 --> 모델에다 쿼리 불러오고 컨트롤러서 메서드만 호출하는 식으로 변경


// 0. 게시글 - 레디스 처리 -- 보류
// 캐시 및 레디스로 -> 해당 게시글의 조회수 저장
// 저장된 조회수를 30분 간격으로 데이터베이스에 업데이트
// 브라우저에서는 캐시 및 레디스로 계속 쓰고 데이터베이스에 저장
// 레디스가 안되면 데이터베이스 값을 쓴다
//---
// 페이지 들어왔을 시 레디스 post + idx key name으로 쓰고 1씩 추가
// 30분마다 조회수 디비에 업데이트 후에 레디스 키 삭제
// 다시 리셋되서 페이지 들어올 때 마다 조회수 보이기
// 뷰 페이지에는 현재 디비 조회수값 + 레디스 키값으로 뿌려주기

// ===
// --- 지금 할것 리스트업
// 0. 좋아요, 조회수 처리(o) -- 일단 디비 업데이트로 처리
// 1. 게시글 쓰는 것
// ㄴ 권한 없을때 권한이 없습니다 페이지 만들어 미들웨어 추가해서 로그인 화면 보여주게 하기
// 2. 게시글 삭제 데이터
// ㄴ 일반 유저에겐 삭제된 게시물 접근 금지 미들웨어 생성
// ㄴ 글 삭제시 이미지 데이터도 디비에서 삭제하기(?)
// 3. 코멘트 작업
// 4. 정보찾기 작업
// 5. 인증메일 재발송 처리 -- 비회원일때 인증메일 처리 안됨 / 인증메일 처리완료시 인증메일 재발송 안보이게 하기
// 6. 다중게시판 / 관리자 페이지 작업

Route::resources([
  'boards' => BoardController::class,
  'upload' => UploadController::class,
  'comments' => CommentController::class,
]);

Route::post('boards/like/{idx}', [BoardController::class, 'like'])->name('boards.like');
Route::post('comments/{idx}/{offset}', [CommentController::class, 'list'])->name('comments.list');
