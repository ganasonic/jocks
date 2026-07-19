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
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>

                        {{-- 体温 --}}
                        <div class="form-group mb-3">
                            <label for="body_temperature">体温 (°C)</label>
                            <input type="number" step="0.01" name="body_temperature" id="body_temperature" class="form-control" value="{{ old('body_temperature') }}" placeholder="例: 36.50">
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
