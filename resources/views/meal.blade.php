@extends('layouts.app')

@section('content')

<div class="container">

    @auth

        <h1>{{ $title }}</h1>

        <div class="row mb-3">

            <div class="col-12">

                <div class="table-responsive">

                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-light">

                            <tr>
                                <th class="headerinfo">日付</th>
                                <th class="headerinfo text-center">寮朝食</th>
                                <th class="headerinfo text-center">昼食</th>
                                <th class="headerinfo text-center">寮夕食</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($meals as $meal)

                                <tr>

                                    <td class="headerinfo">
                                        {{ $meal['date'] }}
                                        {{ $meal['weekday'] }}
                                    </td>

                                    <td class="headerinfo text-center">
                                        {{ $meal['totals']['breakfast'] }}
                                    </td>

                                    <td class="headerinfo text-center">
                                        {{ $meal['totals']['lunch'] }}
                                    </td>

                                    <td class="headerinfo text-center">
                                        {{ $meal['totals']['dinner'] }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @else

    @endauth

</div>

@endsection
