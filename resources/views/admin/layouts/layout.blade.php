@extends('layouts.layout')
@section('title', 'Admin')
@section('content')
  <section class="admin-wrap">
    <h3 class="visually-hidden">어드민 툴</h3>
    <div class="d-flex justify-content-between inner">
      <nav class="left-side-bar col-2">
        <ul class="list text-center mb-0 pl-0 ps-0">
          <li><a href="{{ route('admin') }}">Admin Home</a></li>
          <li><a href="{{ route('admin.board') }}">메뉴 리스트</a></li>
          <li><a href="{{ route('admin.board.create') }}">메뉴 추가</a></li>
        </ul>
      </nav>
      <div class="content col-10">
        @yield('admin-content')
      </div>
    </div>
  </section>
@endsection
@push('scripts')
  @vite(['resources/js/admin.js'])
@endpush
