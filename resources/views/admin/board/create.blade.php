@extends('admin.layouts.layout')
@section('admin-content')
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
@endsection
