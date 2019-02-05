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

        <p>
            Aktiv tid denna månad:<br>
            @foreach($active_times as $i => $active_time)
                {{$i}}: {{$active_time}}<br>
            @endforeach
            Totalt: {{$total_active_time}}
        </p>
    @endif

@endsection
