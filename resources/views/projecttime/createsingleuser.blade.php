@extends('layouts.app')

@section('title', __('Registrera projekttid'))

@section('content')

{{--<script type="text/javascript" src="/tempusdominus/moment-with-locales.js"></script>
<script type="text/javascript" src="/tempusdominus/tempusdominus-bootstrap-4.min.js"></script>
<link rel="stylesheet" href="/tempusdominus/tempusdominus-bootstrap-4.min.css" />--}}

<script type="text/javascript">
    $(function() {
        $('.time').focusout(function(){
            var selectedValue = $(this).val();
            if(selectedValue.length == 4) {
                if(isNaN(selectedValue)) {
                    $(this).val('0'.concat(selectedValue));
                } else {
                    $(this).val(selectedValue.substring(0,2).concat(':', selectedValue.substring(2,4)));
                }
                selectedValue = $(this).val();
            }
            if(selectedValue != '' && selectedValue.substring(2,3) != ':') {
                alert('Tidpunkterna måste vara i formatet hh:mm!');
                $(this).val('');
            }
            if(selectedValue.substring(0,2) == '00') {
                if(!confirm('Den tid du försöker ange är mitt i natten.\nÄr du säker på att det är detta du vill?')) {
                    $(this).val('');
                }
            }
        });
    });
</script>

<div class="col-md-5 mb-3">

    <H1>@lang('Registrera projekttid')</H1>

    <form method="post" name="question" action="{{action('ProjectTimeController@store')}}" accept-charset="UTF-8">
        @csrf

        <input type="hidden" name="workplace_id" value="{{$workplace->id}}">
        <input type="hidden" name="return_url" value="/projecttime/createsingleuser">

        <select class="custom-select d-block w-100" id="type" name="type" required="">
            @foreach($project_time_types as $type)
                <option value="{{$type->id}}">{{$type->name}}</option>
            @endforeach
        </select>

        <br>

        <div class="mb-3">
            <label for="date">@lang('Datum')</label>
            <input type="date" name="date" min="{{$mindate}}" max="{{date("Y-m-d")}}" class="form-control" value="{{old('date')}}">

            {{--<div style="overflow:hidden;">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="date" name="date"></div>
                        </div>
                    </div>
                </div>
                <script type="text/javascript">
                    $(function () {
                        $('#date').datetimepicker({
                            format: 'L',
                            locale: 'sv',
                            inline: true,
                            sideBySide: true,
                            minDate: '{{$mindate}}',
                            maxDate: '{{date("Y-m-d")}}'
                        });
                    });
                </script>
            </div>--}}

        </div>

        <div class="mb-3">
            <div class="row container">
                <div class="mb-3">
                    <label for="starttime">@lang('Från')</label>
                    <input type="time" name="starttime" class="form-control time" value="{{old('starttime')}}">
                </div>
                <div class="mb-3">
                    <label for="endtime">@lang('Till')</label>
                    <input type="time" name="endtime" class="form-control time" value="{{old('endtime')}}">
                </div>
            </div>
        </div>

        <input type="hidden" name="users[]" value="{{$user->id}}" id="{{$user->id}}">

        <br>

        <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
    </form>


</div>

@endsection
