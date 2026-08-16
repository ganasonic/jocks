@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>
                        <i class="fas fa-user-edit me-2"></i>
                        プロフィール編集
                    </strong>
                </div>

                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}"
                          method="POST">
                        @csrf

                        {{-- アカウント名 --}}
                        <div class="mb-3">
                            <label class="form-label">
                                アカウント名
                            </label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $user->name }}"
                                   disabled>
                            <small class="text-muted">
                                アカウント名はこの画面では変更できません。
                            </small>
                        </div>

                        {{-- メールアドレス --}}
                        <div class="mb-3">
                            <label class="form-label">
                                メールアドレス
                            </label>
                            <input type="email"
                                   class="form-control bg-light"
                                   value="{{ $user->email }}"
                                   disabled>
                        </div>

                        {{-- 生年月日 --}}
                        <div class="mb-3">
                            <label for="birthdate"
                                   class="form-label">
                                生年月日
                            </label>
                            <input type="date"
                                   name="birthdate"
                                   id="birthdate"
                                   class="form-control"
                                   value="{{ old('birthdate', $userDetail->birthdate) }}">
                        </div>

                        {{-- 所属 --}}
                        <div class="mb-3">
                            <label for="affiliation"
                                   class="form-label">
                                所属
                            </label>
                            <input type="text"
                                   name="affiliation"
                                   id="affiliation"
                                   class="form-control"
                                   maxlength="100"
                                   placeholder="例：〇〇県スキー連盟、学連、〇〇高校、〇〇株式会社"
                                   value="{{ old('affiliation', $userDetail->affiliation) }}">
                        </div>

                        {{-- チーム名 --}}
                        <div class="mb-3">
                            <label for="team_name"
                                   class="form-label">
                                チーム名
                            </label>
                            <input type="text"
                                   name="team_name"
                                   id="team_name"
                                   class="form-control"
                                   maxlength="100"
                                   placeholder="例：〇〇スキークラブ"
                                   value="{{ old('team_name', $userDetail->team_name) }}">
                        </div>

                        {{-- 性別 --}}
                        <div class="mb-3">
                            <label for="gender"
                                   class="form-label">
                                性別
                            </label>
                            <select name="gender"
                                    id="gender"
                                    class="form-select">
                                <option value="">
                                    未設定
                                </option>
                                <option value="male"
                                    {{ old('gender', $userDetail->gender) === 'male' ? 'selected' : '' }}>
                                    男性
                                </option>
                                <option value="female"
                                    {{ old('gender', $userDetail->gender) === 'female' ? 'selected' : '' }}>
                                    女性
                                </option>
                                <option value="other"
                                    {{ old('gender', $userDetail->gender) === 'other' ? 'selected' : '' }}>
                                    その他
                                </option>
                                <option value="private"
                                    {{ old('gender', $userDetail->gender) === 'private' ? 'selected' : '' }}>
                                    回答しない
                                </option>
                            </select>
                        </div>

                        {{-- 電話番号 --}}
                        <div class="mb-3">
                            <label for="phone"
                                   class="form-label">
                                電話番号
                            </label>
                            <input type="tel"
                                   name="phone"
                                   id="phone"
                                   class="form-control"
                                   maxlength="30"
                                   placeholder="例：090-1234-5678"
                                   value="{{ old('phone', $userDetail->phone) }}">
                        </div>

                        {{-- LINE ID --}}
                        <div class="mb-4">
                            <label for="line_id"
                                   class="form-label">
                                LINE ID
                            </label>
                            <input type="text"
                                   name="line_id"
                                   id="line_id"
                                   class="form-control"
                                   maxlength="100"
                                   value="{{ old('line_id', $userDetail->line_id) }}">
                        </div>

                        {{-- ============================================================
                            パスワード変更
                            ============================================================ --}}
                        <hr class="my-4">
                        <div class="card border-warning mb-4">
                            <div class="card-header bg-warning-subtle">
                                <strong>
                                    <i class="fas fa-key me-2"></i>
                                    パスワード変更
                                </strong>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small mb-3">
                                    パスワードを変更しない場合は、
                                    以下の項目はすべて空欄のままにしてください。
                                </p>

                                {{-- 現在のパスワード --}}
                                <div class="mb-3">
                                    <label for="current_password"
                                        class="form-label">
                                        現在のパスワード
                                    </label>
                                    <input type="password"
                                        name="current_password"
                                        id="current_password"
                                        class="form-control
                                            @error('current_password')
                                                is-invalid
                                            @enderror"
                                        autocomplete="current-password">
                                    @error('current_password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- 新しいパスワード --}}
                                <div class="mb-3">
                                    <label for="password"
                                        class="form-label">
                                        新しいパスワード
                                    </label>
                                    <input type="password"
                                        name="password"
                                        id="password"
                                        class="form-control
                                            @error('password')
                                                is-invalid
                                            @enderror"
                                        minlength="8"
                                        autocomplete="new-password">
                                    <div class="form-text">
                                        8文字以上で入力してください。
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- 新しいパスワード確認 --}}
                                <div class="mb-0">
                                    <label for="password_confirmation"
                                        class="form-label">
                                        新しいパスワード（確認）
                                    </label>
                                    <input type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="form-control"
                                        minlength="8"
                                        autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                保存
                            </button>
                            <a href="{{ route('profile.show') }}"
                               class="btn btn-secondary">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
