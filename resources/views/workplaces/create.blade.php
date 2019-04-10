@extends('layouts.app')

@section('content')

    <H1>Skapa ny arbetsplats</H1>

    <div id="settings">

        <form method="post" name="question" action="{{action('WorkplaceController@store')}}" accept-charset="UTF-8">
            @csrf

            <div class="mb-3">
                <label for="name">@lang('Namn')</label>
                <input name="name" class="form-control" id="name">
            </div>

            <div class="mb-3">
                <label for="municipality">@lang('Kommun')</label>
                <select class="custom-select d-block w-100" name="municipality" id="municipality" required="">
                    @foreach($municipalities as $municipality)
                        <option value="{{$municipality->id}}">{{$municipality->name}}</option>
                    @endforeach
                </select>
            </div>

            <label for="workplace_type">@lang('Typ av arbetsplats')</label>
            <select class="custom-select d-block w-100" id="workplace_type" name="workplace_type" required="">
                @foreach($workplace_types as $workplace_type)
                    <option value="{{$workplace_type->id}}">{{$workplace_type->name}}</option>
                @endforeach
            </select>

            <br>

            <label>@lang('Obligatoriska spår')</label>
            @if(count($tracks) > 0)
                <div class="card">
                    <div class="card-body">
                        @foreach($tracks as $track)
                            <div class="checkbox">
                                <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}">{{$track->translateOrDefault(App::getLocale())->name}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <br><br>

            <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
        </form>

    </div>

@endsection
