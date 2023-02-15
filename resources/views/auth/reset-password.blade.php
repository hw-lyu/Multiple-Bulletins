@extends('layouts.layout')
@section('title', '정보찾기')
@section('content')
  <section class="auth-find-wrap inner">
    <h3 class="title">정보찾기</h3>
    <hr>
    <form action="{{ route('password.update') }}" method="post">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="find-pw">
        <p class="info">
          회원 가입시 등록하신 이메일 주소를 입력해주세요.<br>
          패스워드는 재설정 부탁드립니다.
        </p>
        <div class="d-flex flex-column">
          <label class="mb-1">이메일 주소 <input type="text" class="form-control" name="email" value="{{ $email }}" readonly></label>
        </div>
        <div class="d-flex flex-column">
          <label class="mb-1">패스워드 <input type="password" class="form-control" name="password" required=""></label>
        </div>
        <div class="d-flex flex-column">
          <label class="mb-1">패스워드 확인<input type="password" class="form-control" name="password_confirmation" required=""></label>
          <button type="submit" class="btn btn-link">확인</button>
        </div>
      </div>
    </form>
  </section>
@endsection
