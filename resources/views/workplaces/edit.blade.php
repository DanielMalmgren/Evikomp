@extends('layouts.app')

@section('title', __('Arbetsplatsinställningar'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

<script type="text/javascript">
    $(function() {
        $('#workplace').change(function(){
            var selectedValue = $(this).val();
            $("#settings").load("/workplaceajax/" + selectedValue);
        }){{$prechosen_workplace?".change()":""}};
    });
</script>

    <H1>Inställningar för arbetsplats</H1>

    @if(count($workplaces) == 1)
        @foreach($workplaces as $workplace)
            <H1>{{$workplace->name}}</H1>
            @include('workplaces.ajax')
        @endforeach
    @elseif(count($workplaces) > 1)
        <select class="custom-select d-block w-100" id="workplace" name="workplace" required="">
            <option disabled selected>Välj arbetsplats...</option>
            @foreach($workplaces as $workplace)
                <option {{$prechosen_workplace==$workplace->id?"selected":""}} value="{{$workplace->id}}">{{$workplace->name}} ({{$workplace->municipality->name}})</option>
            @endforeach
        </select>
    @endif

    <div id="settings"></div>

    @can('add workplaces')
        <br>
        <a href="/workplace/create" class="btn btn-primary">@lang('Lägg till arbetsplats')</a>
    @endcan


@endsection
