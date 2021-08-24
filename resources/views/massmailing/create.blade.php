
@extends('layouts.app')

@section('title', __('Massutskick'))

@section('content')

    <x-trumbowyg-includes/>

    <H1>@lang('Skicka ut massmail')</H1>

    <form method="post" onsubmit="return validate(this);" action="{{action('MassMailingController@store')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="subject">@lang('Rubrik')</label>
            <input name="subject" class="form-control" id="subject" value="{{old('subject')}}">
        </div>

        <div class="mb-3">
            <label for="body">@lang('Meddelandetext')</label>
            <textarea rows="6" name="body" class="form-control twe">
            @isset($connectedPoll)
                <br>
                <a href="{{env('APP_URL')}}/poll/{{$connectedPoll->id}}">
                    @lang('Länk till enkät')
                </a>
            @endisset
            </textarea>
        </div>

        @lang('Målgrupp:') <br>
        {{--<select id="workplaces" name="workplaces[]" multiple="multiple">
            @foreach($workplaces as $workplace)
                <option {{$connectedPoll&&$connectedPoll->workplaces->contains('id', $workplace->id)?"selected":""}} value="{{$workplace->id}}" data-section="{{$workplace->municipality->name}}">{{$workplace->name}}</option>
            @endforeach
        </select>--}}

        <select id="users" name="users[]" multiple="multiple">
            @foreach($users as $user)
            {{-- Fixa kollen för selected nedan. Ska vara vald om arbetsplatsen är med i målgruppen
                 och användaren inte redan har fyllt i enkäten --}}
                <option {{$connectedPoll&&$connectedPoll->workplaces->contains('id', $user->workplace->id)&&$user->poll_sessions->where('finished', true)->where('poll_id', $connectedPoll->id)->isEmpty()?"selected":""}} value="{{$user->id}}" data-section="{{$user->workplace->municipality->name}}/{{$user->workplace->name}}">{{$user->name}}</option>
            @endforeach
        </select>

        {{--<div class="mb-3 col-md-6">
            <label for="poll">@lang('Skicka endast till användare som inte besvarat nedanstående enkät')</label>
            <select class="custom-select d-block w-100" id="poll" name="poll" required="">
                <option value="-1">@lang('Ingen koppling till enkät (skicka till alla)')</option>
                @foreach($polls as $poll)
                    <option {{$connectedPoll&&$connectedPoll->id==$poll->id?"selected":""}} value="{{$poll->id}}">{{$poll->translateOrDefault(App::getLocale())->name}}</option>
                @endforeach
            </select>
        </div>--}}

        <br><br>

        <button class="btn btn-primary btn-lg btn-primary" type="submit">@lang('Skicka')</button>

    </form>

    <script type="text/javascript">
        function validate(form) {
            checked = document.querySelectorAll('input[type="checkbox"]:checked.option').length;
            return confirm("@lang('Detta kommer att skicka e-post till ')" + checked + "@lang(' personer. Är du säker?')");
        }

        $('.twe').trumbowyg({
            <x-trumbowyg-settings/>
        });
    </script>

    <link href="/tree-multiselect/jquery.tree-multiselect.min.css" rel="stylesheet">
    <script src="/tree-multiselect/jquery.tree-multiselect.min.js"></script>
    <script type="text/javascript">
    	$("select#users").treeMultiselect({
            startCollapsed: true,
            hideSidePanel: true
        });
    </script>

@endsection
