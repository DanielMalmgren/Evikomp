@extends('layouts.app')

@section('title', __('Spår'))

@section('content')

    <H1>@lang('Spår')</H1>

    @if(count($tracks) > 0)
        <ul class="list-group mb-3 tracks">
            @foreach($tracks as $track)
                <li class="list-group-item d-flex justify-content-between lh-condensed nopadding">
                    <a href="/track/{{$track->id}}">
                        <h6 class="my-0">{{$track->translateOrDefault(App::getLocale())->name}}</h6>
                        <small class="text-muted">{{$track->name}}</small>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        @lang('Du har inga spår valda. Klicka på "Inställningar" för att välja vilka spår som ska visas här!')
    @endif

@endsection
