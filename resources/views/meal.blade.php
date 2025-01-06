@extends('layouts.app')

@section('content')
<div class="container">
    @auth
    <h1>{{ $title }}</h1>
    <div class="row mb-3">
        <div class="col-12">
            <table class="table" width="100%">
                <thead>
                <tr>
                    <th class="headerinfo">日付</th>
                    <th class="headerinfo">寮朝食</th>
                    <th class="headerinfo">昼食</th>
                    <th class="headerinfo">寮夕食</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                    <td class="headerinfo">{{ $shift_date }}{{ $weekday }}</td>
                    <td class="headerinfo">{{ $totals['breakfast'] }}</td>
                    <td class="headerinfo">{{ $totals['lunch'] }}</td>
                    <td class="headerinfo">{{ $totals['dinner'] }}</td>
                    </tr>
                    <tr>
                    <td class="headerinfo">{{ $shift_date1 }}{{ $weekday1 }}</td>
                    <td class="headerinfo">{{ $totals1['breakfast'] }}</td>
                    <td class="headerinfo">{{ $totals1['lunch'] }}</td>
                    <td class="headerinfo">{{ $totals1['dinner'] }}</td>
                    </tr>
                    <tr>
                    <td class="headerinfo">{{ $shift_date2 }}{{ $weekday2 }}</td>
                    <td class="headerinfo">{{ $totals2['breakfast'] }}</td>
                    <td class="headerinfo">{{ $totals2['lunch'] }}</td>
                    <td class="headerinfo">{{ $totals2['dinner'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>



    @else
    @endauth
</div>
<script>
@endsection
