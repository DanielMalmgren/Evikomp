@extends('layouts.app')

@section('title', __('Inställningar'))

@section('content')

<script type="text/javascript">
    $(function() {
        var $titleSelect = $('select[id="title"]');
        var $titleSelectId = $titleSelect.val();
        var $titles = $('option', $titleSelect);
        $('#workplace').on('change', function() {
            $titles.detach();
            var val = $(this).find('option:selected').attr("data-workplace-type");
            $titles.each(function() {
                if($(this).is('[data-workplace_type="' + val + '"') || $(this).is('[data-workplace_type="' + -1 + '"')) {
                    $(this).appendTo($titleSelect);
                }
            });
            document.getElementById('title').disabled = false;
            $titleSelect.val($titleSelectId);
        });
    });

    $(function() {
        var $workplaceSelect = $('select[id="workplace"]');
        var $workplaceSelectId = $workplaceSelect.val();
        var $workplaces = $('option', $workplaceSelect);
        $('#municipality').on('change', function() {
            $workplaces.detach();
            var val = $(this).val();
            $workplaces.each(function() {
                if($(this).is('[data-municipality="' + val + '"')) {
                    $(this).appendTo($workplaceSelect);
                }
            });
            document.getElementById('workplace').disabled = false;
            $("#workplace").change();
            $workplaceSelect.val($workplaceSelectId);
        });
        $("#municipality").change();
    });

    $(function() {
        $('#title').on('change', function() {
            document.settings.submit.disabled = false;
        });
    });
</script>

<div class="col-md-5 mb-3">

    @if(empty(Auth::user()["workplace_id"]))
        <H1>@lang('Välkommen')</H1>
    @else
        <H1>@lang('Inställningar')</H1>

        <form method="post" action="{{action('SettingsController@storeLanguage')}}" accept-charset="UTF-8">
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
    @endif

    <form method="post" name="settings" action="{{action('SettingsController@store')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="municipality">@lang('Kommun')</label>
            <select class="custom-select d-block w-100" id="municipality" required="">
                @if(!$user->workplace)
                    <option disabled selected value>@lang('Välj...')</option>
                @endif
                @foreach($municipalities as $municipality)
                    @if($user->workplace && $user->workplace->municipality->id == $municipality->id)
                        <option selected value="{{$municipality->id}}">{{$municipality->name}}</option>
                    @else
                        <option value="{{$municipality->id}}">{{$municipality->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="workplace">@lang('Arbetsplats')</label>
            @if($user->workplace)
                <select class="custom-select d-block w-100" id="workplace" name="workplace" required="">
                    @foreach($workplaces as $workplace)
                        @if($user->workplace->id == $workplace->id)
                            <option selected data-municipality="{{$workplace->municipality_id}}" data-workplace-type="{{$workplace->workplace_type_id}}" value="{{$workplace->id}}">{{$workplace->name}}</option>
                        @else
                            <option data-municipality="{{$workplace->municipality_id}}" data-workplace-type="{{$workplace->workplace_type_id}}" value="{{$workplace->id}}">{{$workplace->name}}</option>
                        @endif
                    @endforeach
                </select>
            @else
                <select class="custom-select d-block w-100" id="workplace" name="workplace" required="" disabled>
                    <option>@lang('Välj kommun först')</option>
                    @foreach($workplaces as $workplace)
                        <option data-municipality="{{$workplace->municipality_id}}" data-workplace-type="{{$workplace->workplace_type_id}}" value="{{$workplace->id}}">{{$workplace->name}}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="mb-3">
            <label for="title">@lang('Befattning')</label>
            @if($user->title)
                <select class="custom-select d-block w-100" id="title" name="title" required="">
                    <option disabled data-workplace_type="-1">@lang('Välj din befattning')</option>
                    @foreach($titles as $title)
                        @if($user->title->id == $title->id)
                            <option selected data-workplace_type="{{$title->workplace_type->id}}" value="{{$title->id}}">{{$title->name}}</option>
                        @else
                            <option data-workplace_type="{{$title->workplace_type->id}}" value="{{$title->id}}">{{$title->name}}</option>
                        @endif
                    @endforeach
                </select>
            @else
                <select class="custom-select d-block w-100" id="title" name="title" required="" disabled>
                    @if($user->workplace)
                        <option>@lang('Välj arbetsplats först')</option>
                        <option disabled selected data-workplace_type="-1">@lang('Välj din befattning')</option>
                    @else
                        <option selected>@lang('Välj arbetsplats först')</option>
                        <option disabled data-workplace_type="-1">@lang('Välj din befattning')</option>
                    @endif
                    @foreach($titles as $title)
                        <option data-workplace_type="{{$title->workplace_type->id}}" value="{{$title->id}}">{{$title->name}}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="mb-3">
            <label for="email">@lang('E-postadress')</label>
            @if($user->email)
                <input type="email" name="email" class="form-control" id="email" value="{{$user->email}}">
            @else
                <input type="email" name="email" class="form-control" id="email" placeholder="fornamn.efternamn@kommun.se">
            @endif
            <div class="invalid-feedback">
                @lang('Vänligen ange en giltig e-postadress')
            </div>
        </div>

        <label>@lang('Valda spår')</label>
        @if(count($tracks) > 0 && $user->workplace)
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

        <br>

        <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit" {{$user->title?"":"disabled"}}>@lang('Spara')</button>
    </form>
</div>

@endsection
