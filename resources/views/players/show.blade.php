@extends('layouts.app')

@section('content')

<div class="container">

    <div class="mb-4">
        <h3>{{ $player->name }}</h3>
        <div class="text-muted">
            選手管理
        </div>
    </div>

    <div class="row">

        <div class="col-12 col-sm-6 col-md-4">
            <a href="{{ route('conditions.index') }}" class="player-menu-card">
                <div class="player-menu-icon bg-teal-50">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div>
                    <div class="player-menu-title">日時管理</div>
                    <div class="player-menu-subtitle">コンディショニング</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <a href="{{ route('goals.index') }}" class="player-menu-card">
                <div class="player-menu-icon bg-indigo-50">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="player-menu-title">目標設定</div>
                    <div class="player-menu-subtitle">競技計画・振り返り</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <a href="{{ route('trainings.index') }}" class="player-menu-card">
                <div class="player-menu-icon bg-green-50">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div>
                    <div class="player-menu-title">トレーニング</div>
                    <div class="player-menu-subtitle">トレーニング計画・実績</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <a href="{{ route('practices.index') }}" class="player-menu-card">
                <div class="player-menu-icon bg-emerald-50">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <div class="player-menu-title">練習管理</div>
                    <div class="player-menu-subtitle">スケジュール・日誌</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <a href="{{ route('nutritions.index') }}" class="player-menu-card">
                <div class="player-menu-icon bg-amber-50">
                    <i class="fas fa-apple-alt"></i>
                </div>
                <div>
                    <div class="player-menu-title">栄養管理</div>
                    <div class="player-menu-subtitle">食事・栄養状況</div>
                </div>
            </a>
        </div>

    </div>

    <div class="mt-3">
        <a href="{{ route('players.index') }}" class="btn btn-secondary">
            選手一覧へ戻る
        </a>
    </div>

</div>

@endsection
