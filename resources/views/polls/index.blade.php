@extends('layouts.app')

@section('title', __('Enkäter'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Enkäter')</H1>

    @if(count($polls) > 0)
            @foreach($polls as $poll)
            <a class="list-group-item list-group-item-action" id="user-{{$poll->id}}">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        {{$poll->translateOrDefault(App::getLocale())->name}}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-2">
                        {{$poll->active_from}} - {{$poll->active_to}}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-2">
                        @lang('Inlämnade svar: '){{$poll->poll_sessions->where('finished', true)->count()}}
                        @lang(' av '){{$poll->users_count()}}
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <i class="fas fa-copy" onClick="window.location='/poll/{{$poll->id}}/replicate'"></i>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <i class="fas fa-edit" onClick="window.location='/poll/{{$poll->id}}/edit'"></i>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <i class="fas fa-file-excel" onClick="window.location='/poll/{{$poll->id}}/exportresponses'"></i>
                    </div>
                </div>
            </a>


            @endforeach

            <br>
    @endif

    <a href="/poll/create" class="btn btn-primary">@lang('Skapa ny enkät')</a>

@endsection
