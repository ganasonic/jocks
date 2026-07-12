@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 fw-bold fs-5">🎯 新しい目標の設定（Plan）</div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('goals.store') }}" method="POST">
                        @csrf

                        {{-- 期間のタイプ --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">期間の種類</label>
                            <select name="period_type" class="form-control" required>
                                <option value="daily">日単位 (今日・明日)</option>
                                <option value="weekly" selected>週単位 (今週の目標)</option>
                                <option value="monthly">月単位 (今月の目標)</option>
                                <option value="yearly">年単位 (年間目標)</option>
                            </select>
                        </div>

                        {{-- 開始日・終了日 --}}
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">開始日</label>
                                <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">終了日</label>
                                <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            </div>
                        </div>

                        {{-- 目標タイトル --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">目指す目標（成果目標）</label>
                            <input type="text" name="title" class="form-control" placeholder="例: 5kmランニングのタイムを25分切る / 体重を1kg絞る" required>
                        </div>

                        {{-- アクションプラン --}}
                        <div class="form-group mb-4">
                            <label class="form-label fw-bold">行動プラン（具体的なアクション）</label>
                            <textarea name="action_plan" class="form-control" rows="4" placeholder="例: 火・木・土の朝に30分インターバル走をする。間食のチョコを素焼きナッツに変える。"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary w-50">戻る</a>
                            <button type="submit" class="btn btn-primary w-50">目標を設定する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
