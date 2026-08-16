@extends('layouts.app')
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-users-cog me-2"></i>
            ユーザー管理
        </h1>
    </div>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>名前</th>
                            <th>メール</th>
                            <th>権限</th>
                            <th>メンバー区分</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    {{ $user->id }}
                                </td>
                                <td class="fw-bold">
                                    {{ $user->name }}
                                </td>
                                <td>
                                    {{ $user->email }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $user->role_name }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->member_type)
                                        <span class="badge bg-info text-dark">
                                            {{ $user->member_type_name }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            未設定
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a
                                        href="{{ route('admin.users.edit', $user->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="fas fa-pen"></i>
                                        編集
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
