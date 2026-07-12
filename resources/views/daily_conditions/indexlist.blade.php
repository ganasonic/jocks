@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- 登録成功時のメッセージ --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="h5 mb-0">体調記録履歴</span>
                    <a href="{{ route('daily_conditions.create') }}" class="btn btn-sm btn-primary">新しい体調を記録</a>
                </div>

                <div class="card-body">
                    @if($conditions->isEmpty())
                        <p class="text-center my-4">まだ体調の記録がありません。「新しい体調を記録」から登録してみましょう！</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>日付</th>
                                        <th>体温</th>
                                        <th>体調</th>
                                        <th>気分</th>
                                        <th>睡眠</th>
                                        <th>メモ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($conditions as $condition)
                                        <tr>
                                            <td class="fw-bold">{{ $condition->date }}</td>
                                            <td>{{ $condition->body_temperature ? $condition->body_temperature . ' ℃' : '-' }}</td>
                                            <td>
                                                <span class="badge {{ $condition->condition_level >= 4 ? 'bg-success' : ($condition->condition_level <= 2 ? 'bg-danger' : 'bg-secondary') }}">
                                                    レベル {{ $condition->condition_level }}
                                                </span>
                                            </td>
                                            <td>レベル {{ $condition->mood_level }}</td>
                                            <td>
                                                @if($condition->wakeup_time || $condition->bedtime)
                                                    {{ $condition->wakeup_time ? \Carbon\Carbon::parse($condition->wakeup_time)->format('H:i') : '--:--' }}
                                                    〜
                                                    {{ $condition->bedtime ? \Carbon\Carbon::parse($condition->bedtime)->format('H:i') : '--:--' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted d-inline-block text-truncate" style="max-width: 150px;">
                                                    {{ $condition->meals_memo ?? '-' }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
