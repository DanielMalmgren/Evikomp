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
                if($(this).is('[data-workplace_type="' + val + '"]') || !val && $(this).is('[data-workplace_type="' + -1 + '"]')) {
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
                if($(this).is('[data-municipality="' + val + '"]') || !val && $(this).is('[data-municipality="-1"]')) {
                    $(this).appendTo($workplaceSelect);
                }
            });
            document.getElementById('workplace').disabled = false;
            $("#workplace").change();
            $workplaceSelect.val($workplaceSelectId);
        });
        $("#municipality").change();
    });
</script>

<div class="col-md-5 mb-3">

    @if(empty(Auth::user()["workplace_id"]))
        <H1>@lang('Välkommen!')</H1>
        <div class="card">
            <div class="card-body">
                @lang('Du behöver göra vissa inställningar för att kunna börja använda plattformen. Notera att samtliga val nedan är obligatoriska!')
            </div>
        </div>
        <br>
    @else
        <H1>@lang('Inställningar')</H1>
    @endif

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

    <form method="post" name="settings" action="{{action('SettingsController@store', $user->id)}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <div class="row container">
                <div>
                    <label for="firstname">@lang('Förnamn')</label>
                    @if (str_word_count_utf8($user->saml_firstname) > 1)
                        <select class="custom-select d-block w-200" name="firstname">
                            @foreach(str_word_count_utf8($user->saml_firstname, 1) as $firstname)
                                @if($user->firstname == $firstname)
                                    <option selected value="{{$firstname}}">{{$firstname}}</option>
                                @else
                                    <option value="{{$firstname}}">{{$firstname}}</option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        <input type="text" name="firstname" class="form-control" disabled value="{{$user->firstname}}">
                    @endif
                </div>
                <div>
                    <label for="lastname">@lang('Efternamn')</label>
                    <input type="text" name="lastname" class="form-control" disabled value="{{$user->lastname}}">
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="municipality">@lang('Kommun')</label>
            <select class="custom-select d-block w-100" id="municipality" name="municipality" required="">
                @if(!$user->workplace && !old('municipality'))
                    <option disabled selected value>@lang('Välj...')</option>
                @endif
                @foreach($municipalities as $municipality)
                    @if($user->workplace && $user->workplace->municipality->id == $municipality->id || !$user->workplace && old('municipality') == $municipality->id)
                        <option selected value="{{$municipality->id}}">{{$municipality->name}}</option>
                    @else
                        <option value="{{$municipality->id}}">{{$municipality->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="workplace">@lang('Arbetsplats')</label>
            @if($user->workplace || old('workplace'))
                <select class="custom-select d-block w-100" id="workplace" name="workplace" required="">
                    @foreach($workplaces as $workplace)
                        @if($user->workplace && $user->workplace->id == $workplace->id || !$user->workplace && old('workplace') == $workplace->id)
                            <option selected data-municipality="{{$workplace->municipality_id}}" data-workplace-type="{{$workplace->workplace_type_id}}" value="{{$workplace->id}}">{{$workplace->name}}</option>
                        @else
                            <option data-municipality="{{$workplace->municipality_id}}" data-workplace-type="{{$workplace->workplace_type_id}}" value="{{$workplace->id}}">{{$workplace->name}}</option>
                        @endif
                    @endforeach
                </select>
            @else
                <select class="custom-select d-block w-100" id="workplace" name="workplace" required="" disabled>
                    <option disabled data-municipality="-1">@lang('Välj kommun först')</option>
                    @foreach($workplaces as $workplace)
                        <option data-municipality="{{$workplace->municipality_id}}" data-workplace-type="{{$workplace->workplace_type_id}}" value="{{$workplace->id}}">{{$workplace->name}}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="mb-3">
            <label for="title">@lang('Befattning')</label>
            @if($user->title || old('title'))
                <select class="custom-select d-block w-100" id="title" name="title" required="">
                    <option disabled data-workplace_type="-1">@lang('Välj din befattning')</option>
                    @foreach($titles as $title)
                        @if($user->title && $user->title->id == $title->id || !$user->title && old('title') == $title->id)
                            <option selected data-workplace_type="{{$title->workplace_type->id}}" value="{{$title->id}}">{{$title->name}}</option>
                        @else
                            <option data-workplace_type="{{$title->workplace_type->id}}" value="{{$title->id}}">{{$title->name}}</option>
                        @endif
                    @endforeach
                </select>
            @else
                <select class="custom-select d-block w-100" id="title" name="title" required="" disabled>
                    @if($user->workplace)
                        <option disabled>@lang('Välj arbetsplats först')</option>
                        <option disabled selected data-workplace_type="-1">@lang('Välj din befattning')</option>
                    @else
                        <option selected disabled data-workplace_type="-1">@lang('Välj arbetsplats först')</option>
                        <option disabled>@lang('Välj din befattning')</option>
                    @endif
                    @foreach($titles as $title)
                        <option data-workplace_type="{{$title->workplace_type->id}}" value="{{$title->id}}">{{$title->name}}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="mb-3">
            <label for="email">@lang('E-postadress')</label>
            <input type="email" name="email" class="form-control" id="email" value="{{old('email', $user->email)}}" placeholder="fornamn.efternamn@kommun.se">
        </div>

        <div class="mb-3">
            <label for="mobile">@lang('Mobilnummer')</label>
            <input type="tel" name="mobile" class="form-control" id="mobile" value="{{old('mobile', $user->mobile)}}">
        </div>

        <div class="mb-3">
            <label for="terms_of_employment">@lang('Anställningsvillkor')</label>
            <select class="custom-select d-block w-100" name="terms_of_employment" required="">
                @if(!$user->terms_of_employment && !old('terms_of_employment'))
                    <option disabled selected value>@lang('Välj...')</option>
                @endif
                <option value="1" {{$user->terms_of_employment==1||old('terms_of_employment')==1?"selected":""}}>@lang('Tillsvidareanställning')</option>
                <option value="2" {{$user->terms_of_employment==2||old('terms_of_employment')==2?"selected":""}}>@lang('Tidsbegränsad anställning')</option>
                <option value="3" {{$user->terms_of_employment==3||old('terms_of_employment')==3?"selected":""}}>@lang('Vet ej')</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="full_or_part_time">@lang('Anställningens omfattning')</label>
            <select class="custom-select d-block w-100" name="full_or_part_time" required="">
                @if(!$user->full_or_part_time && !old('full_or_part_time'))
                    <option disabled selected value>@lang('Välj...')</option>
                @endif
                <option value="1" {{$user->full_or_part_time==1||old('full_or_part_time')==1?"selected":""}}>@lang('Deltid')</option>
                <option value="2" {{$user->full_or_part_time==2||old('full_or_part_time')==2?"selected":""}}>@lang('Heltid')</option>
                <option value="3" {{$user->full_or_part_time==3||old('full_or_part_time')==3?"selected":""}}>@lang('Vet ej')</option>
            </select>
        </div>

        @if($user->workplace) {{--Settings that are not essential and that is not forced on first login settings--}}

            <div class="mb-3">
                <input type="hidden" name="use_subtitles" value="0">
                <label><input type="checkbox" name="use_subtitles" value="1" {{$user->use_subtitles?"checked":""}}>@lang('Visa undertexter i filmer')</label>
            </div>

            @if(count($tracks) > 0)
                <label>@lang('Valda spår')</label><br>
                <div class="card">
                    <div class="card-body">
                        <small>@lang('De spår som är utgråade är förvalda av din arbetsplats och går inte att välja bort')</small>
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
                    </div>
                </div>
            @endif
        @else
            <input type="hidden" name="use_subtitles" value="1">
        @endif

        <br>

        <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Spara')</button>
    </form>
</div>

@endsection
