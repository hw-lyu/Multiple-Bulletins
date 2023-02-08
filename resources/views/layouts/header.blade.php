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
              <a href="{{ route('verification.notice') }}" class="link">인증메일 재발송</a>
            </div>
          @endauth
          @guest
            <div class="guest-login mb-1">
              <label><input type="text" class="form-control" name="email" placeholder="아이디 입력" autocomplete="email"></label>
              <label><input type="password" class="form-control" name="password" placeholder="비밀번호 입력"
                            autocomplete="current-password"></label>
              <button type="submit" class="btn btn-link">로그인</button>
            </div>
            <div class="user-space text-end">
              <a href="{{ route('join') }}" class="link">회원가입</a>
              <button type="button" class="btn btn-link link auth-find">정보찾기</button>
              <input type="checkbox" id="rememberMe" name="remember_me" value="1">
              <label for="rememberMe">자동로그인</label>
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

@guest
  @push('scripts')
    <script>
      let authFind = document.querySelector('.login-wrap .auth-find');

      authFind.addEventListener('click', () => {
        alert('현재 준비중인 페이지입니다.');
      });
    </script>
  @endpush
@endguest
