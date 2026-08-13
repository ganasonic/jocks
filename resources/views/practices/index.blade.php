@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- タイトル・新規登録 --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 fw-bold">練習記録一覧</h2>

        <a href="{{ route('practices.create') }}"
           class="btn btn-primary fw-bold">
            ＋ 新規練習
        </a>
    </div>

    {{-- メッセージ --}}
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-4"
             role="alert">

            {{ session('status') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>
        </div>
    @endif


    @forelse ($practices as $practice)

        <div class="card shadow-sm border-light-subtle mb-4">

            {{-- =====================================================
                 練習記録ヘッダー
                 ===================================================== --}}
            <div class="card-header bg-white py-3">

                {{-- PC・タブレット --}}
                <div class="d-none d-md-flex justify-content-between align-items-center">

                    <div>
                        <span class="fs-5 fw-bold text-dark">
                            {{ \Carbon\Carbon::parse($practice->practice_date)->format('Y/m/d') }}
                        </span>

                        @if($practice->practice_type)
                            <span class="badge bg-primary ms-2">
                                {{ $practice->practice_type }}
                            </span>
                        @endif

                        @if($practice->title)
                            <span class="text-muted ms-2">
                                {{ $practice->title }}
                            </span>
                        @endif
                    </div>

                    <div class="d-flex">

                        <a href="{{ route('practices.show', $practice->id) }}"
                           class="btn btn-sm btn-outline-info me-1">
                            詳細
                        </a>

                        <a href="{{ route('practices.edit', $practice->id) }}"
                           class="btn btn-sm btn-outline-secondary me-1">
                            編集
                        </a>

                        <form action="{{ route('practices.destroy', $practice->id) }}"
                              method="POST"
                              onsubmit="return confirm('本当に削除しますか？');">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger">
                                削除
                            </button>

                        </form>

                    </div>

                </div>


                {{-- スマホ --}}
                <div class="d-md-none">

                    <div class="mb-2">

                        <div class="fw-bold fs-5">
                            {{ \Carbon\Carbon::parse($practice->practice_date)->format('Y/m/d') }}
                        </div>

                        @if($practice->practice_type)
                            <div class="mt-2">
                                <span class="badge bg-primary">
                                    {{ $practice->practice_type }}
                                </span>
                            </div>
                        @endif

                        @if($practice->title)
                            <div class="text-muted mt-2">
                                {{ $practice->title }}
                            </div>
                        @endif

                    </div>


                    <div class="d-flex gap-2 flex-wrap mt-3">

                        <a href="{{ route('practices.show', $practice->id) }}"
                           class="btn btn-sm btn-outline-info">
                            詳細
                        </a>

                        <a href="{{ route('practices.edit', $practice->id) }}"
                           class="btn btn-sm btn-outline-secondary">
                            編集
                        </a>

                        <form action="{{ route('practices.destroy', $practice->id) }}"
                              method="POST"
                              onsubmit="return confirm('本当に削除しますか？');">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger">
                                削除
                            </button>

                        </form>

                    </div>

                </div>

            </div>


            <div class="card-body p-3">

                {{-- 目標 --}}
                @if($practice->target)
                    <div class="mb-3">
                        <strong>目標:</strong>
                        <span class="text-secondary">
                            {{ $practice->target }}
                        </span>
                    </div>
                @endif


                @if($practice->details->isNotEmpty())

                    {{-- =====================================================
                         PC・タブレット用テーブル
                         ===================================================== --}}
                    <div class="d-none d-md-block">

                        <div class="table-responsive">

                            <table class="table table-sm table-hover border align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th>メニュー名</th>
                                        <th style="width: 100px;">
                                            本数/時間
                                        </th>
                                        <th style="width: 110px;">
                                            本人達成度
                                        </th>
                                        <th style="width: 110px;">
                                            コーチ達成度
                                        </th>
                                        <th>
                                            感触・アドバイス等
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($practice->details as $detail)

                                        <tr>

                                            <td class="fw-bold">
                                                {{ $detail->menu_name }}
                                            </td>

                                            <td>
                                                {{ $detail->runs_or_time ?? '-' }}
                                            </td>

                                            <td>
                                                @if($detail->rating)

                                                    <span class="text-warning">
                                                        {{ str_repeat('★', $detail->rating) }}
                                                    </span>

                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>
                                                @if($detail->coach_rating)

                                                    <span class="text-primary">
                                                        {{ str_repeat('★', $detail->coach_rating) }}
                                                    </span>

                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <td>

                                                @if($detail->impression)
                                                    <div>
                                                        <small class="text-muted">
                                                            【感触】
                                                        </small>

                                                        {{ $detail->impression }}
                                                    </div>
                                                @endif

                                                @if($detail->feedback)
                                                    <div class="text-success">

                                                        <small>
                                                            【コーチ】
                                                        </small>

                                                        {{ $detail->feedback }}

                                                    </div>
                                                @endif

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>


                    {{-- =====================================================
                         スマホ用カード表示
                         ===================================================== --}}
                    <div class="d-md-none">

                        @foreach($practice->details as $detail)

                            <div class="border rounded p-3 mb-3 bg-white">

                                {{-- メニュー名 --}}
                                <div class="fw-bold fs-5 mb-3">
                                    {{ $detail->menu_name }}
                                </div>


                                {{-- 本数/時間 --}}
                                <div class="row mb-2">

                                    <div class="col-5 text-muted">
                                        本数/時間
                                    </div>

                                    <div class="col-7 fw-bold">
                                        {{ $detail->runs_or_time ?? '-' }}
                                    </div>

                                </div>


                                {{-- 本人達成度 --}}
                                <div class="row mb-2">

                                    <div class="col-5 text-muted">
                                        本人達成度
                                    </div>

                                    <div class="col-7">

                                        @if($detail->rating)

                                            <span class="text-warning">
                                                {{ str_repeat('★', $detail->rating) }}
                                            </span>

                                        @else
                                            -
                                        @endif

                                    </div>

                                </div>


                                {{-- コーチ達成度 --}}
                                <div class="row mb-3">

                                    <div class="col-5 text-muted">
                                        コーチ達成度
                                    </div>

                                    <div class="col-7">

                                        @if($detail->coach_rating)

                                            <span class="text-primary">
                                                {{ str_repeat('★', $detail->coach_rating) }}
                                            </span>

                                        @else
                                            -
                                        @endif

                                    </div>

                                </div>


                                {{-- 感触 --}}
                                @if($detail->impression)

                                    <div class="border-top pt-3 mt-2">

                                        <div class="small text-muted mb-1">
                                            感触
                                        </div>

                                        <div style="white-space: pre-wrap;">
                                            {{ $detail->impression }}
                                        </div>

                                    </div>

                                @endif


                                {{-- コーチフィードバック --}}
                                @if($detail->feedback)

                                    <div class="border-top pt-3 mt-3">

                                        <div class="small text-success fw-bold mb-1">
                                            スタッフフィードバック
                                        </div>

                                        <div class="text-success"
                                             style="white-space: pre-wrap;">
                                            {{ $detail->feedback }}
                                        </div>

                                    </div>

                                @endif

                            </div>

                        @endforeach

                    </div>

                @endif


                {{-- 総評 --}}
                @if($practice->feedback)

                    <div class="mt-3 p-3
                                bg-success-subtle
                                border
                                border-success-subtle
                                rounded
                                text-success">

                        <strong>
                            総評（スタッフ）:
                        </strong>

                        <div class="mt-1"
                             style="white-space: pre-wrap;">
                            {{ $practice->feedback }}
                        </div>

                    </div>

                @endif

            </div>

        </div>

    @empty

        <div class="text-center py-5 text-muted">
            練習記録がありません。
        </div>

    @endforelse


    {{-- ページング --}}
    <div class="d-flex justify-content-center">
        {{ $practices->links() }}
    </div>

</div>
@endsection
