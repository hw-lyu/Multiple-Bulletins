@foreach($commentData['data'] as $comment)
  <div
    class="comment{{$comment['idx'] !== $comment['parent_idx'] ? ' reply' : ''}}{{$comment['comment_state'] === 'y' && $grade === 2 ? ' text-bg-danger' : ''}}"
    data-comment-idx="{{ $comment['idx'] }}">
    <div class="comment-content">
      <div class="info">
        {{ $comment['comment_writer'] }}(작성일 {{ $comment['comment_created_at'] }})
        {{ !empty($comment['comment_deleted_at']) ? "(삭제일". $comment['comment_deleted_at'] .")" : '' }}
      </div>
      @if($comment['comment_state'] === 'y' && $grade === 2)
        <div class="content">{{ $comment['comment_content'] }}</div>
      @elseif($comment['comment_state'] === 'n')
        <div class="content">{{ $comment['comment_content'] }}</div>
      @else
        <div class="contnet">삭제된 코멘트 입니다.</div>
      @endif
    </div>
    @if($comment['comment_state'] === 'n')
      @auth
        <div class="btn-wrap">
          @if($comment['comment_writer'] === Illuminate\Support\Facades\Auth::user()['email'])
            <button type="button" class="btn-comment-edit">수정</button>
            <button type="button" class="btn-comment-remove">삭제</button>
          @else
            <button type="button" class="btn-comment-answer">답변하기</button>
          @endif
        </div>
      @endauth
    @endif
  </div>
  <hr style="border:1px solid darkseagreen;">
@endforeach
