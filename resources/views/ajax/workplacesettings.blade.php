
<H2>Obligatoriska spår</H2>


<form method="post" name="question" action="{{action('WorkplaceSettingsController@store')}}" accept-charset="UTF-8">
    @csrf

    <input type="hidden" name="workplace_id" value="{{$workplace->id}}">

    @if(count($tracks) > 0)
        @foreach($tracks as $track)
            <div class="checkbox">
                @if($workplace->tracks->contains('id', $track->id))
                    <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}" checked>{{$track->translateOrDefault(App::getLocale())->name}}</label>
                @else
                    <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}">{{$track->translateOrDefault(App::getLocale())->name}}</label>
                @endif
            </div>
        @endforeach
    @endif

    <br><br>

    <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
</form>
