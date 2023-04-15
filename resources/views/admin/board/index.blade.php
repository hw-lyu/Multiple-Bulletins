@extends('admin.layouts.layout')
@section('admin-content')
  <table class="table table-sm">
    <thead>
    <tr>
      <th>No</th>
      <th>테이블 이름</th>
      <th>게시판 이름</th>
      <th>생성일</th>
      <th>수정/삭제</th>
    </tr>
    </thead>
    <tbody>
    @foreach($listData as $data)
      <tr>
        <td>{{ $data['idx'] }}</td>
        <td>{{ $data['table_name'] }}</td>
        <td>{{ $data['table_board_title'] }}</td>
        <td>{{ $data['table_created_at'] }}</td>
        <td>
          <div class="btn-group" role="group" aria-label="수정/삭제 그룹">
            <a href="{{ route('admin.board.edit', ['boardIdx' => $data['idx']]) }}" class="btn btn-sm btn-primary" role="button" aria-disabled="true">수정</a>
            <a href="#" class="btn btn-sm btn-danger" role="button" aria-disabled="true">삭제</a>
          </div>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
@endsection
