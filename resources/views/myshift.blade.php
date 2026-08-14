@extends('layouts.app')

@section('content')

<div class="container">

@auth

    <h1>{{ $title }}{{ __('管理@') }}{{ $user->name }}</h1>

    <form action="/shiftupdate/{{ $user->id }}" method="POST">
        @csrf

        {{-- =========================================================
             checkboxがOFFの場合に0を送信するためのhidden
             ========================================================= --}}
        <input type="hidden" value="0" name="tdy_breakfast">
        <input type="hidden" value="0" name="tdy_am_shift">
        <input type="hidden" value="0" name="tdy_lunch">
        <input type="hidden" value="0" name="tdy_pm_shift">
        <input type="hidden" value="0" name="tdy_dinner">
        <input type="hidden" value="0" name="tdy_stay">
        <input type="hidden" value="0" name="tdy_bus_outward">
        <input type="hidden" value="0" name="tdy_bus_return">


        {{-- =========================================================
             登録日
             ========================================================= --}}
        <div class="row mb-3">

            <div class="col-8">

                <div class="input-group mb-3">

                    <label class="input-group-text"
                           for="specifieddate">
                        登録日
                    </label>

                    <input type="date"
                           class="form-control"
                           id="specifieddate"
                           name="specifieddate"
                           value="{{ $shift->shift_date }}">
                </div>
            </div>
            <div class="col-4">
                <button type="submit"
                        class="btn btn-success">
                    {{ __('更新') }}
                </button>
            </div>
        </div>

        {{-- =========================================================
             前日
             ========================================================= --}}
        <div class="row mb-3">
            <div class="col-12">
                <h3 class="myshift-h3-color">
                    {{ __('前日') }}
                    {{ $shift_pre->shift_date }}
                    {{ $shift_pre->weekday }}
                </h3>
            </div>
        </div>

        @if(
            $shift_pre->breakfast == 1 ||
            $shift_pre->am_shift == 1 ||
            $shift_pre->lunch == 1 ||
            $shift_pre->pm_shift == 1 ||
            $shift_pre->dinner == 1 ||
            $shift_pre->stay == 1 ||
            $shift_pre->bus_outward == 1 ||
            $shift_pre->bus_return == 1
        )
            <div class="shift-icon-grid shift-icon-grid-readonly mb-3">
                {{-- 朝食 --}}
                <div class="shift-icon-item meal
                    {{ $shift_pre->breakfast == 1 ? 'selected' : '' }}">
                    <i class="fas fa-mug-hot"></i>
                    <span class="shift-icon-label">
                        朝食
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pre->breakfast == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                {{-- 午前 --}}
                <div class="shift-icon-item attendance
                    {{ $shift_pre->am_shift == 1 ? 'selected' : '' }}">
                    <i class="fas fa-sun"></i>
                    <span class="shift-icon-label">
                        午前
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pre->am_shift == 1 ? '✓ 出席' : '欠席' }}
                    </span>
                </div>

                {{-- 昼食 --}}
                <div class="shift-icon-item meal
                    {{ $shift_pre->lunch == 1 ? 'selected' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span class="shift-icon-label">
                        昼食
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pre->lunch == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                {{-- 午後 --}}
                <div class="shift-icon-item attendance
                    {{ $shift_pre->pm_shift == 1 ? 'selected' : '' }}">
                    <i class="fas fa-running"></i>
                    <span class="shift-icon-label">
                        午後
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pre->pm_shift == 1 ? '✓ 出席' : '欠席' }}
                    </span>
                </div>

                {{-- 夕食 --}}
                <div class="shift-icon-item meal
                    {{ $shift_pre->dinner == 1 ? 'selected' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span class="shift-icon-label">
                        夕食
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pre->dinner == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                {{-- 宿泊 --}}
                <div class="shift-icon-item stay
                    {{ $shift_pre->stay == 1 ? 'selected' : '' }}">
                    <i class="fas fa-bed"></i>
                    <span class="shift-icon-label">
                        宿泊
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pre->stay == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                @if(($user->property & 0x10) > 0)
                    {{-- 往路 --}}
                    <div class="shift-icon-item transport
                        {{ $shift_pre->bus_outward == 1 ? 'selected' : '' }}">
                        <i class="fas fa-car-side"></i>
                        <span class="shift-icon-label">
                            往路
                        </span>
                        <span class="shift-icon-state">
                            {{ $shift_pre->bus_outward == 1 ? '✓ 必要' : '不要' }}
                        </span>
                    </div>

                    {{-- 復路 --}}
                    <div class="shift-icon-item transport
                        {{ $shift_pre->bus_return == 1 ? 'selected' : '' }}">
                        <i class="fas fa-car-side"></i>
                        <span class="shift-icon-label">
                            復路
                        </span>
                        <span class="shift-icon-state">
                            {{ $shift_pre->bus_return == 1 ? '✓ 必要' : '不要' }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        {{-- 前日コメント --}}
        @if(!empty($shift_pre->comment))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="shift-comment-readonly">
                        {{ $shift_pre->comment }}
                    </div>
                </div>
            </div>
        @endif

        {{-- =========================================================
             当日
             ========================================================= --}}
        <div class="shift-today-area">
        <div class="shift-today-badge">
            <i class="fas fa-edit"></i>
            シフト入力する日
        </div>

        <div class="row mb-3 align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="myshift-h3-color mb-2 mb-md-0">
                    {{ __('当日') }}
                    <br class="d-md-none">
                    {{ $shift->shift_date }}
                    {{ $shift->weekday }}
                </h3>
            </div>

            <div class="col-12 col-md-6">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <input type="button"
                           class="btn btn-primary"
                           id="move_pre"
                           value="{{ __('前日') }}">
                    <input type="button"
                           class="btn btn-primary"
                           id="move_pst"
                           value="{{ __('翌日') }}">
                    <button type="submit"
                            class="btn btn-success">
                        {{ __('更新/登録') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- =========================================================
             当日のアイコン入力
             ========================================================= --}}
        <div class="shift-icon-grid mb-4">

            {{-- 朝食 --}}
            <div>
                <input type="checkbox"
                       class="shift-checkbox"
                       value="1"
                       name="tdy_breakfast"
                       id="tdy_breakfast"
                       {{ $shift->breakfast == 1 ? 'checked' : '' }}>
                <label for="tdy_breakfast"
                       class="shift-icon-item shift-icon-button meal">
                    <i class="fas fa-mug-hot"></i>
                    <span class="shift-icon-label">
                        朝食
                    </span>
                    <span class="shift-icon-state"
                          data-on="✓ あり"
                          data-off="なし">
                    </span>
                </label>
            </div>

            {{-- 午前 --}}
            <div>
                <input type="checkbox"
                       class="shift-checkbox"
                       value="1"
                       name="tdy_am_shift"
                       id="tdy_am_shift"
                       {{ $shift->am_shift == 1 ? 'checked' : '' }}>
                <label for="tdy_am_shift"
                       class="shift-icon-item shift-icon-button attendance">
                    <i class="fas fa-sun"></i>
                    <span class="shift-icon-label">
                        午前
                    </span>
                    <span class="shift-icon-state"
                          data-on="✓ 出席"
                          data-off="欠席">
                    </span>
                </label>
            </div>

            {{-- 昼食 --}}
            <div>
                <input type="checkbox"
                       class="shift-checkbox"
                       value="1"
                       name="tdy_lunch"
                       id="tdy_lunch"
                       {{ $shift->lunch == 1 ? 'checked' : '' }}>
                <label for="tdy_lunch"
                       class="shift-icon-item shift-icon-button meal">
                    <i class="fas fa-utensils"></i>
                    <span class="shift-icon-label">
                        昼食
                    </span>
                    <span class="shift-icon-state"
                          data-on="✓ あり"
                          data-off="なし">
                    </span>
                </label>
            </div>

            {{-- 午後 --}}
            <div>
                <input type="checkbox"
                       class="shift-checkbox"
                       value="1"
                       name="tdy_pm_shift"
                       id="tdy_pm_shift"
                       {{ $shift->pm_shift == 1 ? 'checked' : '' }}>
                <label for="tdy_pm_shift"
                       class="shift-icon-item shift-icon-button attendance">
                    <i class="fas fa-running"></i>
                    <span class="shift-icon-label">
                        午後
                    </span>
                    <span class="shift-icon-state"
                          data-on="✓ 出席"
                          data-off="欠席">
                    </span>
                </label>
            </div>

            {{-- 夕食 --}}
            <div>
                <input type="checkbox"
                       class="shift-checkbox"
                       value="1"
                       name="tdy_dinner"
                       id="tdy_dinner"
                       {{ $shift->dinner == 1 ? 'checked' : '' }}>
                <label for="tdy_dinner"
                       class="shift-icon-item shift-icon-button meal">
                    <i class="fas fa-utensils"></i>
                    <span class="shift-icon-label">
                        夕食
                    </span>
                    <span class="shift-icon-state"
                          data-on="✓ あり"
                          data-off="なし">
                    </span>
                </label>
            </div>

            {{-- 宿泊 --}}
            <div>
                <input type="checkbox"
                       class="shift-checkbox"
                       value="1"
                       name="tdy_stay"
                       id="tdy_stay"
                       {{ $shift->stay == 1 ? 'checked' : '' }}>
                <label for="tdy_stay"
                       class="shift-icon-item shift-icon-button stay">
                    <i class="fas fa-bed"></i>
                    <span class="shift-icon-label">
                        宿泊
                    </span>
                    <span class="shift-icon-state"
                          data-on="✓ あり"
                          data-off="なし">
                    </span>
                </label>
            </div>

            @if(($user->property & 0x10) > 0)
                {{-- 往路 --}}
                <div>
                    <input type="checkbox"
                           class="shift-checkbox"
                           value="1"
                           name="tdy_bus_outward"
                           id="tdy_bus_outward"
                           {{ $shift->bus_outward == 1 ? 'checked' : '' }}>
                    <label for="tdy_bus_outward"
                           class="shift-icon-item shift-icon-button transport">
                        <i class="fas fa-car-side"></i>
                        <span class="shift-icon-label">
                            往路
                        </span>
                        <span class="shift-icon-state"
                              data-on="✓ 必要"
                              data-off="不要">
                        </span>
                    </label>
                </div>

                {{-- 復路 --}}
                <div>
                    <input type="checkbox"
                           class="shift-checkbox"
                           value="1"
                           name="tdy_bus_return"
                           id="tdy_bus_return"
                           {{ $shift->bus_return == 1 ? 'checked' : '' }}>
                    <label for="tdy_bus_return"
                           class="shift-icon-item shift-icon-button transport">
                        <i class="fas fa-car-side"></i>
                        <span class="shift-icon-label">
                            復路
                        </span>
                        <span class="shift-icon-state"
                              data-on="✓ 必要"
                              data-off="不要">
                        </span>
                    </label>
                </div>
            @endif
        </div>

        {{-- コメント --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="form-floating">
                    <textarea class="form-control"
                              placeholder="コメント"
                              name="tdy_comment"
                              id="tdy_comment"
                              style="height:100px">{{ $shift->comment }}</textarea>
                    <label for="tdy_comment">
                        コメント
                    </label>
                </div>
            </div>
        </div>
        </div>

        {{-- =========================================================
             翌日
             ========================================================= --}}
        <div class="row mb-3">
            <div class="col-12">
                <h3 class="myshift-h3-color">
                    {{ __('翌日') }}
                    {{ $shift_pst->shift_date }}
                    {{ $shift_pst->weekday }}
                </h3>
            </div>
        </div>

        @if(
            $shift_pst->breakfast == 1 ||
            $shift_pst->am_shift == 1 ||
            $shift_pst->lunch == 1 ||
            $shift_pst->pm_shift == 1 ||
            $shift_pst->dinner == 1 ||
            $shift_pst->stay == 1 ||
            $shift_pst->bus_outward == 1 ||
            $shift_pst->bus_return == 1
        )
            <div class="shift-icon-grid shift-icon-grid-readonly mb-3">
                {{-- 朝食 --}}
                <div class="shift-icon-item meal
                    {{ $shift_pst->breakfast == 1 ? 'selected' : '' }}">
                    <i class="fas fa-mug-hot"></i>
                    <span class="shift-icon-label">
                        朝食
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pst->breakfast == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                {{-- 午前 --}}
                <div class="shift-icon-item attendance
                    {{ $shift_pst->am_shift == 1 ? 'selected' : '' }}">
                    <i class="fas fa-sun"></i>
                    <span class="shift-icon-label">
                        午前
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pst->am_shift == 1 ? '✓ 出席' : '欠席' }}
                    </span>
                </div>

                {{-- 昼食 --}}
                <div class="shift-icon-item meal
                    {{ $shift_pst->lunch == 1 ? 'selected' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span class="shift-icon-label">
                        昼食
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pst->lunch == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                {{-- 午後 --}}
                <div class="shift-icon-item attendance
                    {{ $shift_pst->pm_shift == 1 ? 'selected' : '' }}">
                    <i class="fas fa-running"></i>
                    <span class="shift-icon-label">
                        午後
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pst->pm_shift == 1 ? '✓ 出席' : '欠席' }}
                    </span>
                </div>

                {{-- 夕食 --}}
                <div class="shift-icon-item meal
                    {{ $shift_pst->dinner == 1 ? 'selected' : '' }}">
                    <i class="fas fa-utensils"></i>
                    <span class="shift-icon-label">
                        夕食
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pst->dinner == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                {{-- 宿泊 --}}
                <div class="shift-icon-item stay
                    {{ $shift_pst->stay == 1 ? 'selected' : '' }}">
                    <i class="fas fa-bed"></i>
                    <span class="shift-icon-label">
                        宿泊
                    </span>
                    <span class="shift-icon-state">
                        {{ $shift_pst->stay == 1 ? '✓ あり' : 'なし' }}
                    </span>
                </div>

                @if(($user->property & 0x10) > 0)
                    {{-- 往路 --}}
                    <div class="shift-icon-item transport
                        {{ $shift_pst->bus_outward == 1 ? 'selected' : '' }}">
                        <i class="fas fa-car-side"></i>
                        <span class="shift-icon-label">
                            往路
                        </span>
                        <span class="shift-icon-state">
                            {{ $shift_pst->bus_outward == 1 ? '✓ 必要' : '不要' }}
                        </span>
                    </div>

                    {{-- 復路 --}}
                    <div class="shift-icon-item transport
                        {{ $shift_pst->bus_return == 1 ? 'selected' : '' }}">
                        <i class="fas fa-car-side"></i>
                        <span class="shift-icon-label">
                            復路
                        </span>
                        <span class="shift-icon-state">
                            {{ $shift_pst->bus_return == 1 ? '✓ 必要' : '不要' }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        {{-- 翌日コメント --}}
        @if(!empty($shift_pst->comment))
            <div class="row mb-3">
                <div class="col-12">
                    <div class="shift-comment-readonly">
                        {{ $shift_pst->comment }}
                    </div>
                </div>
            </div>
        @endif

    </form>

    {{-- フラッシュメッセージ --}}
    <div id="flash-message"
         style="display:none;
                background-color:#d4edda;
                color:#155724;
                padding:10px;
                border:1px solid #c3e6cb;
                border-radius:5px;
                margin:10px 0;">
        {{ $message }}
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
        const url = `/datechange/${selectedDate}`;
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
        selectedDate.setDate(selectedDate.getDate() - 1);
        const year = selectedDate.getFullYear();
        const month =
            String(selectedDate.getMonth() + 1).padStart(2, '0');
        const day =
            String(selectedDate.getDate()).padStart(2, '0');
        const previousDate =
            `${year}-${month}-${day}`;
        window.location.href =
            `/datechange/${previousDate}`;
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
        selectedDate.setDate(selectedDate.getDate() + 1);
        const year = selectedDate.getFullYear();
        const month =
            String(selectedDate.getMonth() + 1).padStart(2, '0');
        const day =
            String(selectedDate.getDate()).padStart(2, '0');
        const nextDate =
            `${year}-${month}-${day}`;
        window.location.href =
            `/datechange/${nextDate}`;
    }
);

/*
 * ============================================================
 * アイコンボタンの状態表示
 * ============================================================
 */
function updateShiftIcon(checkbox)
{
    const label =
        document.querySelector(
            'label[for="' + checkbox.id + '"]'
        );
    if (!label) {
        return;
    }
    const state =
        label.querySelector('.shift-icon-state');
    if (checkbox.checked) {
        label.classList.add('selected');
        if (state) {
            state.textContent =
                state.getAttribute('data-on');
        }
    } else {
        label.classList.remove('selected');
        if (state) {
            state.textContent =
                state.getAttribute('data-off');
        }
    }
}

/*
 * ============================================================
 * 初期表示
 * ============================================================
 */
document.addEventListener(
    'DOMContentLoaded',
    function () {
        /*
         * checkboxアイコン
         */
        const checkboxes =
            document.querySelectorAll('.shift-checkbox');
        checkboxes.forEach(function (checkbox) {
            updateShiftIcon(checkbox);
            checkbox.addEventListener(
                'change',
                function () {
                    updateShiftIcon(this);
                }
            );
        });

        /*
         * フラッシュメッセージ
         */
        const flashMessage =
            document.getElementById('flash-message');
        if (flashMessage) {
            flashMessage.style.display = 'block';
            setTimeout(function () {
                flashMessage.style.display = 'none';
            }, 1000);
        }
    }
);
</script>
@endsection
