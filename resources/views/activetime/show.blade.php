@extends('layouts.app')

@section('title', __('Närvarorapport'))

@section('content')

    <script type="text/javascript">
        $(function() {
            $("#report").load("/activetimeajax/" + 2019 + "/" + 2);

            $('#year').change(function(){
                var year = $('#year').val();
                var month = $('#month').val();
                $("#report").load("/activetimeajax/" + year + "/" + month);
            });
            $('#month').change(function(){
                var year = $('#year').val();
                var month = $('#month').val();
                $("#report").load("/activetimeajax/" + year + "/" + month);
            });
        });
    </script>

    <div class="col-md-5 mb-3">

        <H1>@lang('Närvarorapport för ESF')</H1>
        <form method="get" name="settings" action="{{action('ActiveTimeController@export')}}" accept-charset="UTF-8">
            @csrf

            <div class="mb-3">
                <label for="year">@lang('År')</label>
                <select class="custom-select d-block w-100" id="year" name="year" required="">
                    <option value="{{date('Y')-1}}">{{date('Y')-1}}</option>
                    <option selected value="{{date('Y')}}">{{date('Y')}}</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="month">@lang('Månad')</label>
                <select class="custom-select d-block w-100" id="month" name="month" required="">
                    @for ($i = 1; $i < 12; $i++)
                        <option value="{{$i}}" {{$i==date('n')-1?"selected":""}}>{{strftime('%B', strtotime('2000-'.$i.'-15'))}}</option>
                    @endfor
                </select>
            </div>

            <br>
            <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Hämta närvarorapport (Excel)')</button>
        </form>
    </div>

    <br>

    <div id="report"></div>

@endsection
