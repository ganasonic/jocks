@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- 更新成功時のメッセージ --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">プロフィール設定</div>

                <div class="card-body p-4">
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

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf

                        {{-- ユーザー名（usersテーブルから取得・編集不可） --}}
                        <div class="form-group mb-3">
                            <label class="form-label text-muted">アカウント名</label>
                            <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }}" disabled>
                            <small class="text-muted">※アカウント名は変更できません。</small>
                        </div>

                        {{-- 生年月日 --}}
                        <div class="form-group mb-3">
                            <label for="birthdate" class="form-label">生年月日</label>
                            <input type="date" name="birthdate" id="birthdate" class="form-control"
                                   value="{{ old('birthdate', $userDetail->birthdate) }}">
                        </div>

                        {{-- 所属 --}}
                        <div class="form-group mb-4">
                            <label for="affiliation" class="form-label">所属（チーム・学校・企業など）</label>
                            <input type="text" name="affiliation" id="affiliation" class="form-control"
                                   placeholder="例: サッカー部、〇〇株式会社"
                                   value="{{ old('affiliation', $userDetail->affiliation) }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">プロフィールを更新する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
