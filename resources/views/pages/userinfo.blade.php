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

        <form method="get" name="settings" action="{{action('ActiveTimeController@export')}}" accept-charset="UTF-8">
            @csrf

            <select class="custom-select d-block w-100" id="year" name="year" required="">
                <option value="2018">2018</option>
                <option value="2019">2019</option>
            </select>

            <select class="custom-select d-block w-100" id="month" name="month" required="">
                <option value="1">@lang('Januari')</option>
                <option value="2">@lang('Februari')</option>
                <option value="3">@lang('Mars')</option>
                <option value="4">@lang('April')</option>
                <option value="5">@lang('Maj')</option>
                <option value="6">@lang('Juni')</option>
                <option value="7">@lang('Juli')</option>
                <option value="8">@lang('Augusti')</option>
                <option value="9">@lang('September')</option>
                <option value="10">@lang('Oktober')</option>
                <option value="11">@lang('November')</option>
                <option value="12">@lang('December')</option>
            </select>

            <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Hämta närvarorapport')</button>
        </form>
    @endif

@endsection
