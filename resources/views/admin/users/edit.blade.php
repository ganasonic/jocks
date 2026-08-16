@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>
                        <i class="fas fa-user-cog me-2"></i>
                        ユーザー権限編集
                    </strong>
                </div>

                <div class="card-body p-4">

                    {{-- ユーザー情報 --}}
                    <div class="mb-4">
                        <div class="fs-5 fw-bold">
                            {{ $user->name }}
                        </div>
                        <div class="text-muted">
                            ID: {{ $user->id }}
                            /
                            {{ $user->email }}
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        action="{{ route('admin.users.update', $user->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PATCH')

                        {{-- =====================================
                             role
                             ===================================== --}}
                        <div class="mb-4">
                            <label for="role"
                                   class="form-label fw-bold">
                                アプリ権限
                            </label>
                            <select
                                name="role"
                                id="role"
                                class="form-select"
                                required
                            >
                                @foreach($roles as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        {{ (int)old('role', $user->role) === (int)$value ? 'selected' : '' }}
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                このユーザーがアプリ内で行える操作を指定します。
                            </div>
                        </div>

                        {{-- =====================================
                             member_type
                             ===================================== --}}
                        <div class="mb-4">
                            <label for="member_type"
                                   class="form-label fw-bold">
                                メンバー区分
                            </label>
                            <select
                                name="member_type"
                                id="member_type"
                                class="form-select"
                                required
                            >
                                <option value="">
                                    選択してください
                                </option>
                                @foreach($memberTypes as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        {{ (int)old('member_type', $user->member_type) === (int)$value ? 'selected' : '' }}
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                スクール内での立場を指定します。
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="fas fa-save me-1"></i>
                                保存
                            </button>

                            <a
                                href="{{ route('admin.users.index') }}"
                                class="btn btn-secondary"
                            >
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
