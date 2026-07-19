@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>トレーニング入力</h2>

    <!-- actionをroute関数に変更して404を確実に回避 -->
    <form action="{{ route('trainings.store') }}" method="POST">
        @csrf
        <div class="form-group mb-4">
            <label>日付</label>
            <input type="date" name="training_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <table class="table" id="training-table">
            <thead>
                <tr>
                    <th>種目名</th>
                    <th>重量(kg)</th>
                    <th>回数</th>
                    <th>セット数</th>
                    <th>インターバル(秒)</th>
                    <th>きつさ(RPE)</th>
                </tr>
            </thead>
            <tbody id="details-body">
                <tr>
                    <td>
                        <input type="text" name="details[0][exercise_name]" class="form-control" list="exercise-list">
                    </td>
                    <td><input type="number" name="details[0][weight]" class="form-control" step="0.5"></td>
                    <td><input type="number" name="details[0][reps]" class="form-control"></td>
                    <td><input type="number" name="details[0][sets]" class="form-control"></td>
                    <td><input type="number" name="details[0][interval]" class="form-control"></td>
                    <td><input type="number" name="details[0][rpe]" class="form-control" min="1" max="10" placeholder="1〜10"></td>
                </tr>
            </tbody>
        </table>

        <!-- 過去の種目リスト（pluck形式の配列に合わせたループ修正） -->
        <datalist id="exercise-list">
            @foreach($recentExercises as $ex)
                <option value="{{ $ex }}">
            @endforeach
        </datalist>

        <div class="mt-3">
            <button type="button" class="btn btn-secondary" onclick="addRow()">行を追加</button>
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </form>
</div>

<script>
    let rowCount = 1;
    function addRow() {
        let tbody = document.getElementById('details-body');
        let tr = `<tr>
            <td><input type="text" name="details[${rowCount}][exercise_name]" class="form-control" list="exercise-list"></td>
            <td><input type="number" name="details[${rowCount}][weight]" class="form-control" step="0.5"></td>
            <td><input type="number" name="details[${rowCount}][reps]" class="form-control"></td>
            <td><input type="number" name="details[${rowCount}][sets]" class="form-control"></td>
            <td><input type="number" name="details[${rowCount}][interval]" class="form-control"></td>
            <td><input type="number" name="details[${rowCount}][rpe]" class="form-control" min="1" max="10" placeholder="1〜10"></td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', tr);
        rowCount++;
    }
</script>
@endsection
