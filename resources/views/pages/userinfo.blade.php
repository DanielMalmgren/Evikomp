@extends('layouts.app')

@section('content')

<H1>Användarinfo</H1>

    @if($user)
        <p>
            Inloggad användare: {{$user->name}}
        </p>

        @if($user->workplace)
            <p>
                Arbetsplats: {{$user->workplace->name}}
            </p>

            @if($user->workplace->municipality)
                <p>
                    Kommun: {{$user->workplace->municipality->name}}
                </p>
            @endif
        @endif

        <p>
            E-post: {{$user->email}}
        </p>

        <p>
            Roller:<br>
            @foreach($user->getRoleNames() as $role)
                {{$role}}<br>
            @endforeach
        </p>
    @endif

@endsection
