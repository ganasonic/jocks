@extends('layouts.app')

@section('content')

<style>
    .player-menu-card {
        display: flex;
        align-items: center;
        padding: 15px;
        background-color: #ffffff;
        border: 1px solid #f0f0f0;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        text-decoration: none !important;
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 15px;
        height: 86px;
        color: #333;
    }

    .player-menu-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .player-menu-icon {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        background-color: #f3f4f6;
        flex-shrink: 0;
    }

    .player-menu-icon i {
        font-size: 20px;
    }

    .player-menu-title {
        font-size: 17px;
        font-weight: bold;
    }

    .player-menu-subtitle {
        font-size: 12px;
        color: #888;
        margin-top: 4px;
    }
</style>

<div class="container">

    <div class="mb-4">
        <h3>{{ $player->name }}</h3>
        <div class="text-muted">
            選手管理
        </div>
    </div>

    <div class="row">

        <div class="col-12 col-sm-6 col-md-4">
            <a href="#" class="player-menu-card">
                <div class="player-menu-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div>
                    <div class="player-menu-title">日時管理</div>
                    <div class="player-menu-subtitle">コンディショニング</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <a href="#" class="player-menu-card">
                <div class="player-menu-icon">
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
                <div class="player-menu-icon">
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
                <div class="player-menu-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <div class="player-menu-title">練習管理</div>
                    <div class="player-menu-subtitle">スケジュール・日誌</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <a href="#" class="player-menu-card">
                <div class="player-menu-icon">
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
