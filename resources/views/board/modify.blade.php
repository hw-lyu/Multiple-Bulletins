@extends('layouts.layout')
@section('title', '글 수정')
@section('content')
  <div class="inner board-detail-wrap">
    <h3 class="board-title">게시판 제목</h3>
    <div class="btn-wrap mb-3">
      <a href="{{ route('home') }}" class="link">리스트</a>
    </div>
    @if(!empty($boardDetail))
      <form action="{{ route('board.update', ['idx' => $idx, 'tableName' => $tableName]) }}" method="post"
            class="form-board-write"
            onsubmit="return false;">
        @csrf
        @method('patch')
        <input type="hidden" name="photo_state" value="{{ $boardDetail['photo_state'] }}">
        <input type="hidden" name="url"
               value="{{ route('upload.store', ['tableName' => $tableName]).'?_token='.csrf_token() }}">
        <div class="head">
          <input type="text" class="form-control board-title mb-1" name="board_title" placeholder="글제목"
                 value="{{ $boardDetail['board_title'] }}">
          <select class="form-select board-cate" aria-label="Default select" name="board_cate">
            <option value="분류" {{ old('board_cate') === '분류' ? 'selected' : ''  }}>분류</option>
            @foreach($cateList as $cate)
              <option
                value="{{ $cate }}" {{ (old('board_cate') === $cate || $boardDetail['board_cate'] === $cate) ? 'selected' : ''  }}>{{ $cate }}</option>
            @endforeach
          </select>
        </div>
        <div class="content mt-3">
          <textarea name="board_content" id="editor">{{ $boardDetail['board_content'] }}</textarea>
          <div class="btn-wrap text-center mt-3">
            <button type="submit" class="btn btn-link btn-add">글등록</button>
            <button type="button" class="btn btn-link" onclick="window.history.back();">취소</button>
          </div>
        </div>
      </form>
    @endif
  </div>

  @push('scripts')
    <script src="{{ asset('lib/ckeditor.js') }}"></script>
    <script src="{{ asset('lib/ckeditorCustomUpload.js') }}"></script>
  @endpush
@endsection
