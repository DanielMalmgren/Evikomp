@extends('layouts.app')

@section('title', __('Registrera lärtillfälle'))

@section('content')

    <script type="text/javascript">
        $(function() {
            $('#workplace').change(function(){
                var selectedValue = $(this).val();
                $("#settings").load("/projecttimeajax/" + selectedValue + "?date={{$date}}&time={{$time}}&allDay={{$allDay}}");
            });
        });
    </script>

    <div class="col-md-8 mb-3">

        <H1>Registrera lärtillfälle</H1>

        @if(count($workplaces) == 0)
            @php
                $workplace = \Auth::user()->workplace;
                $singleuser = true;
            @endphp
            @include('projecttime.ajax')
        @elseif(count($workplaces) == 1)
            @foreach($workplaces as $workplace)
                <H1>{{$workplace->name}}</H1>
                @include('projecttime.ajax')
            @endforeach
        @elseif(count($workplaces) > 1)
            <select class="custom-select d-block w-100" id="workplace" name="workplace" required="">
                <option disabled selected>Välj arbetsplats...</option>
                @foreach($workplaces as $workplace)
                    <option value="{{$workplace->id}}">{{$workplace->name}}</option>
                @endforeach
            </select>
        @endif

        <br>

        <div id="settings">
            @if(count($workplaces) > 1 && old('workplace_id'))
                @php
                    $workplace = \App\Workplace::find(old('workplace_id'))
                @endphp
                @include('projecttime.ajax')
            @endif
        </div>

    </div>

@endsection
