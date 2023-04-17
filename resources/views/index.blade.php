@extends('layouts.layout')
@section('title', '메인')
@section('content')
  <div class="inner">
    <ul class="board-list">
      @foreach($boardTableListData as $boardTableList)
        <li><a
            href="{{ route('board.index', str_replace('_', '/', $boardTableList['table_name'])) }}"
            class="link {{ $boardTableList['board_state'] === 'y' ? 'text-secondary fst-italic' : '' }}">{{ ($boardTableList['board_state'] === 'y'? '(비공개)' : '').$boardTableList['table_board_title'] }}</a>
        </li>
      @endforeach
    </ul>
    @if(!empty($auth))
      <div class="btn-wrap text-end mb-1">
        <a href="{{ route('board.create', ['tableName' => $tableName]) }}" class="link">글쓰기</a>
      </div>
    @endif
    @if( !empty($listData) )
      <table class="table table-hover mb-4">
        <colgroup>
          <col style="width:5%;">
          <col style="width:8%;">
          <col style="width:54%;">
          <col style="width:10%;">
          <col style="width:10%;">
          <col style="width:8%;">
          <col style="width:5%;">
        </colgroup>
        <thead>
        <tr>
          <th>번호</th>
          <th>분류</th>
          <th class="title">제목</th>
          <th>글쓴이</th>
          <th>날짜</th>
          <th>조회수</th>
          <th>추천</th>
        </tr>
        </thead>
        <tbody>
        @if(count($listData))
          @foreach($listData as $data)
            <tr class="{{ $data['board_state'] === 'y' ? 'text-bg-danger' : '' }}">
              <td>{{ $data['idx'] }}</td>
              <td>{{ $data['board_cate'] }}</td>
              <td class="title">
                <div class="title-box">
                  <a
                    href="{{ route('board.show', ['idx' => $data['idx'], 'tableName' => $tableName]) }}">{{ $data['board_title'] }}</a>
                  <a
                    href="{{ route('board.show', ['idx' => $data['idx'], 'tableName' => $tableName, 'comment_view' => 1]) }}"
                    class="all-comment">[{{ $data['all_comment'] }}]</a>
                  {!! $data['photo_state'] === 'y' ? '<div class="file"><span class="visually-hidden">파일</span><i class="bi bi-images"></i>
</span>' : '' !!}
                </div>
              </td>
              <td>{{ $data['user_email'] }}</td>
              <td>{{ $data['view_created_at'] }}</td>
              <td>{{ $data['views'] }}</td>
              <td class={{ $data['view_like'] !== 0 ? "view-like" : '' }}>{{ $data['view_like'] }}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="7">글이 없습니다.</td>
          </tr>
        @endif
        </tbody>
      </table>

      <div class="pagination-wrap">
        {{ $listData->onEachSide(0)->links() }}
      </div>
    @endif
  </div>
@endsection
