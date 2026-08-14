@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show"
                     role="alert">
                    {{ session('status') }}
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>
                        <i class="fas fa-user me-2"></i>
                        プロフィール
                    </strong>
                    <a href="{{ route('profile.edit') }}"
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-pen me-1"></i>
                        編集
                    </a>
                </div>

                <div class="card-body">
                    {{-- アカウント名 --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            アカウント名
                        </div>
                        <div class="profile-value">
                            {{ $user->name }}
                        </div>
                    </div>

                    {{-- メールアドレス --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            メールアドレス
                        </div>
                        <div class="profile-value">
                            {{ $user->email }}
                        </div>
                    </div>

                    {{-- 権限 --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            ユーザー種別
                        </div>
                        <div class="profile-value">
                            {{ $user->role_name }}
                        </div>
                    </div>

                    {{-- 生年月日 --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            生年月日
                        </div>
                        <div class="profile-value">
                            {{ $userDetail->birthdate ?: '未設定' }}
                        </div>
                    </div>

                    {{-- 所属 --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            所属
                        </div>
                        <div class="profile-value">
                            {{ $userDetail->affiliation ?: '未設定' }}
                        </div>
                    </div>

                    {{-- チーム名 --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            チーム名
                        </div>
                        <div class="profile-value">
                            {{ $userDetail->team_name ?: '未設定' }}
                        </div>
                    </div>

                    {{-- 性別 --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            性別
                        </div>
                        <div class="profile-value">
                            @if($userDetail->gender === 'male')
                                男性
                            @elseif($userDetail->gender === 'female')
                                女性
                            @elseif($userDetail->gender === 'other')
                                その他
                            @elseif($userDetail->gender === 'private')
                                回答しない
                            @else
                                未設定
                            @endif
                        </div>
                    </div>

                    {{-- 電話番号 --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            電話番号
                        </div>
                        <div class="profile-value">
                            {{ $userDetail->phone ?: '未設定' }}
                        </div>
                    </div>

                    {{-- LINE ID --}}
                    <div class="profile-row">
                        <div class="profile-label">
                            LINE ID
                        </div>
                        <div class="profile-value">
                            {{ $userDetail->line_id ?: '未設定' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
