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

                        {{-- =========================================================
                            身体データ
                            ========================================================= --}}
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h5 class="mb-3">
                                    <i class="fas fa-heartbeat text-danger"></i>
                                    身体データ
                                </h5>

                                {{-- 体重 --}}
                                <div class="form-group mb-3">
                                    <label for="body_weight">
                                        体重 (kg)
                                    </label>
                                    <input
                                        type="number"
                                        name="body_weight"
                                        id="body_weight"
                                        class="form-control"
                                        value="{{ old('body_weight', $condition->body_weight) }}"
                                        min="20"
                                        max="250"
                                        step="0.1"
                                        inputmode="decimal"
                                    >
                                </div>

                                <div class="row">
                                    {{-- 心拍 --}}
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="resting_heart_rate">
                                            安静時心拍数 (bpm)
                                        </label>
                                        <input
                                            type="number"
                                            name="resting_heart_rate"
                                            id="resting_heart_rate"
                                            class="form-control"
                                            value="{{ old('resting_heart_rate', $condition->resting_heart_rate) }}"
                                            min="30"
                                            max="220"
                                        >
                                    </div>

                                    {{-- SpO2 --}}
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="spo2">
                                            血中酸素飽和度 SpO₂ (%)
                                        </label>
                                        <input
                                            type="number"
                                            name="spo2"
                                            id="spo2"
                                            class="form-control"
                                            value="{{ old('spo2', $condition->spo2) }}"
                                            min="70"
                                            max="100"
                                        >
                                    </div>
                                </div>

                                {{-- 血圧 --}}
                                <label>
                                    血圧 (mmHg)
                                </label>
                                <div class="row">
                                    <div class="col-6">
                                        <input
                                            type="number"
                                            name="systolic_blood_pressure"
                                            class="form-control"
                                            value="{{ old(
                                                'systolic_blood_pressure',
                                                $condition->systolic_blood_pressure
                                            ) }}"
                                            min="60"
                                            max="250"
                                            placeholder="上"
                                        >
                                        <small class="text-muted">
                                            収縮期（上）
                                        </small>
                                    </div>

                                    <div class="col-6">
                                        <input
                                            type="number"
                                            name="diastolic_blood_pressure"
                                            class="form-control"
                                            value="{{ old(
                                                'diastolic_blood_pressure',
                                                $condition->diastolic_blood_pressure
                                            ) }}"
                                            min="30"
                                            max="150"
                                            placeholder="下"
                                        >
                                        <small class="text-muted">
                                            拡張期（下）
                                        </small>
                                    </div>
                                </div>
                            </div>
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

                        @if(
                            $targetUser->userDetail &&
                            $targetUser->userDetail->gender === 'female'
                        )
                            <div class="card border-danger mb-4">
                                <div class="card-header bg-white">
                                    <strong>
                                        <i class="fas fa-venus text-danger"></i>
                                        女性コンディション
                                    </strong>
                                </div>

                                <div class="card-body">
                                    {{-- 生理中 --}}
                                    <div class="form-group mb-3">
                                        <label for="menstruation">
                                            現在、生理中ですか？
                                        </label>
                                        <select
                                            name="menstruation"
                                            id="menstruation"
                                            class="form-control"
                                        >
                                            <option value="">
                                                未入力
                                            </option>
                                            <option value="0"
                                                {{ old('menstruation', $condition->menstruation) !== null &&
                                                (string) old('menstruation', $condition->menstruation) === '0'
                                                ? 'selected' : '' }}>
                                                いいえ
                                            </option>
                                            <option value="1"
                                                {{ (string) old('menstruation', $condition->menstruation) === '1'
                                                ? 'selected' : '' }}>
                                                はい
                                            </option>
                                        </select>
                                    </div>

                                    {{-- 開始日 --}}
                                    <div class="form-group mb-3">
                                        <label for="menstruation_start_date">
                                            今回の生理開始日
                                        </label>
                                        <input
                                            type="date"
                                            name="menstruation_start_date"
                                            id="menstruation_start_date"
                                            class="form-control"
                                            value="{{ old(
                                                'menstruation_start_date',
                                                $condition->menstruation_start_date
                                            ) }}"
                                        >
                                    </div>

                                    {{-- 状態 --}}
                                    <div class="form-group mb-3">
                                        <label for="menstruation_condition">
                                            生理によるコンディション
                                        </label>
                                        <select
                                            name="menstruation_condition"
                                            id="menstruation_condition"
                                            class="form-control"
                                        >
                                            <option value="">
                                                未入力
                                            </option>
                                            @for($i = 5; $i >= 1; $i--)
                                                <option
                                                    value="{{ $i }}"
                                                    {{ old(
                                                        'menstruation_condition',
                                                        $condition->menstruation_condition
                                                    ) == $i ? 'selected' : '' }}
                                                >
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    {{-- メモ --}}
                                    <div class="form-group">
                                        <label for="menstruation_memo">
                                            生理に関するメモ
                                        </label>
                                        <textarea
                                            name="menstruation_memo"
                                            id="menstruation_memo"
                                            class="form-control"
                                            rows="3"
                                            maxlength="1000"
                                        >{{ old(
                                            'menstruation_memo',
                                            $condition->menstruation_memo
                                        ) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif

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
