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
                                <th>
                                    日付
                                </th>
                                <th class="text-center my-status-header">
                                    {{ $user->name }}
                                </th>
                                <th class="text-center">
                                    宿泊人数
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($stays as $index => $stay)
                                <tr class="{{ $index === 0 ? 'today-row' : '' }}">
                                    <td class="align-middle">
                                        <div class="fw-bold">
                                            {{ $stay['date'] }}
                                            {{ $stay['weekday'] }}
                                        </div>
                                        @if($index === 0)
                                            <span class="badge bg-primary mt-1">
                                                今日
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 本人 --}}
                                    <td class="text-center align-middle">
                                        <div class="stay-status-item {{ $stay['mine']['stay'] ? 'active' : '' }}">
                                            <i class="fas fa-bed"></i>
                                            <span>
                                                {{ $stay['mine']['stay'] ? '宿泊' : 'なし' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- 全体 --}}
                                    <td class="text-center align-middle fs-5 fw-bold">
                                        {{ $stay['total'] }}
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
