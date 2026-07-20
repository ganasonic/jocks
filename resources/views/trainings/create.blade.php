@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h2 class="mb-4">トレーニング入力</h2>

    <form action="{{ route('trainings.store') }}" method="POST">
        @csrf

        <!-- ヘッダー情報（日付とワークアウト名） -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <label class="font-weight-bold">実施日</label>
                        <input type="date" name="training_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12 col-md-8">
                        <label class="font-weight-bold">ワークアウト名</label>
                        <input type="text" name="title" class="form-control" placeholder="例: フィジカルトレーニング、脚トレなど" value="フィジカルトレーニング">
                    </div>
                </div>
            </div>
        </div>

        <!-- 種目カードエリア -->
        <div id="details-container">
            <div class="card shadow-sm mb-4 detail-item">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge badge-primary px-3 py-2">種目 #1</span>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-btn" onclick="removeRow(this)" style="display:none;">削除</button>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold">種目名</label>
                        <input type="text" name="details[0][exercise_name]" class="form-control form-control-lg" list="exercise-list" placeholder="例: ベンチプレス" required>
                    </div>

                    <div class="row">
                        <div class="col-6 col-md-2 mb-3">
                            <label class="small text-muted">重量 (kg)</label>
                            <input type="number" name="details[0][weight]" class="form-control" step="0.5" placeholder="0.0">
                        </div>
                        <div class="col-6 col-md-2 mb-3">
                            <label class="small text-muted">回数</label>
                            <select name="details[0][reps]" class="form-control">
                                <option value="">選択</option>
                                @for($i = 1; $i <= 30; $i++)
                                    <option value="{{ $i }}">{{ $i }} 回</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6 col-md-2 mb-3">
                            <label class="small text-muted">セット数</label>
                            <select name="details[0][sets]" class="form-control">
                                <option value="">選択</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }} セット</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label class="small text-muted">セット間インターバル</label>
                            <select name="details[0][interval]" class="form-control">
                                <option value="">選択なし</option>
                                <option value="30">30秒</option>
                                <option value="60">60秒 (1分)</option>
                                <option value="90">90秒 (1分半)</option>
                                <option value="120">120秒 (2分)</option>
                                <option value="180">180秒 (3分)</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label class="small text-muted">きつさ (RPE)</label>
                            <select name="details[0][rpe]" class="form-control">
                                <option value="">選択</option>
                                <option value="10">10 (限界)</option>
                                <option value="9">9 (あと1回可能)</option>
                                <option value="8">8 (あと2回可能)</option>
                                <option value="7">7 (余裕あり)</option>
                                <option value="6">6 (かなり軽い)</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-2 bg-light p-2 rounded">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-6 mb-1 mb-md-0">
                                <span class="small font-weight-bold text-secondary">
                                    次の種目までの休憩時間（種目間休憩）
                                </span>
                            </div>
                            <div class="col-12 col-md-6">
                                <select name="details[0][rest_after_exercise]" class="form-control form-control-sm">
                                    <option value="">指定なし / 最後の種目</option>
                                    <option value="60">1分</option>
                                    <option value="120">2分</option>
                                    <option value="180">3分</option>
                                    <option value="240">4分</option>
                                    <option value="300">5分</option>
                                    <option value="600">10分</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <datalist id="exercise-list">
            @foreach($recentExercises as $ex)
                <option value="{{ $ex }}">
            @endforeach
        </datalist>

        <div class="d-flex justify-content-between mt-4 mb-5">
            <button type="button" class="btn btn-outline-secondary btn-lg" onclick="addRow()">
                ＋ 種目を追加
            </button>
            <button type="submit" class="btn btn-primary btn-lg px-5">
                保存する
            </button>
        </div>
    </form>
</div>

<script>
    let rowCount = 1;

    function addRow() {
        let container = document.getElementById('details-container');
        let cardHtml = `
        <div class="card shadow-sm mb-4 detail-item">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge badge-primary px-3 py-2">種目 #${rowCount + 1}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">削除</button>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold">種目名</label>
                    <input type="text" name="details[${rowCount}][exercise_name]" class="form-control form-control-lg" list="exercise-list" placeholder="例: スクワット" required>
                </div>
                <div class="row">
                    <div class="col-6 col-md-2 mb-3">
                        <label class="small text-muted">重量 (kg)</label>
                        <input type="number" name="details[${rowCount}][weight]" class="form-control" step="0.5" placeholder="0.0">
                    </div>
                    <div class="col-6 col-md-2 mb-3">
                        <label class="small text-muted">回数</label>
                        <select name="details[${rowCount}][reps]" class="form-control">
                            <option value="">選択</option>
                            ${generateOptions(1, 30, '回')}
                        </select>
                    </div>
                    <div class="col-6 col-md-2 mb-3">
                        <label class="small text-muted">セット数</label>
                        <select name="details[${rowCount}][sets]" class="form-control">
                            <option value="">選択</option>
                            ${generateOptions(1, 10, 'セット')}
                        </select>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <label class="small text-muted">セット間インターバル</label>
                        <select name="details[${rowCount}][interval]" class="form-control">
                            <option value="">選択なし</option>
                            <option value="30">30秒</option>
                            <option value="60">60秒 (1分)</option>
                            <option value="90">90秒 (1分半)</option>
                            <option value="120">120秒 (2分)</option>
                            <option value="180">180秒 (3分)</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <label class="small text-muted">きつさ (RPE)</label>
                        <select name="details[${rowCount}][rpe]" class="form-control">
                            <option value="">選択</option>
                            <option value="10">10 (限界)</option>
                            <option value="9">9 (あと1回可能)</option>
                            <option value="8">8 (あと2回可能)</option>
                            <option value="7">7 (余裕あり)</option>
                            <option value="6">6 (かなり軽い)</option>
                        </select>
                    </div>
                </div>
                <div class="border-top pt-3 mt-2 bg-light p-2 rounded">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 mb-1 mb-md-0">
                            <span class="small font-weight-bold text-secondary">次の種目までの休憩時間（種目間休憩）</span>
                        </div>
                        <div class="col-12 col-md-6">
                            <select name="details[${rowCount}][rest_after_exercise]" class="form-control form-control-sm">
                                <option value="">指定なし / 最後の種目</option>
                                <option value="60">1分</option>
                                <option value="120">2分</option>
                                <option value="180">3分</option>
                                <option value="240">4分</option>
                                <option value="300">5分</option>
                                <option value="600">10分</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

        container.insertAdjacentHTML('beforeend', cardHtml);
        updateRemoveButtons();
        rowCount++;
    }

    function removeRow(btn) {
        let card = btn.closest('.detail-item');
        card.remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        let items = document.querySelectorAll('.detail-item');
        items.forEach((item) => {
            let removeBtn = item.querySelector('.remove-btn');
            if (removeBtn) {
                removeBtn.style.display = (items.length > 1) ? 'block' : 'none';
            }
        });
    }

    function generateOptions(min, max, unit) {
        let options = '';
        for (let i = min; i <= max; i++) {
            options += `<option value="${i}">${i} ${unit}</option>`;
        }
        return options;
    }
</script>
@endsection
