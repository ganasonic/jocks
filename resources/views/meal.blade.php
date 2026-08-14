@extends('layouts.app')
@section('content')
<div class="container">
    @auth
        <h1>{{ $title }}</h1>
        <div class="row mb-3">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle shift-summary-table">
                        <thead>
                            <tr>
                                <th rowspan="2" class="align-middle">
                                    日付
                                </th>
                                <th rowspan="2" class="text-center align-middle my-status-header">
                                    {{ $user->name }}
                                </th>
                                <th colspan="3" class="text-center">
                                    全体
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center">朝食</th>
                                <th class="text-center">昼食</th>
                                <th class="text-center">夕食</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($meals as $index => $meal)
                                <tr class="{{ $index === 0 ? 'today-row' : '' }}">
                                    <td class="align-middle">
                                        <div class="fw-bold">
                                            {{ $meal['date'] }}
                                            {{ $meal['weekday'] }}
                                        </div>
                                        @if($index === 0)
                                            <span class="badge bg-primary mt-1">
                                                今日
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 本人の食事予定 --}}
                                    <td class="text-center align-middle">
                                        <div class="meal-status-icons">
                                            {{-- 朝食 --}}
                                            <div class="meal-status-item {{ $meal['mine']['breakfast'] ? 'active' : '' }}">
                                                <i class="fas fa-mug-hot"></i>
                                                <span>朝</span>
                                            </div>

                                            {{-- 昼食 --}}
                                            <div class="meal-status-item {{ $meal['mine']['lunch'] ? 'active' : '' }}">
                                                <i class="fas fa-utensils"></i>
                                                <span>昼</span>
                                            </div>

                                            {{-- 夕食 --}}
                                            <div class="meal-status-item {{ $meal['mine']['dinner'] ? 'active' : '' }}">
                                                <i class="fas fa-utensils"></i>
                                                <span>夕</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 全体集計 --}}
                                    <td class="text-center fs-5">
                                        {{ $meal['totals']['breakfast'] }}
                                    </td>
                                    <td class="text-center fs-5">
                                        {{ $meal['totals']['lunch'] }}
                                    </td>
                                    <td class="text-center fs-5">
                                        {{ $meal['totals']['dinner'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endauth
</div>
@endsection
