@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">食事記録の編集</h1>

            <form action="{{ route('nutritions.update', $nutrition->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- 親データ -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label>日付</label>
                            <input type="date" name="nutrition_date" class="form-control" value="{{ $nutrition->nutrition_date }}">
                        </div>
                        <div class="form-group">
                            <label>コーチのコメント</label>
                            <textarea name="coach_comment" class="form-control" rows="3">{{ $nutrition->coach_comment }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 既存の詳細一覧 -->
                @foreach($nutrition->details as $detail)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label>種別</label>
                                <select name="details[{{ $detail->id }}][meal_type]" class="form-control">
                                    @foreach(['朝食', '昼食', '夕食', '夜食', 'その他'] as $type)
                                        <option value="{{ $type }}" {{ $detail->meal_type == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2"><label>カロリー</label><input type="number" name="details[{{ $detail->id }}][calories]" class="form-control" value="{{ $detail->calories }}"></div>
                            <div class="col-md-2"><label>タンパク質</label><input type="number" name="details[{{ $detail->id }}][protein]" class="form-control" value="{{ $detail->protein }}"></div>
                            <div class="col-md-5">
                                <label>選手コメント</label>
                                <textarea name="details[{{ $detail->id }}][meal_memo]" class="form-control" rows="1">{{ $detail->meal_memo }}</textarea>
                            </div>                        </div>

                        <div class="form-group mt-2">
                            <label>写真</label>
                            @if($detail->photo_path)
                                <div class="mb-2"><img src="{{ asset('storage/' . $detail->photo_path) }}" style="max-width: 150px;"></div>
                            @endif
                            <input type="file" name="details[{{ $detail->id }}][photo]" class="form-control">
                        </div>

                        <div class="form-group mt-2">
                            <label>コーチのコメント</label>
                            <textarea name="details[{{ $detail->id }}][meal_coach_comment]" class="form-control">{{ $detail->meal_coach_comment }}</textarea>
                        </div>

                        <div class="mt-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteDetail({{ $detail->id }})">この食事を削除</button>
                        </div>
                    </div>
                </div>
                @endforeach

                <button type="submit" class="btn btn-primary mt-3 w-100">更新を保存</button>
            </form>

            <!-- 新規追加への導線 -->
            <div class="mt-4 text-center">
                <a href="{{ route('nutritions.details.create', $nutrition->id) }}" class="btn btn-success">新しい食事を追加</a>
                <a href="{{ route('nutritions.index') }}" class="btn btn-secondary">一覧へ戻る</a>
            </div>

            <!-- 削除用フォーム -->
            <form id="delete-form" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>

            <script>
                function deleteDetail(id) {
                    if (confirm('この食事記録を削除しますか？')) {
                        const form = document.getElementById('delete-form');
                        form.action = '/nutritions/details/' + id;
                        form.submit();
                    }
                }
            </script>
        </div>
    </div>
</div>
@endsection
