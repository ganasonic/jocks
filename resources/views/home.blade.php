@extends('layouts.app')

@section('content')
<!-- アイコン表示用のFont Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- カスタムCSS（Bootstrapの標準カードを上書きしてモダンにするためのスタイル） -->
<style>
    .menu-card {
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
    }
    .menu-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .icon-box {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .icon-box i {
        font-size: 20px;
    }
    .menu-title {
        font-size: 17px;
        font-weight: bold;
        color: #333333;
        line-height: 1.2;
    }
    .menu-subtitle {
        font-size: 12px;
        color: #888888;
        margin-top: 4px;
        line-height: 1.2;
    }
    /* configから渡される背景色と文字色のカスタムマッピング */
    .bg-orange-50  { background-color: #fff7ed; color: #ea580c; }
    .bg-yellow-50  { background-color: #fefce8; color: #ca8a04; }
    .bg-blue-50    { background-color: #eff6ff; color: #2563eb; }
    .bg-teal-50    { background-color: #f0fdfa; color: #0d9488; }
    .bg-indigo-50  { background-color: #eef2ff; color: #4f46e5; }
    .bg-green-50   { background-color: #f0fdf4; color: #16a34a; }
    .bg-emerald-50 { background-color: #ecfdf5; color: #059669; }
    .bg-amber-50   { background-color: #fffbeb; color: #d97706; }
    .bg-gray-100   { background-color: #f3f4f6; color: #4b5563; }
</style>

<div class="container">
    @auth
    {{-- 案A: メニュー上部にダッシュボードエリアを追加 --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border-radius: 12px; border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                <div class="card-header bg-white" style="border-radius: 12px 12px 0 0; font-weight: bold;">
                    <i class="fas fa-tachometer-alt mr-2"></i> マイダッシュボード
                </div>
                <div class="card-body">
                    {{-- ここにカレンダーや当日サマリーを埋め込みます --}}
                    <p>直近の練習予定やコンディション状況を表示するエリア</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @if(isset($menu))
            @foreach($menu as $d)
                <!-- Bootstrapのグリッドシステムを利用 (PCで3カラム、タブレットで2カラム、スマホで1カラム) -->
                <div class="col-12 col-sm-6 col-md-4">
                    @if($d['sub_name'] == 'Logout' || $d['url'] == '/logout')
                        <!-- ログアウト -->
                        <a href="#" class="menu-card" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <div class="icon-box bg-gray-100">
                                <i class="{{ $d['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="menu-title">{{ $d['name'] }}</div>
                                <div class="menu-subtitle">{{ $d['sub_name'] }}</div>
                            </div>
                        </a>
                    @else
                        <!-- 通常メニュー -->
                        <a href="{{ url($d['url']) }}" class="menu-card">
                            <!-- クラス名から背景色とアイコン色を抽出して適用 -->
                            @php
                                // configのclass文字列から「bg-xxx-50」または「bg-gray-100」の部分だけを抽出
                                preg_match('/bg-[a-z]+-\d+/', $d['class'], $matches);
                                $bgClass = $matches[0] ?? 'bg-gray-100';
                            @endphp
                            <div class="icon-box {{ $bgClass }}">
                                <i class="{{ $d['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="menu-title">{{ $d['name'] }}</div>
                                @if($d['sub_name'])
                                    <div class="menu-subtitle">{{ $d['sub_name'] }}</div>
                                @endif
                            </div>
                        </a>
                    @endif
                </div>
            @endforeach
        @endif

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    @else
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">川場ファミリーシフト管理</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endauth
</div>
@endsection
