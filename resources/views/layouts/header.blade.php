<header class="common-header">
  <div class="inner">
    <h1 class="common-title"><a href="{{ route('home') }}">Community</a></h1>
    <div class="login-wrap">
      <form action="{{ Illuminate\Support\Facades\Auth::check() ? route('logout') : route('login.check') }}"
            method="post">
        @csrf
        <div class="login">
          @auth
            <div class="info text-center mb-1">{{ Illuminate\Support\Facades\Auth::user()['name'] }}님! 반갑습니다.</div>
            <div class="account">
              <button type="submit" class="btn btn-link">로그아웃</button>
              <a href="{{ route('verification.notice') }}" class="btn btn-link">인증메일 재발송</a>
            </div>
          @endauth
          @guest
            <div class="guest-login d-flex mb-1">
              <label class="me-2"><input type="text" class="form-control" name="email" placeholder="아이디 입력"
                                         autocomplete="email"></label>
              <label class="me-1"><input type="password" class="form-control" name="password" placeholder="비밀번호 입력"
                                         autocomplete="current-password"></label>
              <button type="submit" class="btn btn-link">로그인</button>
            </div>
            <div class="user-space d-flex align-items-center justify-content-end">
              <a href="{{ route('join') }}" class="link me-2">회원가입</a>
              <a href="{{ route('password.request') }}" class="link me-2">정보찾기</a>
              <div class="d-flex align-items-center">
                <input type="checkbox" id="rememberMe" class="me-1" name="remember_me" value="1">
                <label for="rememberMe">자동로그인</label>
              </div>
            </div>
          @endguest
        </div>
      </form>
    </div>
  </div>
</header>

@if ($errors->any())
  <div class="alert alert-danger mt-1 mb-0">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<script>
  let rememberMeInput = document.getElementById('rememberMe');

  if (rememberMeInput !== null) {
    rememberMeInput.addEventListener('input', function () {
        if (this.checked) {
          let con = confirm('자동로그인을 사용하면 다음부터 아이디와 비밀번호를 입력하실 필요 없습니다.\n공공장소에서는 개인정보가 유출 될 수 있으니 사용을 자제해 주십시요.\n\n자동 로그인을 사용하시겠습니까?');
          if (!con) {
            this.checked = false;
          }
        }
      });
  }
</script>
