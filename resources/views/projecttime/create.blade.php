@extends('layouts.app')

@section('title', __('Registrera projekttid'))

@section('content')

<script type="text/javascript">
    $(function() {
        $('#workplace').change(function(){
            var selectedValue = $(this).val();
            $("#settings").load("/projecttimeajax/" + selectedValue);
        });
    });
</script>

<div class="col-md-5 mb-3">

    <H1>Registrera projekttid</H1>

    @if(count($workplaces) == 1)
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

    <div id="settings"></div>

</div>

@endsection
