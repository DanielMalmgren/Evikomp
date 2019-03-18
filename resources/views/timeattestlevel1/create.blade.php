@extends('layouts.app')

@section('title', __('Närvarorapport'))

@section('content')

    <script type="text/javascript">
        $(function() {
            $("#report").load("/timeattestajaxlevel1/-1");

            $('#year').change(function(){
                var month = $('#month').val();
                $("#report").load("/timeattestajaxlevel1/" + month);
            });
            $('#month').change(function(){
                var month = $('#month').val();
                $("#report").load("/timeattestajaxlevel1/" + month);
            });
        });
    </script>

    <div class="col-md-12">

        <H1>@lang('Attestera närvaro')</H1>
        <form method="post" name="settings" action="{{action('TimeAttestLevel1Controller@store')}}" accept-charset="UTF-8">
            @csrf

            <div class="mb-3">
                <label for="month">@lang('Månad')</label>
                <select class="custom-select d-block w-100" id="month" name="month" required="">
                    <option selected value="-1">@lang('Föregående månad') ({{strftime('%B %Y',strtotime("-1 month"))}})</option>
                    <option value="0">@lang('Innevarande månad') ({{strftime('%B %Y')}})</option>
                </select>
            </div>

            <div id="report"></div>

        </form>
    </div>

@endsection
