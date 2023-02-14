@extends('layouts.layout')
@section('title', '정보찾기')
@section('content')
  <section class="auth-find-wrap inner">
    <h3 class="title">정보찾기</h3>
    <hr>
    <form action="" method="post">
      <div class="find-pw">
        <p class="info">
          회원 가입시 등록하신 이메일 주소를 입력해주세요.<br>
          이메일 주소를 통해서 비밀번호 재설정 링크를 드립니다.
        </p>
        <div class="email d-flex flex-column">
          <label class="mb-1">이메일 주소 <input type="text" class="form-control" name="email" required></label>
          <button type="submit" class="btn btn-link">확인</button>
        </div>
      </div>
    </form>
  </section>
@endsection
