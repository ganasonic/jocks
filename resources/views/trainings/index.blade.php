@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">トレーニング履歴</h1>
                <a href="{{ route('trainings.create') }}" class="btn btn-primary">
                    ＋ 新規記録
                </a>
            </div>

            @if($trainings->isEmpty())
                <div class="card shadow-sm text-center p-5">
                    <p class="text-muted mb-0">トレーニング記録がありません。</p>
                </div>
            @else
                @foreach($trainings as $training)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted mr-2">
                                    {{ \Carbon\Carbon::parse($training->training_date)->format('Y年m月d日') }}
                                </span>
                                <!-- ワークアウト名を表示 -->
                                <h5 class="card-title d-inline mb-0 font-weight-bold ml-2">
                                    {{ $training->title ?? 'ワークアウト' }}
                                </h5>
                            </div>
                            <div>
                                <!-- ★ 編集ボタンを追加 -->
                                <a href="{{ route('trainings.edit', $training->id) }}" class="btn btn-sm btn-outline-primary mr-2">
                                    編集
                                </a>
                                <span class="badge badge-secondary">
                                    種目数: {{ $training->details->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0 text-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-left pl-4">種目名</th>
                                            <th>重量</th>
                                            <th>回数</th>
                                            <th>セット</th>
                                            <th>インターバル</th>
                                            <th>RPE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($training->details as $detail)
                                            <tr>
                                                <td class="text-left pl-4 font-weight-bold">{{ $detail->exercise_name }}</td>
                                                <td>{{ $detail->weight ? $detail->weight . ' kg' : '-' }}</td>
                                                <td>{{ $detail->reps ? $detail->reps . ' 回' : '-' }}</td>
                                                <td>{{ $detail->sets ? $detail->sets . ' セット' : '-' }}</td>
                                                <td>{{ $detail->interval ? $detail->interval . ' 秒' : '-' }}</td>
                                                <td>{{ $detail->rpe ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
</div>
@endsection
