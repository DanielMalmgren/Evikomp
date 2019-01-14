@extends('layouts.app')

@section('content')

<div class="col-md-5 mb-3">
    <H1>@lang('Inställningar')</H1>

    <form method="post" action="{{action('SettingsController@store')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="locale">@lang('Språk')</label>
            <select class="custom-select d-block w-100" id="locale" name="locale" required="">
                @foreach($locales as $locale)
                    @if($user->locale_id == $locale->id || (!$user->locale_id && $locale->default))
                        <option value="{{$locale->id}}" selected>{{$locale->name}}</option>
                    @else
                        <option value="{{$locale->id}}">{{$locale->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        @if(count($tracks) > 0)
            @foreach($tracks as $track)
                <div class="checkbox">
                    @if($user->workplace->tracks->contains('id', $track->id))
                        <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}" checked disabled>{{$track->translateOrDefault(App::getLocale())->name}}</label>
                    @elseif($user->tracks->contains('id', $track->id))
                        <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}" checked>{{$track->translateOrDefault(App::getLocale())->name}}</label>
                    @else
                        <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}">{{$track->translateOrDefault(App::getLocale())->name}}</label>
                    @endif
                </div>
            @endforeach
        @endif

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>
</div>

@endsection
