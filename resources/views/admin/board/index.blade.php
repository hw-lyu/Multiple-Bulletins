@extends('admin.layouts.layout')
@section('admin-content')
  <table class="table table-sm">
    <thead>
    <tr>
      <th>No</th>
      <th>테이블 이름</th>
      <th>게시판 이름</th>
      <th>카테고리</th>
      <th>생성일</th>
      <th>수정 / 상태 변경</th>
    </tr>
    </thead>
    <tbody>
    @foreach($listData as $data)
      <tr>
        <td>{{ $data['idx'] }}</td>
        <td>{{ $data['table_name'] }}</td>
        <td>{{ $data['table_board_title'] }}</td>
        <td>{{ $data['board_cate'] }}</td>
        <td>{{ $data['table_created_at'] }}</td>
        <td>
          <div class="btn-group" role="group" aria-label="수정/삭제 그룹">
            <a href="{{ route('admin.board.edit', ['boardIdx' => $data['idx']]) }}" class="btn btn-sm btn-primary"
               role="button" aria-disabled="true">수정</a>
            <form action="{{ route('admin.board.destroy', ['boardIdx' => $data['idx']]) }}" method="post">
              @csrf
              @method('delete')
              <button type="submit" class="btn btn-sm btn-danger"
                      style="border-top-left-radius: 0; border-bottom-left-radius: 0;">{{ $data['board_state'] === 'n' ? '비공개' : '공개' }}</button>
            </form>
          </div>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <div class="pagination-wrap">
    {{ $listData->links() }}
  </div>
@endsection
