@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">本日の体調を記録</div>

                <div class="card-content p-4">
                    {{-- エラー表示 --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('conditions.store') }}" method="POST">
                        @csrf

                        {{-- 日付 --}}
                        <div class="form-group mb-3">
                            <label for="date">記録日</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $date ?: date('Y-m-d')) }}" required>
                        </div>

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
                                    @endphp

                                    <option
                                        value="{{ $temperature }}"
                                        {{ old('body_temperature', '36.4') == $temperature ? 'selected' : '' }}
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
                                        class="form-control @error('body_weight') is-invalid @enderror"
                                        value="{{ old('body_weight') }}"
                                        min="20"
                                        max="250"
                                        step="0.1"
                                        inputmode="decimal"
                                        placeholder="例：65.5"
                                    >
                                    @error('body_weight')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="row">
                                    {{-- 心拍数 --}}
                                    <div class="col-md-6 form-group mb-3">
                                        <label for="resting_heart_rate">
                                            安静時心拍数 (bpm)
                                        </label>
                                        <input
                                            type="number"
                                            name="resting_heart_rate"
                                            id="resting_heart_rate"
                                            class="form-control"
                                            value="{{ old('resting_heart_rate') }}"
                                            min="30"
                                            max="220"
                                            step="1"
                                            inputmode="numeric"
                                            placeholder="例：60"
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
                                            value="{{ old('spo2') }}"
                                            min="70"
                                            max="100"
                                            step="1"
                                            inputmode="numeric"
                                            placeholder="例：98"
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
                                            id="systolic_blood_pressure"
                                            class="form-control"
                                            value="{{ old('systolic_blood_pressure') }}"
                                            min="60"
                                            max="250"
                                            step="1"
                                            inputmode="numeric"
                                            placeholder="上 例：120"
                                        >
                                        <small class="text-muted">
                                            収縮期（上）
                                        </small>
                                    </div>

                                    <div class="col-6">
                                        <input
                                            type="number"
                                            name="diastolic_blood_pressure"
                                            id="diastolic_blood_pressure"
                                            class="form-control"
                                            value="{{ old('diastolic_blood_pressure') }}"
                                            min="30"
                                            max="150"
                                            step="1"
                                            inputmode="numeric"
                                            placeholder="下 例：70"
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
                                <option value="5" {{ old('condition_level') == '5' ? 'selected' : '' }}>5 - 絶好調</option>
                                <option value="4" {{ old('condition_level') == '4' ? 'selected' : '' }}>4 - 良い</option>
                                <option value="3" {{ old('condition_level', '3') == '3' ? 'selected' : '' }}>3 - 普通</option>
                                <option value="2" {{ old('condition_level') == '2' ? 'selected' : '' }}>2 - 微妙</option>
                                <option value="1" {{ old('condition_level') == '1' ? 'selected' : '' }}>1 - 最悪</option>
                            </select>
                        </div>

                        {{-- 気分レベル --}}
                        <div class="form-group mb-3">
                            <label for="mood_level">気分 (5が最高、1が最低)</label>
                            <select name="mood_level" id="mood_level" class="form-control" required>
                                <option value="5" {{ old('mood_level') == '5' ? 'selected' : '' }}>5 - 最高</option>
                                <option value="4" {{ old('mood_level') == '4' ? 'selected' : '' }}>4 - 良い</option>
                                <option value="3" {{ old('mood_level', '3') == '3' ? 'selected' : '' }}>3 - 普通</option>
                                <option value="2" {{ old('mood_level') == '2' ? 'selected' : '' }}>2 - モヤモヤ</option>
                                <option value="1" {{ old('mood_level') == '1' ? 'selected' : '' }}>1 - 最悪</option>
                            </select>
                        </div>

                        {{-- 時間 (起床・就寝) --}}
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="wakeup_time">起床時間</label>
                                <input type="time" name="wakeup_time" id="wakeup_time" class="form-control" value="{{ old('wakeup_time') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="bedtime">就寝時間</label>
                                <input type="time" name="bedtime" id="bedtime" class="form-control" value="{{ old('bedtime') }}">
                            </div>
                        </div>

                        {{-- =========================================================
                            女性コンディション
                            ========================================================= --}}
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
                                            <option
                                                value="0"
                                                {{ old('menstruation') === '0' ? 'selected' : '' }}
                                            >
                                                いいえ
                                            </option>
                                            <option
                                                value="1"
                                                {{ old('menstruation') === '1' ? 'selected' : '' }}
                                            >
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
                                            value="{{ old('menstruation_start_date') }}"
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
                                            <option value="5"
                                                {{ old('menstruation_condition') == '5' ? 'selected' : '' }}>
                                                5 - 影響なし
                                            </option>
                                            <option value="4"
                                                {{ old('menstruation_condition') == '4' ? 'selected' : '' }}>
                                                4 - ほぼ影響なし
                                            </option>
                                            <option value="3"
                                                {{ old('menstruation_condition') == '3' ? 'selected' : '' }}>
                                                3 - 少し影響あり
                                            </option>
                                            <option value="2"
                                                {{ old('menstruation_condition') == '2' ? 'selected' : '' }}>
                                                2 - かなり影響あり
                                            </option>
                                            <option value="1"
                                                {{ old('menstruation_condition') == '1' ? 'selected' : '' }}>
                                                1 - 非常につらい
                                            </option>
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
                                            placeholder="痛み、だるさ、その他気になることなど"
                                        >{{ old('menstruation_memo') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- 食事メモ --}}
                        <div class="form-group mb-4">
                            <label for="meals_memo">食事・メモ</label>
                            <textarea name="meals_memo" id="meals_memo" class="form-control" rows="3" placeholder="食べたものや気になった症状など">{{ old('meals_memo') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">記録する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
