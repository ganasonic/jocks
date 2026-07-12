@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fw-bold">🎯 目標設定 & PDCA管理</h3>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary">
                        新しい目標を設定する
                    </a>
                </div>

                <div class="card-body">
                    {{-- 期間切り替えタブ --}}
                    <ul class="nav nav-tabs mb-4">
                        @foreach([
                            'daily' => '日単位',
                            'weekly' => '週単位',
                            'monthly' => '月単位',
                            'yearly' => '年単位'
                        ] as $key => $label)
                            <li class="nav-tabs-item">
                                <a class="nav-link @if($type === $key) active fw-bold text-primary @else text-secondary @endif"
                                   href="{{ route('goals.index', ['period_type' => $key]) }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    {{-- 目標リスト --}}
                    @if($goals->isEmpty())
                        <div class="text-center text-muted my-5">
                            <p class="fs-5 mb-2">まだこの期間の目標が設定されていません。</p>
                            <p class="small">アクションプランを決めて、一歩ずつ進んでいきましょう！</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($goals as $goal)
                                <div class="col-md-6">
                                    <div class="card h-100 border-start border-4 @if($goal->achievement_rate >= 80) border-success @elseif($goal->achievement_rate && $goal->achievement_rate < 50) border-danger @else border-primary @endif">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-light text-dark border small">
                                                    📅 {{ $goal->start_date }} 〜 {{ $goal->end_date }}
                                                </span>
                                                @if(isset($goal->achievement_rate))
                                                    <span class="badge @if($goal->achievement_rate >= 80) bg-success @else bg-primary @endif">
                                                        達成度: {{ $goal->achievement_rate }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark">進行中 / 未振り返り</span>
                                                @endif
                                            </div>

                                            <h5 class="fw-bold text-dark mb-2">{{ $goal->title }}</h5>

                                            @if($goal->action_plan)
                                                <div class="bg-light p-2 rounded mb-3 small">
                                                    <strong>📋 行動プラン:</strong><br>
                                                    <span class="text-muted">{{ Str::limit($goal->action_plan, 80) }}</span>
                                                </div>
                                            @endif

                                            {{-- コーチコメントの有無 --}}
                                            @if($goal->coach_comment)
                                                <div class="text-success small mb-3">
                                                    💬 <strong>コーチからのフィードバックあり</strong>
                                                </div>
                                            @endif

                                            <div class="d-flex justify-content-end">
                                                <a href="{{ route('goals.edit', $goal->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    🔍 振り返り・コーチコメントを確認
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
