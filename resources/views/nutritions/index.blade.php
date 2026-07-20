@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>食事記録一覧</h1>
        <a href="{{ route('nutritions.create') }}" class="btn btn-primary">新規登録</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>日付</th>
                <th>日次メモ</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nutritions as $nutrition)
            <tr>
                <td>{{ $nutrition->nutrition_date }}</td>
                <td>{{ $nutrition->daily_memo }}</td>
                <td>
                    <a href="{{ route('nutritions.show', $nutrition->id) }}" class="btn btn-sm btn-info">詳細</a>
                    <a href="{{ route('nutritions.edit', $nutrition->id) }}" class="btn btn-sm btn-secondary">編集</a>
                    <form action="{{ route('nutritions.destroy', $nutrition->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('削除してもよろしいですか？')">削除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
