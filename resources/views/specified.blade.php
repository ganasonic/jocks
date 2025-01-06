@extends('layouts.app')

@section('content')
<div class="container">
    @auth
    <h1>{{ $title }}</h1>
    <div class="row">
    </div>
    @else
    @endauth
</div>
<script>
@endsection
