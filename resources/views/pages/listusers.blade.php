@extends('layouts.app')

@section('content')

    <H1>Användare</H1>

    @if(count($users) > 0)
    <ul class="list-group mb-3">
        @foreach($users as $user)
            <li class="list-group-item d-flex justify-content-between lh-condensed">
                <div>
                <a href="/userinfo/{{$user->id}}">
                    <h6 class="my-0">{{$user->name}}</h6>
                </a>
                @if($user->workplace)
                    <small class="text-muted">{{$user->workplace->name}}</small>
                @endif
                </div>
                <span class="text-muted">{{$user->email}}</span>
            </li>
        @endforeach
    </ul>

    <a href="/exportusers" class="btn btn-primary">@lang('Hämta som Excel-fil')</a>

    @endif

@endsection
