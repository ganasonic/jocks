@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-4">
        選手管理
    </h3>

    <table class="table table-bordered table-striped">

        <thead>
        <tr>
            <th>ID</th>
            <th>氏名</th>
            <th>メール</th>
        </tr>
        </thead>

        <tbody>

        @foreach($players as $player)

        <tr>
            <td>{{ $player->id }}</td>
            <td>
                <a href="{{ route('players.edit',$player) }}">
                    {{ $player->name }}
                </a>
            </td>
            <td>{{ $player->email }}</td>
        </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endsection
