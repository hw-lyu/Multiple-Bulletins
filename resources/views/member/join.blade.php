@extends('layouts.layout')
@section('title', '가입')
@section('content')
  <form class="mt-3 inner" method="post" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
      <label for="exampleFormControlInput0" class="visually-hidden">이름</label>
      <input type="text" class="form-control" id="exampleFormControlInput0" name="name" placeholder="name"
             value="{{ old('name') }}">
    </div>
    <div class="mb-3">
      <label for="exampleFormControlInput1" class="visually-hidden">Email address</label>
      <input type="email" class="form-control" id="exampleFormControlInput1" name="email" placeholder="name@example.com"
             value="{{ old('email') }}">
    </div>
    <div>
      <label for="inputPassword2" class="visually-hidden">Password</label>
      <input type="password" class="form-control" id="inputPassword2" name="password" placeholder="password">
    </div>
    <div class="form-check mt-1">
      <input class="form-check-input" type="checkbox" id="termsCheck" name="terms_check" value="1">
      <label class="form-check-label" for="termsCheck">
        다음을 확인하였으며, 이에 동의합니다
      </label>
      <a href="">이용약관</a>
      <span> &amp; </span>
      <a href="">개인정보처리방침</a>
    </div>
    <div class="row mt-3">
      <button type="submit" class="btn btn-primary mb-3">인증메일 발송</button>
    </div>
  </form>
@endsection
