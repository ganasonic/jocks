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
            {{-- ============================================================
                グラフ表示エリア
                ============================================================ --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            📈 直近のコンディション推移
                        </h5>
                        <span class="text-muted small">
                            過去30回分
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    @if(empty($graphLabels))
                        <p class="text-center text-muted my-4">
                            データが蓄積されると、
                            ここにコンディションの推移グラフが表示されます。
                        </p>
                    @else
                        {{-- ====================================================
                            グラフ選択
                            ==================================================== --}}
                        <div class="condition-chart-tabs mb-4">
                            <button type="button"
                                    class="btn btn-primary chart-tab active"
                                    data-chart="condition">
                                <i class="fas fa-heartbeat"></i>
                                体調
                            </button>

                            <button type="button"
                                    class="btn btn-outline-primary chart-tab"
                                    data-chart="weight">
                                <i class="fas fa-weight"></i>
                                体重
                            </button>

                            <button type="button"
                                    class="btn btn-outline-primary chart-tab"
                                    data-chart="circulation">
                                <i class="fas fa-heart"></i>
                                心拍・血圧
                            </button>

                            <button type="button"
                                    class="btn btn-outline-primary chart-tab"
                                    data-chart="spo2">
                                <i class="fas fa-lungs"></i>
                                SpO₂
                            </button>
                        </div>

                        {{-- ====================================================
                            体調
                            ==================================================== --}}
                        <div id="chart-condition"
                            class="condition-chart-panel">
                            <h6 class="condition-chart-title">
                                体温・体調レベル
                            </h6>
                            <div class="condition-chart-container">
                                <canvas id="conditionChart"></canvas>
                            </div>
                        </div>

                        {{-- ====================================================
                            体重
                            ==================================================== --}}
                        <div id="chart-weight"
                            class="condition-chart-panel"
                            style="display:none;">
                            <h6 class="condition-chart-title">
                                体重
                            </h6>
                            <div class="condition-chart-container">
                                <canvas id="weightChart"></canvas>
                            </div>
                        </div>

                        {{-- ====================================================
                            心拍・血圧
                            ==================================================== --}}
                        <div id="chart-circulation"
                            class="condition-chart-panel"
                            style="display:none;">
                            <h6 class="condition-chart-title">
                                安静時心拍数・血圧
                            </h6>
                            <div class="condition-chart-container">
                                <canvas id="circulationChart"></canvas>
                            </div>
                        </div>

                        {{-- ====================================================
                            SpO2
                            ==================================================== --}}
                        <div id="chart-spo2"
                            class="condition-chart-panel"
                            style="display:none;">
                            <h6 class="condition-chart-title">
                                血中酸素飽和度
                            </h6>
                            <div class="condition-chart-container">
                                <canvas id="spo2Chart"></canvas>
                            </div>
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

                                                        @if($dayData['data']->body_weight)
                                                            <span class="text-secondary small fw-semibold"
                                                                style="font-size: 0.75rem;">
                                                                ⚖️ {{ number_format($dayData['data']->body_weight, 1) }} kg
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
{{-- ============================================================
     Chart.js
     ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    /*
     * ============================================================
     * PHP → JavaScript
     * ============================================================
     */
    const labels = {!! json_encode($graphLabels) !!};
    const tempData = {!! json_encode($graphTemperatures) !!};
    const condData = {!! json_encode($graphConditions) !!};
    const weightData = {!! json_encode($graphWeights) !!};
    const heartRateData = {!! json_encode($graphHeartRates) !!};
    const systolicData = {!! json_encode($graphSystolic) !!};
    const diastolicData = {!! json_encode($graphDiastolic) !!};
    const spo2Data = {!! json_encode($graphSpo2) !!};

    if (labels.length === 0) {
        return;
    }

    /*
     * ============================================================
     * 共通設定
     * ============================================================
     */
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                enabled: true
            }
        },
        elements: {
            line: {
                tension: 0.25
            },
            point: {
                radius: 4,
                hoverRadius: 6
            }
        }
    };

    /*
     * ============================================================
     * 1. 体温・体調
     * ============================================================
     */
    const conditionCanvas =
        document.getElementById('conditionChart');
    if (conditionCanvas) {
        new Chart(
            conditionCanvas.getContext('2d'),
            {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '体温 (℃)',
                            data: tempData,
                            borderColor:
                                'rgb(255, 99, 132)',
                            backgroundColor:
                                'rgba(255, 99, 132, 0.1)',
                            yAxisID: 'y-temp',
                            spanGaps: true
                        },
                        {
                            label: '体調レベル',
                            data: condData,
                            borderColor:
                                'rgb(54, 162, 235)',
                            backgroundColor:
                                'rgba(54, 162, 235, 0.1)',
                            yAxisID: 'y-cond',
                            spanGaps: true
                        }
                    ]
                },

                options: {
                    ...commonOptions,
                    scales: {
                        'y-temp': {
                            type: 'linear',
                            position: 'left',
                            min: 35,
                            max: 42,
                            title: {
                                display: true,
                                text: '体温 (℃)'
                            }
                        },

                        'y-cond': {
                            type: 'linear',
                            position: 'right',
                            min: 1,
                            max: 5,
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: '体調レベル'
                            }
                        }
                    }
                }
            }
        );
    }

    /*
     * ============================================================
     * 2. 体重
     * ============================================================
     */
    const weightCanvas =
        document.getElementById('weightChart');
    if (weightCanvas) {
        new Chart(
            weightCanvas.getContext('2d'),
            {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '体重 (kg)',
                            data: weightData,
                            borderColor:
                                'rgb(75, 192, 192)',
                            backgroundColor:
                                'rgba(75, 192, 192, 0.1)',
                            fill: false,
                            spanGaps: true
                        }
                    ]
                },

                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            title: {
                                display: true,
                                text: '体重 (kg)'
                            }
                        }
                    }
                }
            }
        );
    }

    /*
     * ============================================================
     * 3. 心拍数・血圧
     * ============================================================
     */
    const circulationCanvas =
        document.getElementById('circulationChart');
    if (circulationCanvas) {
        new Chart(
            circulationCanvas.getContext('2d'),
            {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '最高血圧',
                            data: systolicData,
                            borderColor:
                                'rgb(255, 99, 132)',
                            backgroundColor:
                                'rgba(255, 99, 132, 0.1)',
                            yAxisID: 'y-pressure',
                            spanGaps: true
                        },

                        {
                            label: '最低血圧',
                            data: diastolicData,
                            borderColor:
                                'rgb(54, 162, 235)',
                            backgroundColor:
                                'rgba(54, 162, 235, 0.1)',
                            yAxisID: 'y-pressure',
                            spanGaps: true
                        },

                        {
                            label: '安静時心拍数',
                            data: heartRateData,
                            borderColor:
                                'rgb(255, 159, 64)',
                            backgroundColor:
                                'rgba(255, 159, 64, 0.1)',
                            yAxisID: 'y-heart',
                            borderDash: [5, 5],
                            spanGaps: true
                        }
                    ]
                },

                options: {
                    ...commonOptions,
                    scales: {
                        'y-pressure': {
                            type: 'linear',
                            position: 'left',
                            suggestedMin: 40,
                            suggestedMax: 160,
                            title: {
                                display: true,
                                text: '血圧 (mmHg)'
                            }
                        },

                        'y-heart': {
                            type: 'linear',
                            position: 'right',
                            suggestedMin: 40,
                            suggestedMax: 120,
                            grid: {
                                drawOnChartArea: false
                            },
                            title: {
                                display: true,
                                text: '心拍数 (bpm)'
                            }
                        }
                    }
                }
            }
        );
    }

    /*
     * ============================================================
     * 4. SpO2
     * ============================================================
     */
    const spo2Canvas =
        document.getElementById('spo2Chart');
    if (spo2Canvas) {
        new Chart(
            spo2Canvas.getContext('2d'),
            {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'SpO₂ (%)',
                            data: spo2Data,
                            borderColor:
                                'rgb(153, 102, 255)',
                            backgroundColor:
                                'rgba(153, 102, 255, 0.1)',
                            spanGaps: true
                        }
                    ]
                },

                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            min: 70,
                            max: 100,
                            ticks: {
                                stepSize: 5
                            },
                            title: {
                                display: true,
                                text: 'SpO₂ (%)'
                            }
                        }
                    }
                }
            }
        );
    }

    /*
     * ============================================================
     * グラフ切り替え
     * ============================================================
     */
    const chartTabs =
        document.querySelectorAll('.chart-tab');
    const chartPanels =
        document.querySelectorAll('.condition-chart-panel');

    chartTabs.forEach(function (button) {
        button.addEventListener(
            'click',
            function () {
                const target =
                    this.dataset.chart;

                /*
                 * 全グラフを非表示
                 */
                chartPanels.forEach(
                    function (panel) {
                        panel.style.display = 'none';
                    }
                );

                /*
                 * 選択グラフを表示
                 */
                const targetPanel =
                    document.getElementById(
                        'chart-' + target
                    );
                if (targetPanel) {
                    targetPanel.style.display =
                        'block';
                }

                /*
                 * ボタン表示
                 */
                chartTabs.forEach(
                    function (tab) {
                        tab.classList.remove(
                            'btn-primary'
                        );
                        tab.classList.remove(
                            'active'
                        );
                        tab.classList.add(
                            'btn-outline-primary'
                        );
                    }
                );

                this.classList.remove(
                    'btn-outline-primary'
                );
                this.classList.add(
                    'btn-primary'
                );
                this.classList.add(
                    'active'
                );
            }
        );
    });
});
</script>
@endsection
