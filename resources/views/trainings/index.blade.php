@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <!-- 他の画面（日時管理・目標設定）と完全に統一したヘッダーと新規ボタン -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">トレーニング履歴</h1>
                <a href="{{ route('trainings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 新規記録
                </a>
            </div>

            <!-- 履歴一覧表示部分 -->
            @if($trainings->isEmpty())
                <div class="card shadow-sm text-center p-5">
                    <p class="text-muted mb-0">トレーニング記録がありません。</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($trainings as $training)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 font-weight-bold">
                                    <!-- コントローラーのtraining_dateカラムに修正 -->
                                    {{ \Carbon\Carbon::parse($training->training_date)->format('Y年m月d日') }}
                                </h5>
                                <span class="badge badge-secondary">
                                    種目数: {{ $training->details->count() }}
                                </span>
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
                                                <th>RPE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($training->details as $detail)
                                                <tr>
                                                    <td class="text-left pl-4 font-weight-bold">{{ $detail->exercise_name }}</td>
                                                    <td>{{ $detail->weight ? $detail->weight . ' kg' : '-' }}</td>
                                                    <td>{{ $detail->reps ?? '-' }}</td>
                                                    <td>{{ $detail->sets ?? '-' }}</td>
                                                    <td>{{ $detail->rpe ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
