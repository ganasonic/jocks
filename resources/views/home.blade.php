@extends('layouts.app')

@section('content')
<div class="container">
    @auth
    <div class="row">
        <ul class="menu_btn">
            @if(isset($menu))

            @foreach($menu as $d)
            <li>
                @if($d['sub_name']=='logout')
                    <a href="#" class="{{$d['class']}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <div>
                            <p>{{$d['name']}}</p>
                            @if($d['sub_name'])
                                {!! preg_replace('/([^,]+),?/is', '<span>$1</span>', $d['sub_name']) !!}
                            @endif
                        </div>
                    </a>
                @else
                    <a
                    href="{{url($d['url'])}}"
                    class="{{$d['class']}}">
                        <div>
                            <p>{{$d['name']}}</p>
                            @if($d['sub_name'])
                                {!! preg_replace('/([^,]+),?/is', '<span>$1</span>', $d['sub_name']) !!}
                            @endif
                        </div>
                    </a>
                @endif
            </li>
            @endforeach
            @endif
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </ul>
    </div>
    @else
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">川場ファミリーシフト管理</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endauth
</div>
<script>
@endsection
