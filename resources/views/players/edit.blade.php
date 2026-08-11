@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-4">
        {{ $player->name }} の担当スタッフ設定
    </h3>

    <form method="POST" action="{{ route('players.update', $player->id) }}">

        @csrf

        <div class="card">
            <div class="card-body">

                @forelse($staffs as $staff)

                    <div class="form-check mb-3">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="staffs[]"
                            value="{{ $staff->id }}"
                            id="staff{{ $staff->id }}"
                            {{ in_array($staff->id, $checked) ? 'checked' : '' }}
                        >

                        <label
                            class="form-check-label"
                            for="staff{{ $staff->id }}"
                        >
                            {{ $staff->name }}
                            （{{ $staff->role_name }}）
                        </label>

                    </div>

                @empty

                    <p>スタッフが登録されていません。</p>

                @endforelse

            </div>
        </div>

        <div class="mt-3">

            <button type="submit" class="btn btn-primary">
                保存
            </button>

            <a href="{{ route('players.index') }}" class="btn btn-secondary">
                戻る
            </a>

        </div>

    </form>

</div>

@endsection
