@extends('layouts.app')
@section('content')
<div class="container">
@auth
    {{-- ============================================================
         タイトル
         ============================================================ --}}
    <h1 class="mb-3">{{ $title }}</h1>

    {{-- ============================================================
         日付移動
         ============================================================ --}}
    <div class="row mb-3 align-items-center">
        <div class="col-12 col-md-7">
            <div class="input-group">
                <label class="input-group-text"
                       for="specifieddate">
                    日時
                </label>
                <input type="date"
                       class="form-control"
                       id="specifieddate"
                       name="specifieddate"
                       value="{{ $shift_date }}">
            </div>
        </div>

        <div class="col-12 col-md-5 mt-2 mt-md-0">
            <div class="d-flex gap-2 justify-content-md-end">
                <input type="button"
                       class="btn btn-outline-primary"
                       id="move_pre"
                       value="{{ __('前日') }}">
                <input type="button"
                       class="btn btn-outline-primary"
                       id="move_pst"
                       value="{{ __('翌日') }}">
            </div>
        </div>
    </div>

    {{-- ============================================================
         日付表示
         ============================================================ --}}
    <div class="detail-date-header mb-4">
        <i class="far fa-calendar-alt"></i>
        <span>
            {{ $shift_date }}
            {{ $weekday }}
        </span>
    </div>

    {{-- ============================================================
         項目別表示
         ============================================================ --}}
    <div class="mb-4">
        <h3 class="detail-h3-color mb-3">
            項目別表示
        </h3>

        <div class="detail-summary-grid">
            {{-- 朝食 --}}
            <div class="detail-summary-card meal">
                <div class="detail-summary-icon">
                    <i class="fas fa-mug-hot"></i>
                </div>
                <div class="detail-summary-title">
                    朝食
                </div>
                <div class="detail-summary-count">
                    {{ $totals['breakfast'] }}
                    <small>名</small>
                </div>
                <div class="detail-summary-names">
                    @forelse($names['breakfast'] as $name)
                        <div>{{ $name }}</div>
                    @empty
                        <span class="text-muted">
                            なし
                        </span>
                    @endforelse
                </div>
            </div>

            {{-- 午前 --}}
            <div class="detail-summary-card attendance">
                <div class="detail-summary-icon">
                    <i class="fas fa-sun"></i>
                </div>
                <div class="detail-summary-title">
                    午前
                </div>
                <div class="detail-summary-count">
                    {{ $totals['am_shift'] }}
                    <small>名</small>
                </div>
                <div class="detail-summary-names">
                    @forelse($names['am_shift'] as $name)
                        <div>{{ $name }}</div>
                    @empty
                        <span class="text-muted">
                            なし
                        </span>
                    @endforelse
                </div>
            </div>

            {{-- 昼食 --}}
            <div class="detail-summary-card meal">
                <div class="detail-summary-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="detail-summary-title">
                    昼食
                </div>
                <div class="detail-summary-count">
                    {{ $totals['lunch'] }}
                    <small>名</small>
                </div>
                <div class="detail-summary-names">
                    @forelse($names['lunch'] as $name)
                        <div>{{ $name }}</div>
                    @empty
                        <span class="text-muted">
                            なし
                        </span>
                    @endforelse
                </div>
            </div>

            {{-- 午後 --}}
            <div class="detail-summary-card attendance">
                <div class="detail-summary-icon">
                    <i class="fas fa-running"></i>
                </div>
                <div class="detail-summary-title">
                    午後
                </div>
                <div class="detail-summary-count">
                    {{ $totals['pm_shift'] }}
                    <small>名</small>
                </div>
                <div class="detail-summary-names">
                    @forelse($names['pm_shift'] as $name)
                        <div>{{ $name }}</div>
                    @empty
                        <span class="text-muted">
                            なし
                        </span>
                    @endforelse
                </div>
            </div>

            {{-- 夕食 --}}
            <div class="detail-summary-card meal">
                <div class="detail-summary-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="detail-summary-title">
                    夕食
                </div>
                <div class="detail-summary-count">
                    {{ $totals['dinner'] }}
                    <small>名</small>
                </div>
                <div class="detail-summary-names">
                    @forelse($names['dinner'] as $name)
                        <div>{{ $name }}</div>
                    @empty
                        <span class="text-muted">
                            なし
                        </span>
                    @endforelse
                </div>
            </div>

            {{-- 宿泊 --}}
            <div class="detail-summary-card stay">
                <div class="detail-summary-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <div class="detail-summary-title">
                    宿泊
                </div>
                <div class="detail-summary-count">
                    {{ $totals['stay'] }}
                    <small>名</small>
                </div>
                <div class="detail-summary-names">
                    @forelse($names['stay'] as $name)
                        <div>{{ $name }}</div>
                    @empty
                        <span class="text-muted">
                            なし
                        </span>
                    @endforelse
                </div>
            </div>

            @if(($user->property & 0x10) > 0)
                {{-- 往路 --}}
                <div class="detail-summary-card transport">
                    <div class="detail-summary-icon">
                        <i class="fas fa-car-side"></i>
                    </div>
                    <div class="detail-summary-title">
                        往路
                    </div>
                    <div class="detail-summary-count">
                        {{ $totals['bus_outward'] }}
                        <small>名</small>
                    </div>
                    <div class="detail-summary-names">
                        @forelse($names['bus_outward'] as $name)
                            <div>{{ $name }}</div>
                        @empty
                            <span class="text-muted">
                                なし
                            </span>
                        @endforelse
                    </div>
                </div>

                {{-- 復路 --}}
                <div class="detail-summary-card transport">
                    <div class="detail-summary-icon">
                        <i class="fas fa-car-side"></i>
                    </div>
                    <div class="detail-summary-title">
                        復路
                    </div>
                    <div class="detail-summary-count">
                        {{ $totals['bus_return'] }}
                        <small>名</small>
                    </div>
                    <div class="detail-summary-names">
                        @forelse($names['bus_return'] as $name)
                            <div>{{ $name }}</div>
                        @empty
                            <span class="text-muted">
                                なし
                            </span>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ============================================================
         コメント
         ============================================================ --}}
    <div class="mb-4">
        <h3 class="detail-h3-color mb-3">
            コメント
        </h3>

        @php
            $hasComment = false;
        @endphp

        @foreach($shifts as $shift)
            @if(!empty($shift->comment))
                @php
                    $hasComment = true;
                @endphp
                <div class="detail-comment-card">
                    <div class="detail-comment-name">
                        <i class="fas fa-user me-1"></i>
                        {{ $shift->user->name }}
                    </div>
                    <div class="detail-comment-text">
                        {{ $shift->comment }}
                    </div>
                </div>
            @endif
        @endforeach

        @if(!$hasComment)
            <div class="text-muted">
                コメントはありません。
            </div>
        @endif
    </div>

    {{-- ============================================================
         メンバー別表示
         ============================================================ --}}
    <div class="mb-4">
        <h3 class="detail-h3-color mb-3">
            メンバー別表示
        </h3>

        @foreach($shifts as $shift)
            <div class="detail-member-card">
                {{-- 名前 --}}
                <div class="detail-member-name">
                    <i class="fas fa-user-circle"></i>
                    {{ $shift->user->name }}
                </div>

                {{-- アイコン --}}
                <div class="detail-member-grid">

                    {{-- 朝食 --}}
                    <div class="detail-member-status meal
                        {{ $shift->breakfast == 1 ? 'selected' : '' }}">
                        <i class="fas fa-mug-hot"></i>
                        <span>
                            朝食
                        </span>
                    </div>

                    {{-- 午前 --}}
                    <div class="detail-member-status attendance
                        {{ $shift->am_shift == 1 ? 'selected' : '' }}">
                        <i class="fas fa-sun"></i>
                        <span>
                            午前
                        </span>
                    </div>

                    {{-- 昼食 --}}
                    <div class="detail-member-status meal
                        {{ $shift->lunch == 1 ? 'selected' : '' }}">
                        <i class="fas fa-utensils"></i>
                        <span>
                            昼食
                        </span>
                    </div>

                    {{-- 午後 --}}
                    <div class="detail-member-status attendance
                        {{ $shift->pm_shift == 1 ? 'selected' : '' }}">
                        <i class="fas fa-running"></i>
                        <span>
                            午後
                        </span>
                    </div>

                    {{-- 夕食 --}}
                    <div class="detail-member-status meal
                        {{ $shift->dinner == 1 ? 'selected' : '' }}">
                        <i class="fas fa-utensils"></i>
                        <span>
                            夕食
                        </span>
                    </div>

                    {{-- 宿泊 --}}
                    <div class="detail-member-status stay
                        {{ $shift->stay == 1 ? 'selected' : '' }}">
                        <i class="fas fa-bed"></i>
                        <span>
                            宿泊
                        </span>
                    </div>

                    @if(($user->property & 0x10) > 0)
                        {{-- 往路 --}}
                        <div class="detail-member-status transport
                            {{ $shift->bus_outward == 1 ? 'selected' : '' }}">
                            <i class="fas fa-car-side"></i>
                            <span>
                                往路
                            </span>
                        </div>

                        {{-- 復路 --}}
                        <div class="detail-member-status transport
                            {{ $shift->bus_return == 1 ? 'selected' : '' }}">
                            <i class="fas fa-car-side"></i>
                            <span>
                                復路
                            </span>
                        </div>
                    @endif
                </div>

                {{-- コメントもメンバーのカード内に表示 --}}
                @if(!empty($shift->comment))
                    <div class="detail-member-comment">
                        <i class="far fa-comment"></i>
                        {{ $shift->comment }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

@endauth
</div>

<script>
/*
 * ============================================================
 * 日付直接選択
 * ============================================================
 */
document.getElementById('specifieddate').addEventListener(
    'change',
    function () {
        const selectedDate = this.value;
        const url = `/specified/${selectedDate}`;
        window.location.href = url;
    }
);

/*
 * ============================================================
 * 前日
 * ============================================================
 */
document.getElementById('move_pre').addEventListener(
    'click',
    function () {
        const selectedDate =
            new Date(document.getElementById('specifieddate').value);
        selectedDate.setDate(
            selectedDate.getDate() - 1
        );
        const year =
            selectedDate.getFullYear();
        const month =
            String(
                selectedDate.getMonth() + 1
            ).padStart(2, '0');
        const day =
            String(
                selectedDate.getDate()
            ).padStart(2, '0');
        const previousDate =
            `${year}-${month}-${day}`;
        window.location.href =
            `/specified/${previousDate}`;
    }
);

/*
 * ============================================================
 * 翌日
 * ============================================================
 */
document.getElementById('move_pst').addEventListener(
    'click',
    function () {
        const selectedDate =
            new Date(document.getElementById('specifieddate').value);
        selectedDate.setDate(
            selectedDate.getDate() + 1
        );
        const year =
            selectedDate.getFullYear();
        const month =
            String(
                selectedDate.getMonth() + 1
            ).padStart(2, '0');
        const day =
            String(
                selectedDate.getDate()
            ).padStart(2, '0');
        const nextDate =
            `${year}-${month}-${day}`;
        window.location.href =
            `/specified/${nextDate}`;
    }
);
</script>
@endsection
