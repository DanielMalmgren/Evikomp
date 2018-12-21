@extends('layouts.app')

@section('content')

    <H1>@lang('Testresultat')</H1>

    @lang('Grattis, du hade rätt på :percent% av frågorna på första försöket!', ['percent' => $test_session->percent()])

    <br><br>

    <div>
        @if($test_session->percent()>49)
            <img class="resultstar" src="/images/Star_happy.png">
        @else
            <img class="resultstar" src="/images/Star_unhappy.png">
        @endif
        @if($test_session->percent()>74)
            <img class="resultstar" src="/images/Star_happy.png">
        @else
            <img class="resultstar" src="/images/Star_unhappy.png">
        @endif
        @if($test_session->percent()==100)
            <img class="resultstar" src="/images/Star_happy.png">
        @else
            <img class="resultstar" src="/images/Star_unhappy.png">
        @endif
    </div>

@endsection
