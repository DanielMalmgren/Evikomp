@extends('layouts.app')

@section('title', __('Sammanställning till ESF'))

@section('content')

    <script type="text/javascript">
        $(function() {
            $('#rel_month').change(function(){
                var rel_month = $('#rel_month').val();
                $("#monthsummary").load("/timesummaryajax/" + rel_month);
            }).change();
        });
    </script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <div class="col-md-8 mb-3">

        <H1>@lang('Sammanställning till ESF')</H1>
        <form method="get" name="settings" action="{{action('TimeSummaryController@export')}}" accept-charset="UTF-8">
            @csrf

            <div class="mb-3">
                <label for="rel_month">@lang('Månad')</label>
                <select class="custom-select d-block w-100" id="rel_month" name="rel_month" required="">
                    @for ($i = -12; $i <= 0; $i++)
                        <option value="{{$i}}" {{$i==-1?'selected':''}}>{{strftime('%B %Y',incrementDate($i))}}</option>
                    @endfor
                </select>
            </div>

            <div id="monthsummary"></div>

            <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Hämta sammanställningen')</button>
        </form>
    </div>
@endsection
