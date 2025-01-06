@extends('layouts.app')

@section('content')
<div class="container">
    @auth
    <h1>{{ $title }}</h1>

    <div class="row mb-3">
        <div class="col-7">
            <div class="input-group mb-3">
            <label class="input-group-text" for="inputGroup">日時</label>
            <input type="date" class="form-control" id="specifieddate" name="specifieddate" Value="{{ $shift_date }}">
            </div>
        </div>
        <div class="col-5">
        <input type="button" class="btn btn-primary" id="move_pre" value="{{ __('前日') }}">
        <input type="button" class="btn btn-primary" id="move_pst" value="{{ __('翌日') }}">
        </div>
    </div>

    <div class="row mb-6">
        <div class="col-12">
        <h3>{{ $shift_date }} {{ $weekday }}</h3>
        </div>
    </div>

    <div class="row mb-6">
        <div class="col-12">
        <h3 class="detail-h3-color">名前表示</h3>
        </div>
    </div>
    <div class="row mb-3">
        <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table" style="width: 800px;border-collapse: collapse;">
                <thead>
                <tr>
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
                </tr>
                <tr>
                    <td class="headerinfo">{{ $totals['breakfast'] }}</td>
                    <td class="headerinfo">{{ $totals['am_shift'] }}</td>
                    <td class="headerinfo">{{ $totals['lunch'] }}</td>
                    <td class="headerinfo">{{ $totals['pm_shift'] }}</td>
                    <td class="headerinfo">{{ $totals['dinner'] }}</td>
                    <td class="headerinfo">{{ $totals['stay'] }}</td>
                    @if(($user->property & 0x10) > 0)
                    <td class="headerinfo">{{ $totals['bus_outward'] }}</td>
                    <td class="headerinfo">{{ $totals['bus_return'] }}</td>
                    @endif
                </tr>
                </thead>
                <tbody>

                    <tr>
                    <td class="headerinfo">
                    @foreach($names['breakfast'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    <td class="headerinfo">
                    @foreach($names['am_shift'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    <td class="headerinfo">
                    @foreach($names['lunch'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    <td class="headerinfo">
                    @foreach($names['pm_shift'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    <td class="headerinfo">
                    @foreach($names['dinner'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    <td class="headerinfo">
                    @foreach($names['stay'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    @if(($user->property & 0x10) > 0)
                    <td class="headerinfo">
                    @foreach($names['bus_outward'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    <td class="headerinfo">
                    @foreach($names['bus_return'] as $name)
                    <span>{{ $name }}</span></br>
                    @endforeach
                    </td>
                    @endif
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
        <h3 class="detail-h3-color">コメント</h3>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <table class="table" width="100%">
                <thead>
                <tr>
                    <th class="headerinfo">名前</th>
                    <th class="headerinfo">コメント</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($shifts as $shift)
                    @if(!empty($shift->comment))
                    <tr>
                    <td class="headerinfo">{{ $shift->user->name }}</td>
                    <td class="headerinfo">{{ $shift->comment }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
        <h3 class="detail-h3-color">メンバー別表示</h3>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <table class="table" width="100%">
                <thead>
                <tr>
                    <th class="headerinfo">名前</th>
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
                </tr>
                <tr>
                    <td class="headerinfo">合計</td>
                    <td class="headerinfo">{{ $totals['breakfast'] }}</td>
                    <td class="headerinfo">{{ $totals['am_shift'] }}</td>
                    <td class="headerinfo">{{ $totals['lunch'] }}</td>
                    <td class="headerinfo">{{ $totals['pm_shift'] }}</td>
                    <td class="headerinfo">{{ $totals['dinner'] }}</td>
                    <td class="headerinfo">{{ $totals['stay'] }}</td>
                    @if(($user->property & 0x10) > 0)
                    <td class="headerinfo">{{ $totals['bus_outward'] }}</td>
                    <td class="headerinfo">{{ $totals['bus_return'] }}</td>
                    @endif
                </tr>
                </thead>
                <tbody>
                    @foreach($shifts as $shift)
                    <tr>
                    <td class="headerinfo">{{ $shift->user->name }}</td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->breakfast == 1 ? 'checked' : '' }} id="tdy_breakfast"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->am_shift == 1 ? 'checked' : '' }} id="tdy_am_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->lunch == 1 ? 'checked' : '' }} id="tdy_lunch"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->pm_shift == 1 ? 'checked' : '' }} id="tdy_pm_shift"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->dinner == 1 ? 'checked' : '' }} id="tdy_dinner"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->stay == 1 ? 'checked' : '' }} id="tdy_stay"></td>
                    @if(($user->property & 0x10) > 0)
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->bus_outward == 1 ? 'checked' : '' }} id="tdy_bus_outward"></td>
                    <td class="headerinfo"><input class="form-check-input" type="checkbox" disabled {{ $shift->bus_return == 1 ? 'checked' : '' }} id="tdy_bus_return"></td>
                    @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    @else
    @endauth
</div>

<script>
    document.getElementById('specifieddate').addEventListener('change', function () {
        const selectedDate = this.value; // 選択された日付
        const url = `/specified/${selectedDate}`;
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
        const url = `/specified/${previousDate}`;
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
        const url = `/specified/${nextDate}`;
        window.location.href = url; // 指定した URL にリダイレクト
    });

</script>

@endsection
