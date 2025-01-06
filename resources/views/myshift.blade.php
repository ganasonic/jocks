@extends('layouts.app')

@section('content')
<div class="container">
    @auth
    <h1>{{ $title }}{{__('管理@')}}{{ $user->name }}</h1>
    <form action="/shiftupdate/{{ $user->id }}" method="POST">
    @csrf
    <input type="hidden" value="0" name="tdy_breakfast">
    <input type="hidden" value="0" name="tdy_am_shift">
    <input type="hidden" value="0" name="tdy_lunch">
    <input type="hidden" value="0" name="tdy_pm_shift">
    <input type="hidden" value="0" name="tdy_dinner">
    <input type="hidden" value="0" name="tdy_stay">
    <input type="hidden" value="0" name="tdy_bus_outward">
    <input type="hidden" value="0" name="tdy_bus_return">

    <div class="row mb-3">
        <div class="col-8">
            <div class="input-group mb-3">
            <label class="input-group-text" for="inputGroup">登録日</label>
            <input type="date" class="form-control" id="specifieddate" name="specifieddate" Value="{{ $shift->shift_date }}">
            </div>
        </div>
        <div class="col-4">
            <button type="submit" class="btn btn-success">{{ __('更新') }}</button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
        <h3 class="myshift-h3-color">{{ __('前日') }} {{ $shift_pre->shift_date }} {{ $shift_pre->weekday }}</h3>
        </div>
    </div>
    <div class="row mb-3">
        @if($shift_pre->breakfast == 1 || $shift_pre->am_shift == 1 || $shift_pre->lunch == 1 || $shift_pre->pm_shift == 1
        || $shift_pre->dinner == 1 || $shift_pre->stay == 1 || $shift_pre->bus_outward == 1 || $shift_pre->bus_return == 1)
        <div class="col-12">
            <table class="table table-borderless" width="100%">
                <thead>
                    <th class="headerinfo">朝食</th>
                    <th class="headerinfo">午前</th>
                    <th class="headerinfo">昼食</th>
                    <th class="headerinfo">午後</th>
                    <th class="headerinfo">夕食</th>
                    <th class="headerinfo">宿泊</th>
                    @if(($user->property & 0x10) > 0)
                    <th class="headerinfo">往路</th>
                    <th class="headerinfo">復路</th>
                    @endif
                </thead>
                <tbody>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->breakfast == 1 ? 'checked' : '' }} name="pre_breakfast" id="pre_breakfast"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->am_shift == 1 ? 'checked' : '' }} name="pre_am_shift" id="pre_am_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->lunch == 1 ? 'checked' : '' }} name="pre_lunch" id="pre_lunch"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->pm_shift == 1 ? 'checked' : '' }} name="pre_pm_shift" id="pre_pm_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->dinner == 1 ? 'checked' : '' }} name="pre_dinner" id="pre_dinner"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->stay == 1 ? 'checked' : '' }} name="pre_stay" id="pre_stay"></td>
                    @if(($user->property & 0x10) > 0)
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->bus_outward == 1 ? 'checked' : '' }} name="pre_bus_outward" id="pre_bus_outward"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pre->bus_return == 1 ? 'checked' : '' }} name="pre_bus_return" id="pre_bus_return"></td>
                    @endif
                </tbody>
            </table>
        </div>
        @endif
    </div>
    <div class="row mb-3">
        <div class="form-floating">
        <span>{{ $shift_pre->comment }}</span>
        </div>
    </div>
    <div class="row mb-3">
    </div>
    <div class="row mb-3">
        <div class="col-6">
            <h3 class="myshift-h3-color">{{ __('当日') }}</br>{{ $shift->shift_date }} {{ $shift->weekday }}</h3>
        </div>
        <div class="col-6">
            <input type="button" class="btn btn-primary" id="move_pre" value="{{ __('前日') }}">
            <input type="button" class="btn btn-primary" id="move_pst" value="{{ __('翌日') }}">
            <button type="submit" class="btn btn-success">{{ __('更新') }}</button>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <table class="table-primary" width="100%">
                <thead>
                    <th class="headerinfo">朝食</th>
                    <th class="headerinfo">午前</th>
                    <th class="headerinfo">昼食</th>
                    <th class="headerinfo">午後</th>
                    <th class="headerinfo">夕食</th>
                    <th class="headerinfo">宿泊</th>
                    @if(($user->property & 0x10) > 0)
                    <th class="headerinfo">往路</th>
                    <th class="headerinfo">復路</th>
                    @endif
                </thead>
                <tbody>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->breakfast == 1 ? 'checked' : '' }} name="tdy_breakfast" id="tdy_breakfast"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->am_shift == 1 ? 'checked' : '' }} name="tdy_am_shift" id="tdy_am_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->lunch == 1 ? 'checked' : '' }} name="tdy_lunch" id="tdy_lunch"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->pm_shift == 1 ? 'checked' : '' }} name="tdy_pm_shift" id="tdy_pm_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->dinner == 1 ? 'checked' : '' }} name="tdy_dinner" id="tdy_dinner"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->stay == 1 ? 'checked' : '' }} name="tdy_stay" id="tdy_stay"></td>
                    @if(($user->property & 0x10) > 0)
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->bus_outward == 1 ? 'checked' : '' }} name="tdy_bus_outward" id="tdy_bus_outward"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" value="1" {{ $shift->bus_return == 1 ? 'checked' : '' }} name="tdy_bus_return" id="tdy_bus_return"></td>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="row mb-3">
        <div class="form-floating">
            <textarea class="form-control" placeholder="Leave a comment here" name="tdy_comment" id="tdy_comment" style="height: 100px">{{ $shift->comment }}</textarea>
            <label for="floatingTextarea">コメント</label>
        </div>
    </div>
    <div class="row mb-3">
    </div>
    <div class="row mb-3">
        <div class="col-12">
        <h3 class="myshift-h3-color">{{ __('翌日') }} {{ $shift_pst->shift_date }} {{ $shift_pst->weekday }}</h3>
        </div>
    </div>

    <div class="row mb-3">
        @if($shift_pst->breakfast == 1 || $shift_pst->am_shift == 1 || $shift_pst->lunch == 1 || $shift_pst->pm_shift == 1
        || $shift_pst->dinner == 1 || $shift_pst->stay == 1 || $shift_pst->bus_outward == 1 || $shift_pst->bus_return == 1)
        <div class="col-12">
            <table class="table table-borderless" width="100%">
                <thead>
                    <th class="headerinfo">朝食</th>
                    <th class="headerinfo">午前</th>
                    <th class="headerinfo">昼食</th>
                    <th class="headerinfo">午後</th>
                    <th class="headerinfo">夕食</th>
                    <th class="headerinfo">宿泊</th>
                    @if(($user->property & 0x10) > 0)
                    <th class="headerinfo">往路</th>
                    <th class="headerinfo">復路</th>
                    @endif
                </thead>
                <tbody>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->breakfast == 1 ? 'checked' : '' }} name="pst_breakfast" id="pst_breakfast"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->am_shift == 1 ? 'checked' : '' }} name="pst_am_shift" id="pst_am_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->lunch == 1 ? 'checked' : '' }} name="pst_lunch" id="pst_lunch"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->pm_shift == 1 ? 'checked' : '' }} name="pst_pm_shift" id="pst_pm_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->dinner == 1 ? 'checked' : '' }} name="pst_dinner" id="pst_dinner"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->stay == 1 ? 'checked' : '' }} name="pst_stay" id="pst_stay"></td>
                    @if(($user->property & 0x10) > 0)
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->bus_outward == 1 ? 'checked' : '' }} name="pst_bus_outward" id="pst_bus_outward"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift_pst->bus_return == 1 ? 'checked' : '' }} name="pst_bus_return" id="pst_bus_return"></td>
                    @endif
                </tbody>
            </table>
        </div>
        @endif
    </div>
    <div class="row mb-3">
        <div class="form-floating">
            <span>{{ $shift_pst->comment }}</span>
        </div>
    </div>
    </form>
    <div id="flash-message" style="display: none; background-color: #d4edda; color: #155724; padding: 10px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;">
        {{ $message }}
    </div>

    @else
    @endauth
</div>

<script>
    document.getElementById('specifieddate').addEventListener('change', function () {
        const selectedDate = this.value; // 選択された日付
        const url = `/datechange/${selectedDate}`;
        window.location.href = url; // 指定した URL にリダイレクト
    });
    document.getElementById('move_pre').addEventListener('click', function () {
        // 指定された日付を取得
        const selectedDate = new Date(document.getElementById('specifieddate').value);

        // 前日を計算
        selectedDate.setDate(selectedDate.getDate() - 1);

        // 前日の値をYYYY-MM-DD形式にフォーマット
        const year = selectedDate.getFullYear();
        const month = String(selectedDate.getMonth() + 1).padStart(2, '0'); // 月は0始まりなので+1
        const day = String(selectedDate.getDate()).padStart(2, '0');

        const previousDate = `${year}-${month}-${day}`;

        // URLを構築してリダイレクト
        const url = `/datechange/${previousDate}`;
        window.location.href = url; // 指定した URL にリダイレクト
    });
    document.getElementById('move_pst').addEventListener('click', function () {
        // 指定された日付を取得
        const selectedDate = new Date(document.getElementById('specifieddate').value);

        // 翌日を計算
        selectedDate.setDate(selectedDate.getDate() + 1);

        // 翌日の値をYYYY-MM-DD形式にフォーマット
        const year = selectedDate.getFullYear();
        const month = String(selectedDate.getMonth() + 1).padStart(2, '0'); // 月は0始まりなので+1
        const day = String(selectedDate.getDate()).padStart(2, '0');

        const nextDate = `${year}-${month}-${day}`;

        // URLを構築してリダイレクト
        const url = `/datechange/${nextDate}`;
        window.location.href = url; // 指定した URL にリダイレクト
    });

    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            // メッセージを表示
            flashMessage.style.display = 'block';

            // 2秒後に非表示にする
            setTimeout(() => {
                flashMessage.style.display = 'none';
            }, 1000);
        }
    });

</script>


<script>
@endsection
