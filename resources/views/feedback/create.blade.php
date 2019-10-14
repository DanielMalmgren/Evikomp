@extends('layouts.app')

@section('title', __('Feedback'))

@section('content')

<script type="text/javascript">
    function toggleDisableAnonymous() {
        var a=document.getElementById('anonymous');
        var c=document.getElementById('contacted');
        if(c.checked) {
            a.disabled = true;
        } else {
            a.disabled = false;
        }
    }

    function toggleDisableContacted() {
        var a=document.getElementById('anonymous');
        var c=document.getElementById('contacted');
        if(a.checked) {
            c.disabled = true;
        } else {
            c.disabled = false;
        }
    }
</script>

    <H1>@lang('Skicka feedback')</H1>

    <form method="post" action="{{action('FeedbackController@post')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-3">
            <label for="lesson">@lang('Min feedback gäller lektion')</label>
            <select class="custom-select d-block w-100" name="lesson" id="lesson">
                <option selected value="null">@lang('Ingen specifik lektion')</option>
                @foreach($lessons as $lesson)
                    @if(strpos(url()->previous(), '/lessons/') && $lesson->id == substr(url()->previous(), strrpos(url()->previous(), '/')+1))
                        <option selected value="{{$lesson->name}}">{{$lesson->name}}</option>
                    @else
                        <option value="{{$lesson->name}}">{{$lesson->name}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="content">@lang('Meddelande')</label>
            <textarea rows=5 name="content" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label><input type="checkbox" id="anonymous" name="anonymous" onclick="toggleDisableContacted()">@lang('Jag vill vara anonym')</label>
        </div>

        <div class="mb-3">
            <label><input type="checkbox" id="contacted" name="contacted" onclick="toggleDisableAnonymous()">@lang('Jag vill bli kontaktad')</label>
        </div>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Skicka')</button>
    </form>

@endsection
