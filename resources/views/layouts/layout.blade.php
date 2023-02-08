<!doctype html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title') - Community</title>
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<div class="wrap">
  @include('layouts.header')

  <section class="main-container mt-5 mb-5">
    <h2 class="visually-hidden">메인 콘텐츠</h2>
    @yield('content')
  </section>
</div>

@stack('scripts')
</body>
</html>
