@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>体調記録の編集</span>
                    <span class="fw-bold text-primary">{{ $condition->date }}</span>
                </div>

                <div class="card-content p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 送信先をPATCHメソッドに指定 --}}
                    <form action="{{ route('conditions.update', ['date' => $condition->date]) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- 体温 --}}
                        <div class="form-group mb-3">
                            <label for="body_temperature">体温 (°C)</label>
                            <select
                                name="body_temperature"
                                id="body_temperature"
                                class="form-control @error('body_temperature') is-invalid @enderror"
                            >
                                @for ($i = 350; $i <= 420; $i++)
                                    @php
                                        $temperature = number_format($i / 10, 1, '.', '');
                                        $selectedTemperature = number_format(
                                            (float) old('body_temperature', $condition->body_temperature),
                                            1,
                                            '.',
                                            ''
                                        );
                                    @endphp

                                    <option
                                        value="{{ $temperature }}"
                                        {{ $selectedTemperature == $temperature ? 'selected' : '' }}
                                    >
                                        {{ $temperature }} ℃
                                    </option>
                                @endfor
                            </select>

                            @error('body_temperature')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- 体調レベル --}}
                        <div class="form-group mb-3">
                            <label for="condition_level">体調 (5が最高、1が最低)</label>
                            <select name="condition_level" id="condition_level" class="form-control" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ old('condition_level', $condition->condition_level) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- 気分レベル --}}
                        <div class="form-group mb-3">
                            <label for="mood_level">気分 (5が最高、1が最低)</label>
                            <select name="mood_level" id="mood_level" class="form-control" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ old('mood_level', $condition->mood_level) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- 時間 --}}
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="wakeup_time">起床時間</label>
                                <input type="time" name="wakeup_time" id="wakeup_time" class="form-control" value="{{ old('wakeup_time', $condition->wakeup_time ? \Carbon\Carbon::parse($condition->wakeup_time)->format('H:i') : '') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="bedtime">就寝時間</label>
                                <input type="time" name="bedtime" id="bedtime" class="form-control" value="{{ old('bedtime', $condition->bedtime ? \Carbon\Carbon::parse($condition->bedtime)->format('H:i') : '') }}">
                            </div>
                        </div>

                        {{-- 食事メモ --}}
                        <div class="form-group mb-4">
                            <label for="meals_memo">食事・メモ</label>
                            <textarea name="meals_memo" id="meals_memo" class="form-control" rows="3">{{ old('meals_memo', $condition->meals_memo) }}</textarea>
                        </div>

                        <hr class="my-4">

                        <div class="form-group mb-3">
                            <label for="feedback">
                                <strong>スタッフフィードバック</strong>
                            </label>

                            @if(Auth::user()->isStaff())

                                <textarea
                                    name="feedback"
                                    id="feedback"
                                    class="form-control @error('feedback') is-invalid @enderror"
                                    rows="4"
                                    maxlength="2000"
                                    placeholder="選手のコンディションについてフィードバックを入力してください"
                                >{{ old('feedback', $condition->feedback) }}</textarea>

                                @error('feedback')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                @if($condition->feedback_by && $condition->feedbackUser)
                                    <small class="text-muted">
                                        前回入力：
                                        {{ $condition->feedbackUser->name }}
                                    </small>
                                @endif

                            @else

                                @if($condition->feedback)
                                    <div class="form-control bg-light"
                                        style="height: auto; min-height: 80px;">
                                        {!! nl2br(e($condition->feedback)) !!}
                                    </div>

                                    @if($condition->feedback_by && $condition->feedbackUser)
                                        <small class="text-muted">
                                            {{ $condition->feedbackUser->name }} より
                                        </small>
                                    @endif
                                @else
                                    <div class="form-control bg-light text-muted"
                                        style="height: auto; min-height: 80px;">
                                        フィードバックはありません。
                                    </div>
                                @endif

                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('conditions.index') }}" class="btn btn-outline-secondary w-50">キャンセル</a>
                            <button type="submit" class="btn btn-success w-50">更新する</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
