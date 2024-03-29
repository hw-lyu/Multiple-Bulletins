<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserRegisterController;

use App\Http\Controllers\JoinController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminBoardController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PasswordController;

use App\Http\Controllers\PolicyController;

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

// 메인
Route::get('/', [BoardController::class, 'index'])->name('home');

// 로그인
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login-check', [LoginController::class, 'authenticate'])->name('login.check');

// 로그아웃
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// naver-login | naver-logout
Route::get('/naverLogin', [LoginController::class, 'naverLogin'])->name('login.naver');
Route::get('/member/Oauth2C', [LoginController::class, 'naverCallBack'])->name('login.naver.callback');

// 가입
Route::get('/join', [JoinController::class, 'index'])->name('join');

// 가입등록
Route::post('/register', [UserRegisterController::class, 'userRegister'])->name('register');

// 이메일 인증 관련
Route::get('/email/verify', [EmailController::class, 'notice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [EmailController::class, 'send'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 정보찾기 - 비밀번호 찾기
Route::get('/forgot-password', [PasswordController::class, 'index'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'emailSend'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'reset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'update'])->middleware('guest')->name('password.update');

// Policy - 이용약관 : service / 개인정보처리방침 : privacy
Route::get('/privacy', [PolicyController::class, 'privacy'])->name('policy.privacy');
Route::get('/service', [PolicyController::class, 'service'])->name('policy.service');

// 게시판
Route::get('/board/{tableName}', [BoardController::class, 'index'])->name('board.index');
Route::get('/board/{tableName}/create', [BoardController::class, 'create'])->name('board.create');
Route::post('/board/{tableName}', [BoardController::class, 'store'])->name('board.store');
Route::get('/board/{tableName}/{idx}', [BoardController::class, 'show'])->name('board.show');
Route::get('/board/{tableName}/{idx}/edit', [BoardController::class, 'edit'])->name('board.edit');
Route::patch('/board/{tableName}/{idx}', [BoardController::class, 'update'])->name('board.update');
Route::delete('/board/{tableName}/{idx}', [BoardController::class, 'destroy'])->name('board.destroy');
Route::post('/board/like/{tableName}/{idx}', [BoardController::class, 'like'])->name('board.like');

// 코멘트
Route::post('/comments/{tableName}/{idx}/{offset}', [CommentController::class, 'list'])->name('comments.list');
Route::post('/comments/{tableName}', [CommentController::class, 'store'])->name('comments.store');
Route::get('/comments/{tableName}/{idx}/edit', [CommentController::class, 'edit'])->name('comments.edit');
Route::patch('/comments/{tableName}/{idx}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{tableName}/{idx}', [CommentController::class, 'destroy'])->name('comments.destroy');

// 파일 업로드
Route::post('/upload/{tableName}', [UploadController::class, 'store'])->name('upload.store');

// 어드민툴
Route::get('/admin', [AdminController::class, 'index'])->middleware('auth')->name('admin');
Route::get('/admin/board', [AdminBoardController::class, 'index'])->middleware('auth')->name('admin.board');
Route::get('/admin/board/create', [AdminBoardController::class, 'create'])->middleware('auth')->name('admin.board.create');
Route::post('/admin/board/store', [AdminBoardController::class, 'store'])->middleware('auth')->name('admin.board.store');
Route::get('/admin/board/{boardIdx}/edit', [AdminBoardController::class, 'edit'])->middleware('auth')->name('admin.board.edit');
Route::put('/admin/board/{boardIdx}', [AdminBoardController::class, 'update'])->middleware('auth')->name('admin.board.update');
Route::delete('/admin/board/{boardIdx}', [AdminBoardController::class, 'destroy'])->middleware('auth')->name('admin.board.destroy');
