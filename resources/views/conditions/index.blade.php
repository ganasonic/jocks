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

            {{-- 1. グラフ表示エリア (新設) --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">📈 直近の体調推移（過去30回分）</h5>
                </div>
                <div class="card-body">
                    @if(empty($graphLabels))
                        <p class="text-center text-muted my-4">データが蓄積されると、ここに体温と体調の推移グラフが表示されます。</p>
                    @else
                        <div style="position: relative; height: 300px; width: 100%;">
                            <canvas id="conditionChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. カレンダー本体 --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('conditions.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="btn btn-outline-secondary btn-sm">
                            &lt; 前月
                        </a>
                        <h3 class="mb-0 mx-3 fw-bold">{{ $year }}年 {{ $month }}月</h3>
                        <a href="{{ route('conditions.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="btn btn-outline-secondary btn-sm">
                            次月 &gt;
                        </a>
                    </div>
                    <a href="{{ route('conditions.create') }}" class="btn btn-primary">
                        体調を記録する
                    </a>
                </div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0 calendar-table" style="table-layout: fixed; width: 100%;">
                        <thead class="table-light text-center">
                            <tr>
                                <th class="text-danger" style="width: 14.28%;">日</th>
                                <th style="width: 14.28%;">月</th>
                                <th style="width: 14.28%;">火</th>
                                <th style="width: 14.28%;">水</th>
                                <th style="width: 14.28%;">木</th>
                                <th style="width: 14.28%;">金</th>
                                <th class="text-primary" style="width: 14.28%;">土</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calendarWeeks as $week)
                                <tr style="height: 110px;">
                                    @foreach($week as $dayData)
                                        @if(is_null($dayData))
                                            <td class="bg-light"></td>
                                        @else
                                            <td class="p-2 position-relative align-top @if($dayData['date'] == date('Y-m-d')) bg-warning bg-opacity-10 @endif">
                                                {{-- 日付の数字表示の下あたり --}}
                                                <div class="fw-bold mb-1 small">{{ $dayData['day'] }}</div>
                                                {{-- 体調データが存在する場合 --}}
                                                @if($dayData['data'])
                                                    {{-- ⬇️ ここに編集画面へのリンクを追記（右上に小さく配置する例） --}}
                                                    <div class="position-absolute" style="top: 5px; right: 5px;">
                                                        <a href="{{ route('conditions.edit', ['date' => $dayData['date']]) }}" class="text-decoration-none btn btn-link p-0 text-muted" style="font-size: 0.75rem;" title="編集">✏️</a>
                                                    </div>

                                                    <div class="d-flex flex-column gap-1">
                                                        @if($dayData['data']->body_temperature)
                                                            <span class="text-secondary small fw-semibold" style="font-size: 0.75rem;">
                                                                🌡️ {{ $dayData['data']->body_temperature }}℃
                                                            </span>
                                                        @endif

                                                        @php
                                                            $score = $dayData['data']->condition_level;
                                                            $badgeClass = $score >= 4 ? 'bg-success' : ($score <= 2 ? 'bg-danger' : 'bg-primary');
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }} text-start p-1" style="font-size: 0.7rem; white-space: normal; line-height: 1.2;">
                                                            体調: {{ $score }}
                                                        </span>

                                                        <span class="badge bg-secondary text-start p-1" style="font-size: 0.7rem; white-space: normal; line-height: 1.2;">
                                                            気分: {{ $dayData['data']->mood_level }}
                                                        </span>

                                                        @if($dayData['data']->meals_memo)
                                                            <div class="text-muted text-truncate mt-1" style="font-size: 0.65rem;" title="{{ $dayData['data']->meals_memo }}">
                                                                📝 {{ $dayData['data']->meals_memo }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="text-center mt-3">
                                                        <a href="{{ route('conditions.create', ['date' => $dayData['date']]) }}" class="text-decoration-none text-muted opacity-25" style="font-size: 0.8rem;">+</a>
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .calendar-table td { transition: background-color 0.2s; }
    .calendar-table td:hover { background-color: rgba(0, 0, 0, 0.02); }
</style>

{{-- 3. Chart.jsライブラリの読み込みとグラフスクリプト --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // PHPから渡された配列データをJavaScriptの配列に展開
        const labels = {!! json_encode($graphLabels) !!};
        const tempData = {!! json_encode($graphTemperatures) !!};
        const condData = {!! json_encode($graphConditions) !!};

        if (labels.length === 0) return;

        const ctx = document.getElementById('conditionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '体温 (°C)',
                        data: tempData,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        yAxisID: 'y-temp',
                        tension: 0.2
                    },
                    {
                        label: '体調レベル (1-5)',
                        data: condData,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        yAxisID: 'y-cond',
                        tension: 0.2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    'y-temp': {
                        type: 'linear',
                        position: 'left',
                        min: 35.0,
                        max: 39.0,
                        title: { display: true, text: '体温 (°C)' }
                    },
                    'y-cond': {
                        type: 'linear',
                        position: 'right',
                        min: 1,
                        max: 5,
                        ticks: { stepSize: 1 },
                        grid: { drawOnChartArea: false }, // グリッド線が重なって見づらくなるのを防ぐ
                        title: { display: true, text: '体調レベル' }
                    }
                }
            }
        });
    });
</script>
@endsection
