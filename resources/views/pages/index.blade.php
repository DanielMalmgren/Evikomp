@extends('layouts.app')

@section('title', __('Startsida'))

@section('content')

@if($should_attest)
    <div class="new border border-danger importantnotification">
        <h1>@lang('Du har tid att attestera för') {{$monthstr}}!</h1>

        <a href="/timeattestlevel1/create" class="btn btn-primary">@lang('Gå till attesteringen')</a>
        <br>

    </div>
    <br>
@endif


@isset($poll)
    <div class="new border border-danger importantnotification">
        <h1>@lang('Du har en enkät att fylla i!')</h1>

        <a href="/poll/{{$poll->id}}" class="btn btn-primary">@lang('Gå till enkäten')</a>
        <br>

    </div>
    <br>
@endif

@can('see beta features')
    @if(isset($shared_lists) && $shared_lists->isNotEmpty())
        <H1>@lang('Listor')</H1>

        @foreach($shared_lists as $list)
            <a class="list-group-item list-group-item-action" onClick="window.location='/lists/{{$list->id}}'">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        {{$list->name}}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-2">
                        {{$list->user->name}}
                    </div>
                </div>
            </a>
        @endforeach
        <br>
        <a href="/lists" class="btn btn-secondary">@lang('Redigera listor')</a>
        <br><br>
    @endif
@endcan

<H1>@lang('Nyheter')</H1>

@if(count($announcements) > 0)
    <div class="list-group mb-4">
        @foreach($announcements as $announcement)
            <a href="/announcements/{{$announcement->id}}" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{$announcement->heading}}</h5>
                    <small>{{\Carbon\Carbon::parse($announcement->created_at)->format('Y-m-d')}}</small>
                </div>
                <p class="mb-1">{{$announcement->preamble}}</p>
            </a>
        @endforeach
      </div>
@endif

@can('manage announcements')
    <a href="/announcements/create" class="btn btn-primary">@lang('Skapa nytt meddelande')</a>
@endcan

@endsection
