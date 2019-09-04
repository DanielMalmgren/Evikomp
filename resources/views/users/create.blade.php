@extends('layouts.app')

@section('title', __('Skapa användare manuellt'))

@section('content')

<div class="col-md-5 mb-3">

    <H1>@lang('Skapa användare manuellt')</H1>

    <form method="post" name="settings" action="{{action('UsersController@store')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="personid">@lang('Personnummer')</label>
            <input required type="text" name="personid" class="form-control" id="personid" placeholder="yyyymmddxxxx"  value="{{old('personid')}}">
        </div>

        <div class="mb-3">
            <div class="row container">
                <div>
                    <label for="firstname">@lang('Förnamn')</label>
                    <input required type="text" name="firstname" class="form-control"  value="{{old('firstname')}}">
                </div>
                <div>
                    <label for="lastname">@lang('Efternamn')</label>
                    <input required type="text" name="lastname" class="form-control"  value="{{old('lastname')}}">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="email">@lang('E-postadress')</label>
            <input required type="email" name="email" class="form-control" id="email" placeholder="fornamn.efternamn@kommun.se"  value="{{old('email')}}">
        </div>

        <div class="mb-3">
            <label for="password">@lang('Lösenord')</label>
            <input type="text" class="form-control" disabled value="{{$password}}">
            <input type="hidden" name="password" class="form-control" id="password" value="{{$password}}">
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Skapa')</button>
    </form>
</div>

@endsection
