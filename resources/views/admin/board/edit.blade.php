@extends('admin.layouts.layout')
@section('admin-content')
  <form action="" method="post">
    <div class="row mb-3">
      <label for="boardIdx" class="col-sm-2 col-form-label">등록번호</label>
      <div class="col-sm-10">
        <input type="text" class="form-control-plaintext" id="boardIdx" name="board_idx" value="{{ $listData['idx'] }}"
               readonly>
      </div>
    </div>
    <div class="row mb-3">
      <label for="userEmail" class="col-sm-2 col-form-label">최초 등록자</label>
      <div class="col-sm-10">
        <input type="email" class="form-control-plaintext" id="userEmail" name="user_email" value="{{ $listData['user_email'] }}"
               readonly>
      </div>
    </div>
    <div class="row mb-3">
      <label for="tableName" class="col-sm-2 col-form-label">테이블 이름</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="tableName" name="table_name" value="{{ $listData['table_name'] }}">
      </div>
    </div>
    <div class="row mb-3">
      <label for="tableBoardTitle" class="col-sm-2 col-form-label">테이블 이름</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="tableBoardTitle" name="table_board_title"
               value="{{ $listData['table_board_title'] }}">
      </div>
    </div>
    <div class="row mb-3">
      <label for="tableCreatedAt" class="col-sm-2 col-form-label">테이블 생성일</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="tableCreatedAt" name="table_created_at"
               value="{{ $listData['table_created_at'] }}">
      </div>
    </div>
    <div class="d-flex justify-content-center">
      <button type="submit" class="btn btn-primary me-2">수정</button>
      <button type="button" class="btn btn-secondary">취소</button>
    </div>
  </form>
@endsection
