@extends('layouts.app')

@section('title', __('Enkäter'))

@section('content')

    <H1>@lang('Enkäter')</H1>

    @if(count($polls) > 0)
        <ul class="list-group mb-3 tracks" id="polls">
            @foreach($polls as $poll)
                <li class="list-group-item d-flex justify-content-between lh-condensed nopadding" id="id-{{$poll->id}}">
                    <a href="/poll/{{$poll->id}}/edit">
                        <h6 class="my-0">{{$poll->translateOrDefault(App::getLocale())->name}}
                        </h6>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif

@endsection
