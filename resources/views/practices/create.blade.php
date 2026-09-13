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
        <input type="hidden" name="player_id" value="{{ auth()->user()->targetPlayerId() }}">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

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
                    <textarea name="feedback" class="form-control border-success-subtle bg-success-subtle" rows="3" placeholder="全体に対するアドバイスや総評を入力">{{ old('feedback') }}</textarea>
                </div>
            </div>
        </div>

        <h3 class="h5 fw-bold mb-3">練習メニュー</h3>
        @php($detailRows = old('details', [['menu_name' => '']]))
        <div id="details-container">
            @foreach($detailRows as $index => $row)
                @include('practices.partials.detail-form', ['index' => $index, 'row' => $row])
            @endforeach
        </div>
        <template id="detail-template">
            @include('practices.partials.detail-form', ['index' => '__INDEX__', 'row' => []])
        </template>

        <button type="button" id="add-detail-btn" class="btn btn-outline-primary fw-bold mb-4">＋ メニュー追加</button>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg fw-bold">保存する</button>
        </div>
    </form>
</div>

@include('practices.partials.upload-script')
@endsection
