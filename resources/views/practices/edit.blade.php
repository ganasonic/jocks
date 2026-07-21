@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">練習記録の編集</h2>
        <a href="{{ route('practices.index') }}" class="btn btn-outline-secondary">キャンセル</a>
    </div>

    <form action="{{ route('practices.update', $practice->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="card shadow-sm border-light-subtle mb-4">
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">練習日 <span class="text-danger">*</span></label>
                        <input type="date" name="practice_date" class="form-control" value="{{ old('practice_date', $practice->practice_date instanceof \DateTime ? $practice->practice_date->format('Y-m-d') : $practice->practice_date) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">種別・カテゴリ</label>
                        <input type="text" name="practice_type" class="form-control" list="type-options" value="{{ old('practice_type', $practice->practice_type) }}" autocomplete="off">
                        <datalist id="type-options">
                            <option value="雪上練習"><option value="ウォータージャンプ"><option value="トランポリン"><option value="陸上トレーニング"><option value="コーディネーション">
                        </datalist>
                    </div>

                    {{-- ここを「タイトル・場所」に戻しました --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">タイトル・場所</label>
                        <input type="text" name="title" class="form-control" placeholder="例: 白馬八方スキー場 / 〇〇体育館" value="{{ old('title', $practice->title) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">目標</label>
                    <textarea name="target" class="form-control" rows="2">{{ old('target', $practice->target) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-success">全体アドバイス・総評（コーチ）</label>
                    <textarea name="overall_coach_comment" class="form-control border-success-subtle bg-success-subtle" rows="3">{{ old('overall_coach_comment', $practice->overall_coach_comment) }}</textarea>
                </div>
            </div>
        </div>

        <h3 class="h5 fw-bold mb-3">練習メニュー</h3>
        <div id="details-container">
            @foreach($practice->details as $index => $detail)
            <div class="card shadow-sm border-light-subtle mb-3 detail-item">
                <div class="card-body p-3">
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold">メニュー名 <span class="text-danger">*</span></label>
                            <input type="text" name="details[{{ $index }}][menu_name]" class="form-control form-control-sm" value="{{ old("details.{$index}.menu_name", $detail->menu_name) }}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-bold">本数/時間</label>
                            <select name="details[{{ $index }}][runs_or_time]" class="form-select form-select-sm">
                                <option value="">選択</option>
                                @foreach(['1本', '3本', '5本', '10本', '15本', '20本', '5分', '10分', '15分', '20分', '30分', '60分'] as $val)
                                    <option value="{{ $val }}" {{ old("details.{$index}.runs_or_time", $detail->runs_or_time) == $val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-7 fw-bold">本人達成度</label>
                            <select name="details[{{ $index }}][rating]" class="form-select form-select-sm">
                                <option value="">選択なし</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old("details.{$index}.rating", $detail->rating) == $i ? 'selected' : '' }}>{{ $i }} ({{ str_repeat('★', $i) }})</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-7 fw-bold text-primary">コーチ達成度</label>
                            <select name="details[{{ $index }}][coach_rating]" class="form-select form-select-sm border-primary">
                                <option value="">選択なし</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old("details.{$index}.coach_rating", $detail->coach_rating) == $i ? 'selected' : '' }}>{{ $i }} ({{ str_repeat('★', $i) }})</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6"><label class="form-label fs-7">感触・自己評価</label><textarea name="details[{{ $index }}][impression]" class="form-control form-control-sm" rows="2">{{ old("details.{$index}.impression", $detail->impression) }}</textarea></div>
                        <div class="col-md-6"><label class="form-label fs-7">課題・反省</label><textarea name="details[{{ $index }}][notice]" class="form-control form-control-sm" rows="2">{{ old("details.{$index}.notice", $detail->notice) }}</textarea></div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-8"><label class="form-label fs-7 text-success fw-bold">アドバイス（コーチコメント）</label><textarea name="details[{{ $index }}][coach_comment]" class="form-control form-control-sm border-success-subtle bg-success-subtle bg-opacity-10" rows="2">{{ old("details.{$index}.coach_comment", $detail->coach_comment) }}</textarea></div>
                        <div class="col-md-4"><label class="form-label fs-7">動画URL</label><input type="url" name="details[{{ $index }}][video_url]" class="form-control form-control-sm" value="{{ old("details.{$index}.video_url", $detail->video_url) }}"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" id="add-detail-btn" class="btn btn-outline-primary fw-bold mb-4">＋ メニュー追加</button>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg fw-bold">更新する</button>
        </div>
    </form>
</div>

<script>
    let detailIndex = {{ count($practice->details) }};
    document.getElementById('add-detail-btn').addEventListener('click', function() {
        const container = document.getElementById('details-container');
        const template = container.firstElementChild.cloneNode(true);
        template.querySelectorAll('input, textarea, select').forEach(el => {
            el.name = el.name.replace(/details\[\d+\]/, `details[${detailIndex}]`);
            el.value = '';
        });
        container.appendChild(template);
        detailIndex++;
    });
</script>
@endsection
