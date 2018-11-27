@extends('layouts.app')

@section('content')

<script>
$(function() {
    var $workplaceSelect = $('select[id="workplace"]');
    var $workplaces = $('option', $workplaceSelect);
    // and then listen to change event and show/hide them
    $('#municipality').on('change', function() {
        // first remove all elements from dom
        $workplaces.detach();
        var val = $(this).val();
        $workplaces.each(function() {
            if($(this).is('[data-municipality="' + val + '"')) {
                $(this).appendTo($workplaceSelect);
            }
        });
        document.getElementById('workplace').disabled = false;
    });
});
</script>

<div class="col-md-5 mb-3">
    <H1>Välkommen!</H1>

    <p>Det verkar vara första gången du loggar in här. Du behöver därför ange lite uppgifter nedan:</p>

    <form method="post" action="{{action('PagesController@storeFirstLogin')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="locale">Språk</label>
            <select class="custom-select d-block w-100" name="locale" id="locale" required="">
                @foreach($locales as $locale)
                    @if($locale->default)
                        <option value="{{$locale->id}}" selected>{{$locale->name}}</option>
                    @else
                        <option value="{{$locale->id}}">{{$locale->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="municipality">Kommun</label>
            <select class="custom-select d-block w-100" id="municipality" required="">
                <option disabled selected value>Välj...</option>
                @foreach($municipalities as $municipality)
                    <option value="{{$municipality->id}}">{{$municipality->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="workplace">Arbetsplats</label>
            <select class="custom-select d-block w-100" id="workplace" name="workplace" required="" disabled>
                <option>Välj kommun först</option>
                @foreach($workplaces as $workplace)
                    <option data-municipality="{{$workplace->municipality_id}}" value="{{$workplace->id}}">{{$workplace->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="email">E-postadress</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="fornamn.efternamn@kommun.se">
            <div class="invalid-feedback">
                Please enter a valid email address.
            </div>
        </div>

        <button class="btn btn-primary btn-lg btn-block" type="submit">Gå vidare</button>
    </form>
</div>

@endsection
