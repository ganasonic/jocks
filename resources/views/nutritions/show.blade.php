@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">栄養管理 詳細</h1>

            <div class="card mb-4">
                <div class="card-body">
                    <h3>日付: {{ $nutrition->nutrition_date }}</h3>
                    <p>メモ: {{ $nutrition->daily_memo }}</p>
                    {{-- 日ごとのコーチコメント --}}
                    @if(!empty($nutrition->daily_coach_comment))
                        <p><strong>コーチコメント:</strong> {{ $nutrition->daily_coach_comment }}</p>
                    @endif
                </div>
            </div>

            @foreach($nutrition->details as $detail)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>食事種別:</strong> {{ $detail->meal_type }}
                        </div>
                        <div class="col-md-4">
                            <strong>カロリー:</strong> {{ $detail->calories }} kcal
                        </div>
                        <div class="col-md-4">
                            <strong>タンパク質:</strong> {{ $detail->protein }} g
                        </div>
                    </div>
                    @if($detail->photo_path)
                    <div class="mt-3">
                        <img src="{{ asset('storage/'.$detail->photo_path) }}" style="max-width: 100%; border-radius: 5px;">
                    </div>
                    @endif
                    {{-- 食事ごとのコーチコメント --}}
                    @if(!empty($detail->meal_coach_comment))
                    <div class="mt-2">
                        <strong>コーチコメント:</strong> {{ $detail->meal_coach_comment }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach

            <a href="{{ route('nutritions.index') }}" class="btn btn-secondary">一覧へ戻る</a>
        </div>
    </div>
</div>
@endsection
