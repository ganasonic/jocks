@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">新規練習記録</h2>
        <a href="{{ route('practices.index') }}" class="btn btn-outline-secondary">
            キャンセル
        </a>
    </div>

    <form action="{{ route('practices.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-light-subtle mb-4">
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">練習日 <span class="text-danger">*</span></label>
                        <input type="date" name="practice_date" class="form-control" value="{{ old('practice_date', date('Y-m-d')) }}" required>
                    </div>

                    {{-- 種別・カテゴリ --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">種別・カテゴリ</label>
                        <input type="text" name="practice_type" class="form-control" list="type-options" placeholder="直接入力または選択" value="{{ old('practice_type') }}" autocomplete="off">
                        <datalist id="type-options">
                            <option value="雪上練習"><option value="ウォータージャンプ"><option value="トランポリン"><option value="陸上トレーニング"><option value="コーディネーション">
                            @foreach($recentTypes ?? [] as $type)
                                @if(!in_array($type, ['雪上練習', 'ウォータージャンプ', 'トランポリン', '陸上トレーニング', 'コーディネーション']))
                                    <option value="{{ $type }}">
                                @endif
                            @endforeach
                        </datalist>
                    </div>

                    {{-- 「場所」を「タイトル・場所」に変更 --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">タイトル・場所</label>
                        <input type="text" name="title" class="form-control" placeholder="例: 白馬八方スキー場" value="{{ old('title') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">目標</label>
                    <textarea name="target" class="form-control" rows="2" placeholder="本日の目標を入力">{{ old('target') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-success">全体アドバイス・総評（コーチ）</label>
                    <textarea name="overall_coach_comment" class="form-control border-success-subtle bg-success-subtle" rows="3" placeholder="全体に対するアドバイスや総評を入力">{{ old('overall_coach_comment') }}</textarea>
                </div>
            </div>
        </div>

        <h3 class="h5 fw-bold mb-3">練習メニュー</h3>
        <div id="details-container">
            <div class="card shadow-sm border-light-subtle mb-3 detail-item">
                <div class="card-body p-3">
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fs-7 fw-bold">メニュー名 <span class="text-danger">*</span></label>
                            <input type="text" name="details[0][menu_name]" class="form-control form-control-sm" list="menu-options" placeholder="直接入力または選択" required autocomplete="off">
                            <datalist id="menu-options">
                                <option value="フラット"><option value="ライン取り"><option value="ポジション"><option value="フラット・カービング"><option value="スピード"><option value="コーク720"><option value="フルツイスト"><option value="グラブ練習">
                            </datalist>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-bold">本数/時間</label>
                            <select name="details[0][runs_or_time]" class="form-select form-select-sm">
                                <option value="">選択</option>
                                @foreach(['1本', '3本', '5本', '10本', '15分', '30分', '60分'] as $val)
                                    <option value="{{ $val }}">{{ $val }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fs-7 fw-bold">本人達成度</label>
                            <select name="details[0][rating]" class="form-select form-select-sm">
                                <option value="">選択なし</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} ({{ str_repeat('★', $i) }})</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-7 fw-bold text-primary">コーチ達成度</label>
                            <select name="details[0][coach_rating]" class="form-select form-select-sm border-primary">
                                <option value="">選択なし</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} ({{ str_repeat('★', $i) }})</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6"><label class="form-label fs-7">感触・自己評価</label><textarea name="details[0][impression]" class="form-control form-control-sm" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="form-label fs-7">課題・反省</label><textarea name="details[0][notice]" class="form-control form-control-sm" rows="2"></textarea></div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-8"><label class="form-label fs-7 text-success fw-bold">アドバイス（コーチコメント）</label><textarea name="details[0][coach_comment]" class="form-control form-control-sm border-success-subtle bg-success-subtle bg-opacity-10" rows="2"></textarea></div>
                        <div class="col-md-4"><label class="form-label fs-7">動画URL</label><input type="url" name="details[0][video_url]" class="form-control form-control-sm" placeholder="https://..."></div>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" id="add-detail-btn" class="btn btn-outline-primary fw-bold mb-4">＋ メニュー追加</button>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg fw-bold">保存する</button>
        </div>
    </form>
</div>

<script>
    let detailIndex = 1;
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
