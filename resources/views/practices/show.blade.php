@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">練習記録詳細</h2>
        <div>
            <a href="{{ route('practices.edit', $practice->id) }}" class="btn btn-secondary me-2">編集</a>
            <a href="{{ route('practices.index') }}" class="btn btn-outline-secondary">一覧へ戻る</a>
        </div>
    </div>

    <div class="card shadow-sm border-light-subtle mb-4">
        <div class="card-body p-4">
            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>練習日:</strong> {{ \Carbon\Carbon::parse($practice->practice_date)->format('Y/m/d') }}
                </div>
                <div class="col-md-4">
                    <strong>種別・カテゴリ:</strong> {{ $practice->practice_type ?? '未設定' }}
                </div>
                <div class="col-md-4">
                    <strong>タイトル・場所:</strong> {{ $practice->title ?? '未設定' }}
                </div>
            </div>

            @if($practice->target)
                <div class="mb-3">
                    <strong>本日の目標:</strong>
                    <div class="p-2 bg-light border rounded mt-1">{{ $practice->target }}</div>
                </div>
            @endif

            @if($practice->overall_coach_comment)
                <div class="mb-3">
                    <strong class="text-success">全体アドバイス・総評（コーチ）:</strong>
                    <div class="p-3 bg-success-subtle border border-success-subtle text-success rounded mt-1">
                        {{ $practice->overall_coach_comment }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <h3 class="h5 fw-bold mb-3">練習メニュー</h3>
    @forelse($practice->details as $detail)
        <div class="card shadow-sm border-light-subtle mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                    <h4 class="h6 fw-bold mb-0 text-primary">{{ $detail->menu_name }}</h4>
                    <div>
                        <span class="badge bg-secondary me-2">本数/時間: {{ $detail->runs_or_time ?? '未設定' }}</span>
                        <span class="badge bg-warning text-dark me-1">本人: {{ $detail->rating ? str_repeat('★', $detail->rating) : '未評価' }}</span>
                        <span class="badge bg-primary">コーチ: {{ $detail->coach_rating ? str_repeat('★', $detail->coach_rating) : '未評価' }}</span>
                    </div>
                </div>

                <div class="row g-2 mb-2 fs-7">
                    @if($detail->impression)
                        <div class="col-md-6">
                            <strong>感触・自己評価:</strong>
                            <p class="mb-0 text-muted">{{ $detail->impression }}</p>
                        </div>
                    @endif
                    @if($detail->notice)
                        <div class="col-md-6">
                            <strong>課題・反省:</strong>
                            <p class="mb-0 text-muted">{{ $detail->notice }}</p>
                        </div>
                    @endif
                </div>

                @if($detail->coach_comment)
                    <div class="p-2 bg-success-subtle text-success rounded fs-7 mb-2">
                        <strong>アドバイス（コーチ）:</strong> {{ $detail->coach_comment }}
                    </div>
                @endif

                @if($detail->video_url)
                    <div class="fs-7">
                        <i class="bi bi-camera-video"></i> <strong>動画URL:</strong>
                        <a href="{{ $detail->video_url }}" target="_blank" rel="noopener noreferrer">{{ $detail->video_url }}</a>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-light border">登録された練習メニューはありません。</div>
    @endforelse
</div>
@endsection
