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
                                <th class="headerinfo">
                                    日付
                                </th>

                                <th class="headerinfo text-center">
                                    寮宿泊
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($stays as $stay)

                                <tr>

                                    <td class="headerinfo">
                                        {{ $stay['date'] }}
                                        {{ $stay['weekday'] }}
                                    </td>

                                    <td class="headerinfo text-center">
                                        {{ $stay['total'] }}
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
