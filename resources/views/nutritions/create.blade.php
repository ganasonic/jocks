@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">栄養管理 新規登録</h1>

            <form action="{{ route('nutritions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- 親データ -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>日付</label>
                            <input type="date" name="nutrition_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>日次メモ</label>
                            <textarea name="daily_memo" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- 初回の食事詳細 -->
                <h3>初回の食事登録</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>食事種別</label>
                                <select name="meal_type" class="form-control" required>
                                    <option value="朝食">朝食</option>
                                    <option value="昼食">昼食</option>
                                    <option value="夕食">夕食</option>
                                    <option value="夜食">夜食</option>
                                    <option value="その他">その他</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>タンパク質 (g)</label>
                                <input type="number" name="protein" class="form-control" placeholder="例: 20">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label>カロリー (kcal)</label>
                            <input type="number" name="calories" class="form-control" placeholder="例: 500">
                        </div>

                        <div class="form-group mb-3">
                            <label>写真</label>
                            <input type="file" name="photo" class="form-control-file border p-2 w-100">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg w-100">登録する</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
