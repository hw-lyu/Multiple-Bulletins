@extends('admin.layouts.layout')
@section('admin-content')
  <form action="{{ route('admin.board.update', ['boardIdx' => $listData['idx']]) }}" method="post">
    @csrf
    @method('patch')
    <input type="hidden" name="old_table_name" value="{{ $listData['table_name'] }}">
    <div class="row mb-3">
      <label for="boardIdx" class="col-sm-2 col-form-label">등록번호</label>
      <div class="col-sm-10">
        <input type="text" class="form-control-plaintext" id="boardIdx" name="board_idx" value="{{ $listData['idx'] }}"
               readonly>
      </div>
    </div>
    <div class="row mb-3">
      <label for="userEmail" class="col-sm-2 col-form-label">등록자</label>
      <div class="col-sm-10">
        <input type="email" class="form-control-plaintext" id="userEmail" name="user_email"
               value="{{ $listData['user_email'] }}"
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
      <label for="tableBoardTitle" class="col-sm-2 col-form-label">게시판 이름</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="tableBoardTitle" name="table_board_title"
               value="{{ $listData['table_board_title'] }}">
      </div>
    </div>
    <div class="row mb-3">
      <label for="tableCreatedAt" class="col-sm-2 col-form-label">테이블 생성일</label>
      <div class="col-sm-10">
        <input type="text" class="form-control-plaintext" id="tableCreatedAt" name="table_created_at"
               value="{{ $listData['table_created_at'] }}">
      </div>
    </div>
    @if(!empty($listData['table_updated_at']))
      <div class="row mb-3">
        <label for="tableUpdatedAt" class="col-sm-2 col-form-label">테이블 수정일</label>
        <div class="col-sm-10">
          <input type="text" class="form-control-plaintext" id="tableUpdatedAt" name="table_updated_at"
                 value="{{ $listData['table_updated_at'] }}">
        </div>
      </div>
    @endif
    <div class="d-flex justify-content-center">
      <button type="submit" class="btn btn-primary me-2">수정</button>
      <button type="button" class="btn btn-secondary" onclick="history.back();">취소</button>
    </div>
  </form>
  <hr class="mt-3">
  <div class="mt-3">
    <p class="mb-2">--- 수정로그 입니다 ---</p>
    <div class="log-list" style="height: 200px; overflow: hidden; overflow-y: auto;">
      @foreach($logData as $data)
        <p class="lh-sm mb-1">[{{ $data['table_created_at'] }}] {{ $data['user_email'] }}님께서 테이블 이름
          : {{ $data['table_name'] }} / 게시판 제목 : {{ $data['table_board_title'] }} 내용을 등록하셨습니다.</p>
      @endforeach
    </div>
  </div>
@endsection
