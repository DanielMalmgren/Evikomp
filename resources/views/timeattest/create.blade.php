@extends('layouts.app')

@section('title', 'Attestera projekttid')

@section('content')

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <script type="text/javascript">
        $(function() {
            function updatelist(){
                var workplace = $('#workplace').val();
                var month = $('#month').val();
                var year = $('#year').val();
                $("#attestlist").load("/timeattestajaxuserlist/" + workplace + "/" + year + "/" + month);
            }

            $('#workplace').change(function(){
                updatelist();
            });

            $('#month').change(function(){
                updatelist();
            });

            @if(count($workplaces) == 1)
                updatelist();
            @endif
        });
    </script>

    <H1>@lang('Attestera projekttid') - {{strftime('%B %Y', strtotime("first day of previous month"))}}</H1>
    <form method="post" name="settings" action="{{action('TimeAttestController@store')}}" accept-charset="UTF-8">
        @csrf

        @if(count($workplaces) == 1)
            @foreach($workplaces as $workplace)
                <H1>{{$workplace->name}}</H1>
                <input type="hidden" name="workplace" id="workplace" value="{{$workplace->id}}">
            @endforeach
        @elseif(count($workplaces) > 1)
            <select class="custom-select d-block w-100" id="workplace" name="workplace" required="">
                <option disabled selected>@lang('Välj arbetsplats...')</option>
                @foreach($workplaces as $workplace)
                    <option value="{{$workplace->id}}">{{$workplace->name}}</option>
                @endforeach
            </select>
        @endif

        <br>

        {{--<div class="mb-3">
            <select class="custom-select d-block w-100" id="month" name="month" required="">
                <option selected value="-1">@lang('Föregående månad') ({{strftime('%B %Y',strtotime("-1 month"))}})</option>
                <option value="0">@lang('Innevarande månad') ({{strftime('%B %Y')}})</option>
            </select>
        </div>--}}

        <div class="list-group mb-4" id="attestlist"></div>

        <input type="hidden" name="month" id="month" value="{{$month}}">
        <input type="hidden" name="year" id="year" value="{{$year}}">

    </form>

@endsection
