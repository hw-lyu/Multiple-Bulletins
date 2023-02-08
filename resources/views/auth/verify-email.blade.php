@extends('layouts.layout')
@section('title', '인증메일 재발송')
@section('content')
  <form action="{{ route('verification.send') }}" method="post" class="inner">
    @csrf
    @if($userEmailVerifiedState)
      <button type="submit" class="btn btn-link">인증메일 재발송</button>
    @else
      <div class="info">이미 이메일 인증이 된 계정입니다.</div>
    @endif

    @if(!empty( session('message') ))
      <div class="info">{!! session('message') !!}</div>
    @endif
  </form>
@endsection
