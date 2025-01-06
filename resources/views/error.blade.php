@extends('layouts.app')

@section('content')
<div class="container">
    @auth
    <h1>{{ $title }}{{ $user->name }}</h1>
    <div class="row">
        <span>{{ $errortext }}</span>
        <span>{{ $errorreason }}</span>
    </div>
    <button onclick="history.back()" class="btn btn-secondary">戻る</button>
    @else
    @endauth
</div>
<script>
@endsection
