@extends('layouts.app')

@section('content')

    <H1>@lang('Spår')</H1>

    @if(count($tracks) > 0)
        <ul class="list-group mb-3">
            @foreach($tracks as $track)
                <li class="list-group-item d-flex justify-content-between lh-condensed">
                    <a href="/track/{{$track->id}}">
                        <div>
                        <h6 class="my-0">{{$track->translateOrDefault(App::getLocale())->name}}</h6>
                        <small class="text-muted">{{$track->name}}</small>
                        </div>
                    </a>
                    <span class="text-muted">whatever</span>
                  </li>
            @endforeach
        </ul>
    @endif

@endsection
