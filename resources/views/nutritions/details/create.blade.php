@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">新しい食事の追加</h1>

            <form action="{{ route('nutritions.details.store', $nutrition->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>種別</label>
                            <select name="meal_type" class="form-control" required>
                                <option value="朝食">朝食</option>
                                <option value="昼食">昼食</option>
                                <option value="夕食">夕食</option>
                                <option value="夜食">夜食</option>
                                <option value="その他">その他</option>
                            </select>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>カロリー (kcal)</label>
                                <input type="number" name="calories" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label>タンパク質 (g)</label>
                                <input type="number" name="protein" class="form-control">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <!-- meal_memo カラムを使用 -->
                            <label>選手メモ（選手コメント）</label>
                            <textarea name="meal_memo" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>写真</label>
                            <input type="file" name="photo" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary w-100">追加を保存</button>
                    <a href="{{ route('nutritions.edit', $nutrition->id) }}" class="btn btn-secondary w-100 mt-2">戻る</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
