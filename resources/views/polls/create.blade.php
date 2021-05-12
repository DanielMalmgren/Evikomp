
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <x-trumbowyg-includes/>

    <form method="post" action="{{action('PollController@store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name">
        </div>

        <div class="mb-3">
            <label for="infotext">@lang('Informationstext före')</label>
            <textarea rows="4" name="infotext" class="form-control twe"></textarea>
        </div>

        <div class="mb-3">
            <label for="infotext2">@lang('Informationstext efter')</label>
            <textarea rows="4" name="infotext2" class="form-control twe"></textarea>
        </div>

        @lang('Målgrupp:') <br>
        <select id="workplaces" name="workplaces[]" multiple="multiple">
            @foreach($workplaces as $workplace)
                <option value="{{$workplace->id}}" data-section="{{$workplace->municipality->name}}">{{$workplace->name}}</option>
            @endforeach
        </select>

        <div class="mb-3">
            <select class="custom-select d-block w-100" name="scope_terms_of_employment" required="">
                <option value="0">@lang('Samtliga')</option>
                <option value="1">@lang('Tillsvidareanställning')</option>
                <option value="2">@lang('Tidsbegränsad anställning')</option>
                <option value="3">@lang('Vet ej')</option>
            </select>
        </div>

        <div class="mb-3">
            <select class="custom-select d-block w-100" name="scope_full_or_part_time" required="">
                <option value="0">@lang('Samtliga')</option>
                <option value="1">@lang('Deltid')</option>
                <option value="2">@lang('Heltid')</option>
                <option value="3">@lang('Vet ej')</option>
            </select>
        </div>

        <br><br>

        @lang('mellan')
        <input type="date" name="active_from" class="form-control">
        @lang('och')
        <input type="date" name="active_to" class="form-control">

        <br>

        <button class="btn btn-primary btn-lg btn-primary" type="submit">@lang('Spara')</button>

    </form>

    <script type="text/javascript">
        $('.twe').trumbowyg({
            <x-trumbowyg-settings/>
        });
    </script>


    <link href="/tree-multiselect/jquery.tree-multiselect.min.css" rel="stylesheet">
    <script src="/tree-multiselect/jquery.tree-multiselect.min.js"></script>
    <script type="text/javascript">
    	$("select#workplaces").treeMultiselect({
            startCollapsed: true,
            hideSidePanel: true
        });
    </script>

@endsection
