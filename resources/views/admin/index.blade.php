@extends('layouts.layout')
@section('title', 'Admin')
@section('content')
  <section class="admin-wrap">
    <h3 class="visually-hidden">어드민 툴</h3>
    <div class="d-flex justify-content-between inner">
      <nav class="left-side-bar col-2">
        <ul class="list text-center mb-0 pl-0 ps-0">
          <li><a href="">게시판 추가하기</a></li>
        </ul>
      </nav>
      <div class="content col-10">
        어드민 페이지 본문 내용
      </div>
    </div>
  </section>
@endsection
