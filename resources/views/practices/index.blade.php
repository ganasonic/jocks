@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">練習記録一覧</h2>
        <a href="{{ route('practices.create') }}" class="btn btn-primary fw-bold">
            ＋ 新規記録を作成
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @forelse ($practices as $practice)
        <div class="card shadow-sm border-light-subtle mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <span class="fs-5 fw-bold text-dark">
                        {{ \Carbon\Carbon::parse($practice->practice_date)->format('Y/m/d') }}
                    </span>
                    @if($practice->practice_type)
                        <span class="badge bg-primary ms-2">{{ $practice->practice_type }}</span>
                    @endif
                    @if($practice->title)
                        <span class="text-muted ms-2"><i class="bi bi-geo-alt"></i> {{ $practice->title }}</span>
                    @endif
                </div>
                <div>
                    <a href="{{ route('practices.show', $practice->id) }}" class="btn btn-sm btn-outline-info me-1">詳細</a>
                    <a href="{{ route('practices.edit', $practice->id) }}" class="btn btn-sm btn-outline-secondary me-1">編集</a>
                    <form action="{{ route('practices.destroy', $practice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('本当に削除しますか？');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                    </form>
                </div>
            </div>

            <div class="card-body p-3">
                @if($practice->target)
                    <p class="mb-2 text-secondary"><strong>目標:</strong> {{ $practice->target }}</p>
                @endif

                @if($practice->details->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover border align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>メニュー名</th>
                                    <th style="width: 100px;">本数/時間</th>
                                    <th style="width: 110px;">本人達成度</th>
                                    <th style="width: 110px;">コーチ達成度</th>
                                    <th>感触・アドバイス等</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($practice->details as $detail)
                                    <tr>
                                        <td class="fw-bold">{{ $detail->menu_name }}</td>
                                        <td>{{ $detail->runs_or_time ?? '-' }}</td>
                                        <td>
                                            @if($detail->rating)
                                                <span class="text-warning">{{ str_repeat('★', $detail->rating) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($detail->coach_rating)
                                                <span class="text-primary">{{ str_repeat('★', $detail->coach_rating) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($detail->impression)
                                                <div><small class="text-muted">【感触】</small> {{ $detail->impression }}</div>
                                            @endif
                                            @if($detail->coach_comment)
                                                <div class="text-success"><small>【コーチ】</small> {{ $detail->coach_comment }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($practice->overall_coach_comment)
                    <div class="mt-3 p-2 bg-success-subtle border border-success-subtle rounded text-success">
                        <strong>総評（コーチ）:</strong> {{ $practice->overall_coach_comment }}
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted">
            練習記録がありません。
        </div>
    @endforelse

    <div class="d-flex justify-content-center">
        {{ $practices->links() }}
    </div>
</div>
@endsection
