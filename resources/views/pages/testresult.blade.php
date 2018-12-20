@extends('layouts.app')

@section('content')

    <H1>@lang('Testresultat')</H1>

    @lang('Grattis, du hade rätt på :percent% av frågorna på första försöket!', ['percent' => $percent])

    <br><br>

    <div>
        @if($percent>49)
            <img class="resultstar" src="/images/Star_happy.png">
        @else
            <img class="resultstar" src="/images/Star_unhappy.png">
        @endif
        @if($percent>74)
            <img class="resultstar" src="/images/Star_happy.png">
        @else
            <img class="resultstar" src="/images/Star_unhappy.png">
        @endif
        @if($percent==100)
            <img class="resultstar" src="/images/Star_happy.png">
        @else
            <img class="resultstar" src="/images/Star_unhappy.png">
        @endif
    </div>

@endsection
