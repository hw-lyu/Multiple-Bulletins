@extends('layouts.layout')
@section('title', '글 보기')
@section('content')
  <div class="inner board-detail-wrap">
    @if(!empty($boardDetail))
      @if($boardDetail['board_state'] === 'y')
        <div class="text-bg-danger">현재 글은 삭제된 글입니다.</div>
      @endif
      <h3 class="board-title">{{ $boardTitle }}</h3>
      <div class="head">
        <div class="list-util-wrap">
          <a href="{{ route('board.index', ['tableName' => $tableName]) }}">리스트</a>
          @if($boardDetail['photo_state'] === 'Y')
            <div class="file-icon">파일첨부</div>
          @endif
          @if(!empty($boardDetailAuth))
            @csrf
            <a href="{{ route('board.edit', ['idx' => $idx, 'tableName' => $tableName]) }}"
               class="link">수정</a>
            <button type="button" class="btn btn-link btn-remove">삭제</button>
          @else
            @auth
              <button type="button" class="btn btn-link btn-like">좋아요</button>
            @endauth
          @endif
        </div>
        <hr>
        <div class="title-box">
          <div class="cate">{{ $boardDetail['board_cate'] }}</div>
          <div class="title">{{ $boardDetail['board_title'] }}</div>
        </div>
        <div class="article-info">
          <div class="info">
            <div class="writer">작성자 <span class="name">{{ $boardDetail['user_email'] }}</span></div>
            <p class="time mb-0">작성일
              <time datetime="{{ $boardDetail['view_created_at'] }}">{{ $boardDetail['view_created_at'] }}</time>
            </p>
            @if( $boardUpdatedDateState )
              <p class="time mb-0">글 수정일
                <time datetime="{{ $boardDetail['view_updated_at'] }}">{{ $boardDetail['view_updated_at'] }}</time>
              </p>
            @endif
          </div>
          <div class="info">
            <div class="views">조회{{ $boardDetail['views'] }}회</div>
            <div class="view-like">추천수<span class="num">{{ $boardDetail['view_like'] }}</span>번</div>
            <div class="all-comment">댓글{{ $boardDetail['all_comment'] }}건</div>
          </div>
        </div>
      </div>
      <hr>
      <div class="content board-content mt-5 mb-5{{ !empty($commentView) ? ' d-none' : '' }}">
        {!! $boardDetail['board_content'] !!}
      </div>
      @if(!empty($commentView))
        <button type="button" class="btn btn-primary d-block w-100 btn-comment-view">본문내용 보이기</button>
      @endif
    @else
      <div class="info">해당 글을 찾을 수 없습니다.</div>
    @endif
    <hr>
    <div class="comment-wrap">
      <div class="all-comment">총 {{ $commentData['total'] }}개의 댓글</div>
      @if(!empty($commentData['ceil']))
        <div class="d-grid gap-2 comment-btn-wrap mt-2">
          @for($i = 0; $i < $commentData['ceil']; $i++)
            <button class="btn btn-primary {{ ($i + 1) === $commentData['ceil'] ? 'disabled' : '' }}"
                    type="button"
                    data-offset="{{ $i * 100 }}">
              <strong>{{ ( $i !== 0 ? $i * 100 + 1 : $i ) . ' ~ ' . ( ($i + 1) === $commentData['ceil'] ? '' : ($i + 1) * 100 ) }}</strong>
              번째 댓글
            </button>
          @endfor
        </div>
      @endif
      <hr>
      @auth
        <div class="comment-write">
          <div class="name mb-1">{{ Illuminate\Support\Facades\Auth::user()['email'] }} 님</div>
          <form action="{{ route('comments.store', ['tableName' => $tableName]) }}" method="post">
            @csrf
            <input type="hidden" name="comment_view" value="{{ $commentView }}">
            <input type="hidden" name="board_idx" value="{{ $idx }}">
            <input type="hidden" name="board_url" value="{{ $boardUrl }}">
            <textarea name="comment_content" cols="30" rows="10"></textarea>
            <button type="submit" class="btn btn-link btn-comment-add">등록</button>
          </form>
        </div>
      @endauth
    </div>
  </div>

  @push('scripts')
    <script>
      let btnRemove = document.querySelector('.head .btn-remove'),
        btnLike = document.querySelector('.head .btn-like'),
        commentWrap = document.querySelector('.comment-wrap'),
        commentBtnWrap = document.querySelector('.comment-btn-wrap'),
        commentViewBtn = document.querySelector('.btn-comment-view');

      // 글 삭제
      if (btnRemove !== null) {
        btnRemove.addEventListener('click', () => {
          let con = confirm('삭제 하시곘습니까?');

          if (con) {
            fetch('{{ route('board.destroy', ['idx' => $idx, 'tableName' => $tableName]) }}', {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
              },
            })
              .then(() => {
                location.href = '{{ route('home') }}';
              })
              .catch((error) => {
                console.error('Error:', error);
              });
          }
        })
      }

      // 글 좋아요
      if (btnLike !== null) {
        btnLike.addEventListener('click', () => {
          let con = confirm('글 추천을 누르시겠습니까?');

          if (con) {
            fetch('{{ route('board.like', ['idx' => $idx, 'tableName' => $tableName]) }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
              },
            })
              .then((response) => {
                return response.json();
              })
              .then((data) => {
                if (data.error) {
                  return alert(data.error);
                }
                document.querySelector('.view-like .num').innerText = data.view_like;
                alert('추천되었습니다.');
              })
              .catch((error) => {
                console.error('Error:', error);
              });
          }
        })
      }

      // 코멘트
      commentWrap.addEventListener('click', function (evt) {
        let evtTarget = evt.target,
          commentBox = evtTarget.closest('.comment'),
          evtTargetClassListArr = [...evtTarget.classList];

        // 코멘트 등록
        if (evtTargetClassListArr.includes('btn-comment-answer')) {
          let divEle = document.createElement('div');

          if (commentBox.querySelectorAll('.comment-recomment').length) {
            return;
          }

          divEle.className = 'comment-recomment';
          divEle.innerHTML = `
    <form action="{{ route('comments.store', ['tableName' => $tableName]) }}" method="post">
      <input type="hidden" name="_token" value={{ csrf_token() }}>
      <input type="hidden" name="board_idx" value="{{ $idx }}">
      <input type="hidden" name="board_url" value="{{ $boardUrl }}">
      <input type="hidden" name="comment_idx" value="${commentBox.dataset.commentIdx}">
      <input type="hidden" name="group_idx" value="${commentBox.dataset.groupIdx}">
      <input type="hidden" name="group_order" value="${commentBox.dataset.groupOrder}">
      <textarea name="comment_content" id="" cols="30" rows="10"></textarea>
      <button type="submit" class="btn btn-link btn-comment-add">등록</button>
      <button type="button" class="btn btn-link btn-comment-close">닫기</button>
    </form>
  `;
          commentBox.appendChild(divEle);

          let btnCommentClose = commentBox.querySelector('.btn-comment-close');

          if (btnCommentClose !== null) {
            btnCommentClose.addEventListener('click', () => {
              commentBox.querySelector('.comment-recomment').remove();
            });
          }
        }

        // 코멘트 수정시 수정버튼
        if (evtTargetClassListArr.includes('btn-comment-edit')) {
          let commentRoute = '{{ route('comments.edit', ['idx' => ':idx', 'tableName' => $tableName]) }}',
            commentIdx = commentBox.dataset.commentIdx;

          commentRoute = commentRoute.replace(':idx', commentIdx);

          fetch(commentRoute, {
            method: 'GET',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
          })
            .then((response) => {
              return response.json();
            })
            .then((data) => {
              if (data.error) {
                return alert(data.error);
              }

              commentBox.querySelector('.comment-content .content').innerHTML = `<textarea name="comment_content" id="" cols="30" rows="10">${data.comment_content}</textarea>`;
              commentBox.querySelector('.list-util-wrap').innerHTML = `<button type="button" class="btn btn-link btn-comment-update">등록</button> <button type="button" class="btn btn-link btn-comment-cancel">취소</button>`;

              commentBox.querySelector('.btn-comment-cancel').addEventListener('click', () => {
                commentBox.querySelector('.comment-content .content').innerHTML = `${data.comment_content}`;
                commentBox.querySelector('.list-util-wrap').innerHTML = `<button type="button" class="btn btn-link btn-comment-edit">수정</button> <button type="button" class="btn btn-link btn-comment-remove">삭제</button>`;
              });
            })
            .catch((error) => {
              console.error('Error:', error);
            });
        }

        // 코멘트 수정 시 등록 버튼 - 업데이트
        if (evtTargetClassListArr.includes('btn-comment-update')) {
          let commentRoute = '{{ route('comments.update', ['idx' => ':idx', 'tableName' => $tableName]) }}',
            commentIdx = commentBox.dataset.commentIdx,
            data = new FormData();

          commentRoute = commentRoute.replace(':idx', commentIdx);

          data.append("_method", 'PATCH');
          data.append("comment_content", commentBox.querySelector('textarea[name="comment_content"]').value.trim());

          fetch(commentRoute, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: data
          })
            .then((response) => {
              return response.json();
            })
            .then((data) => {
              if (data.error) {
                return alert(data.error);
              }

              commentBox.querySelector('.comment-content .content').innerHTML = `${data.comment_content}`;
              commentBox.querySelector('.list-util-wrap').innerHTML = `<button type="button" class="btn btn-link btn-comment-edit">수정</button> <button type="button" class="btn btn-link btn-comment-remove">삭제</button>`;

              if (commentBox.querySelector('.info .updated_at')) {
                commentBox.querySelector('.info .updated_at').innerHTML = ` (수정일 ${data.comment_updated_at})`;
              } else {
                let spanEle = document.createElement('span');

                commentBox.querySelector('.info').appendChild(spanEle);

                spanEle.className = 'updated_at';
                spanEle.innerHTML = ` (수정일 ${data.comment_updated_at})`;
              }
            })
            .catch((error) => {
              console.error('Error:', error);
            });
        }

        // 코멘트 삭제
        if (evtTargetClassListArr.includes('btn-comment-remove')) {
          let route = '{{ route('comments.destroy', ['idx' => ':comment', 'tableName' => $tableName]) }}';

          route = route.replace(':comment', commentBox.dataset.commentIdx);

          fetch(route, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
          })
            .then((response) => {
              return response.json();
            })
            .then((data) => {
              if (data.error) {
                return alert(data.error);
              }

              let con = confirm('삭제하시겠습니까?');

              if (con) {
                alert(data.message);
                commentBox.querySelector('.comment-content .content').innerText = "삭제된 코멘트 입니다.";
                commentBox.querySelector('.list-util-wrap').remove();
                commentBox.querySelector('.info').innerText += ` (삭제일 ${data.comment_deleted_at})`;
              }
            })
            .catch((error) => {
              console.error('Error:', error);
            });
        }

        // 코멘트 컨텐츠 입력시 내용 출력
        if (commentBox && commentBox.querySelector('textarea[name="comment_content"]') !== null) {
          commentBox.querySelector('textarea[name="comment_content"]').addEventListener('input', function () {
            this.innerHTML = this.value;
          });
        }
      });

      // 코멘트 처리
      if (commentBtnWrap !== null) {
        function commentGetList(evtTarget = document.querySelector('.comment-btn-wrap .btn.disabled'), idx = {{ $idx }}, offset = document.querySelector('.comment-btn-wrap .btn.disabled').dataset.offset) {
          let route = '{{ route('comments.list', ['idx' => ':idx', 'offset' => ':offset', 'tableName' => $tableName ]) }}';

          route = route.replace(':idx', idx);
          route = route.replace(':offset', offset);

          fetch(route, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
          })
            .then((response) => {
              return response.json();
            })
            .then((data) => {
              if (data.error) {
                return alert(data.error);
              }

              let htmlTags = '',
                creEle = document.createElement('div'),
                dataLen = data.length - 1;
              data.forEach((ele, idx) => {
                htmlTags +=
                  `
                  <div
                  class="comment${ele.comment_state === 'y' && {{ $grade }} === 2 ? ' text-bg-danger' : ''}"
                  data-comment-idx="${ele.idx}" data-group-idx="${ele.group_idx}" data-group-order="${ele.group_order}" style="${ele.group_order ? 'padding-left: ' + ele.group_order + '%' : ''}">
                    <div class="comment-content">
                      <div class="info">
                        ${ele.comment_writer}<span class='created_at'>(작성일 ${ele.comment_created_at})</span>
                        ${(ele.comment_updated_at !== null && ele.comment_updated_at !== ele.comment_created_at) ? "<span class='updated_at'>(수정일 " + ele.comment_updated_at + ")</span>" : ''}
                        ${ele.comment_deleted_at !== null ? "(삭제일 " + ele.comment_deleted_at + ")" : ''}
                    </div>
                  `;

                if (ele.comment_state === 'y' && {{ $grade }} === 2) {
                  htmlTags += `<div class="content">${ele.comment_content}</div>`;
                } else if (ele.comment_state === 'n') {
                  htmlTags += `<div class="content">${ele.comment_content}</div>`;
                } else {
                  htmlTags += `<div class="contnet">삭제된 코멘트 입니다.</div>`;
                }
                htmlTags += `</div>`;

                if (ele.comment_state === 'n') {
                  @auth
                  if (ele.comment_writer === '{{ Illuminate\Support\Facades\Auth::user()['email'] }}') {
                    htmlTags += `<div class="list-util-wrap mt-1">`;
                    htmlTags += `<button type="button" class="btn btn-link btn-comment-edit">수정</button>
                    <button type="button" class="btn btn-link btn-comment-remove">삭제</button>`;
                    htmlTags += `</div>`;
                  } else {
                    htmlTags += `<div class="list-util-wrap mt-1">`;
                    htmlTags += `<button type="button" class="btn btn-link btn-comment-answer">답글</button>`
                    htmlTags += `</div>`;
                  }
                  @endauth
                }

                if (dataLen !== idx) {
                  htmlTags += `</div> <hr style="border-top:1px solid #6c757d;">`;
                }
              });

              if (evtTarget.nextElementSibling?.className === 'comment-box') {
                evtTarget.nextElementSibling.remove();
                return false;
              }

              evtTarget.after(creEle);
              creEle.classList.add('comment-box');

              evtTarget.nextElementSibling.innerHTML = `${htmlTags}`;
            })
            .catch((error) => {
              console.error('Error:', error);
            });
        }

        commentBtnWrap.addEventListener('click', function (evt) {
          let evtTarget = evt.target,
            datasetOffset = evtTarget.dataset.offset;

          if (datasetOffset !== undefined) {
            commentGetList(evtTarget, {{ $idx }}, datasetOffset);
          }
        });

        window.addEventListener('DOMContentLoaded', function () {
          commentGetList();
        });
      }

      // 코멘트 클릭시 - 본문 내용 보이기
      if (commentViewBtn !== null) {
        commentViewBtn.addEventListener('click', function () {
          this.closest('.board-detail-wrap').querySelector('.content').classList.remove('d-none');
          this.classList.add('d-none');
        });
      }
    </script>
  @endpush

@endsection
