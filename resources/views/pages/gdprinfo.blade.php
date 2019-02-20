@extends('layouts.app')

@section('title', __('GDPR'))

@section('content')

<div class="col-md-5 mb-3">

    <form method="post" action="{{action('FirstLoginController@storeLanguage')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="locale">@lang('Språk')</label>
            <select class="custom-select d-block w-100" name="locale" id="locale" required="" onchange="this.form.submit()">
                @foreach($locales as $locale)
                    @if($user->locale_id == $locale->id || (!$user->locale_id && $locale->default)) {{-- Om antingen denna locale matchar med användarens eller om användaren inte har någon och detta är default locale --}}
                        <option value="{{$locale->id}}" selected>{{$locale->name}}</option>
                    @else
                        <option value="{{$locale->id}}">{{$locale->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </form>

    @lang('messages.gdprinfo')

    <br><br>

    <form method="post" action="{{action('FirstLoginController@storeGdprAccept')}}" accept-charset="UTF-8">
        @csrf

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Fortsätt')</button>
    </form>

</div>

@endsection
