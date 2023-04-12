@extends('layouts.layout')
@section('title', '글쓰기')
@section('content')
  <div class="inner board-detail-wrap">
    <h3 class="board-title">게시판 제목</h3>
    <div class="btn-wrap mb-3">
      <a href="{{ route('home') }}" class="link">리스트</a>
    </div>
    <form action="{{ route('board.store', ['tableName' => $tableName]) }}" method="post" class="form-board-write" onsubmit="return false;">
      @csrf
      <input type="hidden" name="photo_state" value="{{ old('photo_state') }}">
      <div class="head">
        <input type="text" class="form-control board-title mb-1" name="board_title" placeholder="글제목"
               value="{{ old('board_title') }}">
        <select class="form-select board-cate" aria-label="Default select" name="board_cate">
          <option value="분류" {{ old('board_cate') === '분류' ? 'selected' : ''  }}>분류</option>
          <option value="카테1" {{ old('board_cate') === '카테1' ? 'selected' : ''  }}>카테1</option>
          <option value="카테2" {{ old('board_cate') === '카테2' ? 'selected' : ''  }}>카테2</option>
          <option value="카테3" {{ old('board_cate') === '카테3' ? 'selected' : ''  }}>카테3</option>
        </select>
      </div>
      <div class="content mt-3">
        <textarea name="board_content" id="editor">{{ old('board_content') }}</textarea>
        <div class="btn-wrap text-center mt-3">
          <button type="submit" class="btn btn-link btn-add">글등록</button>
          <button type="button" class="btn btn-link" onclick="window.history.back();">취소</button>
        </div>
      </div>
    </form>
  </div>

  @push('scripts')
    <script src="{{ asset('lib/ckeditor.js') }}"></script>
    <script>
      class MyUploadAdapter {
        constructor(loader) {
          // The file loader instance to use during the upload.
          this.loader = loader;
        }

        // Starts the upload process.
        upload() {
          return this.loader.file
            .then(file => new Promise((resolve, reject) => {

              this._initRequest();
              this._initListeners(resolve, reject, file);
              this._sendRequest(file);
            }));
        }

        // Aborts the upload process.
        abort() {
          if (this.xhr) {
            this.xhr.abort();
          }
        }

        // Initializes the XMLHttpRequest object using the URL passed to the constructor.
        _initRequest() {
          const xhr = this.xhr = new XMLHttpRequest();

          // Note that your request may look different. It is up to you and your editor
          // integration to choose the right communication channel. This example uses
          // a POST request with JSON as a data structure but your configuration
          // could be different.
          xhr.open('POST', '{{ route('upload.store').'?_token='.csrf_token() }}', true);
          xhr.responseType = 'json';
        }

        // Initializes XMLHttpRequest listeners.
        _initListeners(resolve, reject, file) {
          const xhr = this.xhr;
          const loader = this.loader;
          const genericErrorText = `Couldn't upload file: ${file.name}.`;

          xhr.addEventListener('error', () => reject(genericErrorText));
          xhr.addEventListener('abort', () => reject());
          xhr.addEventListener('load', () => {
            const response = xhr.response,
              responseErr = response.error;
            // This example assumes the XHR server's "response" object will come with
            // an "error" which has its own "message" that can be passed to reject()
            // in the upload promise.
            //
            // Your integration may handle upload errors in a different way so make sure
            // it is done properly. The reject() function must be called when the upload fails.

            if (!response || responseErr) {
              if (responseErr) alert(`${genericErrorText}\n${responseErr}`);
              return reject(response && responseErr ? responseErr.message : genericErrorText);
            }

            // If the upload is successful, resolve the upload promise with an object containing
            // at least the "default" URL, pointing to the image on the server.
            // This URL will be used to display the image in the content. Learn more in the
            // UploadAdapter#upload documentation.
            resolve({
              default: response.url
            });
          });

          // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
          // properties which are used e.g. to display the upload progress bar in the editor
          // user interface.
          if (xhr.upload) {
            xhr.upload.addEventListener('progress', evt => {
              if (evt.lengthComputable) {
                loader.uploadTotal = evt.total;
                loader.uploaded = evt.loaded;
              }
            });
          }
        }

        // Prepares the data and sends the request.
        _sendRequest(file) {
          // Prepare the form data.
          const data = new FormData();

          data.append('upload', file);
          // Important note: This is the right place to implement security mechanisms
          // like authentication and CSRF protection. For instance, you can use
          // XMLHttpRequest.setRequestHeader() to set the request headers containing
          // the CSRF token generated earlier by your application.

          // Send the request.
          this.xhr.send(data);
        }
      }

      // ...

      function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
          // Configure the URL to the upload script in your back-end here!

          return new MyUploadAdapter(loader);
        };
      }

      ClassicEditor.create(document.querySelector('#editor'), {
        extraPlugins: [MyCustomUploadAdapterPlugin],
        image: {
          toolbar: [
            '|',
            'toggleImageCaption',
            'imageTextAlternative'
          ]
        },
        imageRemoveEvent: {
          additionalElementTypes: null,
          callback: (imagesSrc, nodeObjects) => {
            document.querySelector('.form-board-write').insertAdjacentHTML('afterbegin', `<input type="hidden" name="board_content_delete_img[]" value="${imagesSrc[0]}">`)
          }
        }
      })
        .catch(error => {
          console.error('There was a problem initializing the editor.', error);
        });

      //버튼 클릭시 photoState를 위한 비교값 추가
      document.querySelector('.btn-add').addEventListener('click', function () {
        const FigureImgArrLen = [...document.querySelectorAll('figure.image img[src*="http"]')].length;

        let photoState = document.querySelector('input[name="photo_state"]');

        if (!FigureImgArrLen) {
          photoState.value = 'N';
        } else {
          photoState.value = 'Y';
        }

        this.closest('.form-board-write').submit();
      });
    </script>
  @endpush
@endsection
