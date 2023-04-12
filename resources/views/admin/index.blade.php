@extends('layouts.layout')
@section('title', 'Admin')
@section('content')
  <section class="admin-wrap">
    <h3 class="visually-hidden">어드민 툴</h3>
    <div class="d-flex justify-content-between inner">
      <nav class="left-side-bar col-2">
        <ul class="list text-center mb-0 pl-0 ps-0">
          <li><a href="{{ route('admin.board') }}">메뉴 추가하기</a></li>
        </ul>
      </nav>
      <div class="content col-10">
        <form action="{{ route('admin.board.store') }}" method="post">
          @csrf
          <div class="mb-3">
            <label for="boardUrl" class="form-label">게시판 주소</label>
            <input type="text" class="form-control" id="boardUrl" name="board_url"
                   placeholder="/board/{boardTableName}">
            <div class="info mt-1">
              - 게시판 주소는 영어 소문자와 숫자만 등록 가능합니다.<br>
              - 게시판 주소는 <strong>{boardTableName}</strong>에 해당합니다<br>
              - 게시판 주소는 중복이 불가합니다.
            </div>
          </div>
          <div class="mb-3">
            <label for="boardTitle" class="form-label">게시판 제목</label>
            <input type="text" class="form-control" id="boardTitle" name="board_title" placeholder="게시판명">
          </div>
          <button class="btn btn-primary" type="submit">게시판 추가</button>
        </form>
      </div>
    </div>
  </section>
@endsection
