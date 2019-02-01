@extends('layouts.app')

@section('content')

<script type="text/javascript">
    $(function() {
        var $titlesDiv = $('select[id="titles"]');
        $('#limited_by_title').on('change', function() {
            var val = this.checked;
            console.log(val);
            $("#titles").toggle(this.checked);
        });
    });
</script>

    <H1>@lang('Redigera lektion')</H1>

    <form method="post" action="{{action('LessonController@update', $lesson->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{$lesson->translateOrDefault(App::getLocale())->name}}">
        </div>

        <div class="mb-3">
            <label for="description">@lang('Beskrivning')</label>
            <textarea rows=5 name="description" class="form-control" id="description" value="{{$lesson->translateOrDefault(App::getLocale())->description}}"></textarea>
        </div>

        <div class="mb-3">
            <input type="hidden" name="active" value="0">
            <label><input type="checkbox" name="active" value="1" {{$lesson->active?"checked":""}}>@lang('Aktiv')</label>
        </div>

        <div class="mb-3">
            <input type="hidden" name="limited_by_title" value="0">
            <label><input type="checkbox" name="limited_by_title" id="limited_by_title" value="1" {{$lesson->limited_by_title?"checked":""}}>@lang('Begränsad enbart till vissa befattningar')</label>
        </div>

        <div id="titles" style="{{!$lesson->limited_by_title?"display: none;":""}}">
            @foreach($titles as $title)
                <label><input type="checkbox" {{$lesson->titles->contains('id', $title->id)?"checked":""}} name="titles[]" value="{{$title->id}}">{{$title->workplace_type->name}} - {{$title->name}}</label><br>
            @endforeach
        </div>

        @if(count($lesson->questions) > 0)
            @lang('Frågor')
            <ul class="list-group mb-3" id="questionlist">
                @foreach($lesson->questions as $question)
                    <li class="list-group-item d-flex justify-content-between lh-condensed">
                        <div>
                        <a href="/test/question/{{$question->id}}/edit">
                            <h6 class="my-0">{{$question->translateOrDefault(App::getLocale())->text}}</h6>
                        </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
        <a href="/test/question/create?lesson_id={{$lesson->id}}" class="btn btn-primary">@lang('Lägg till fråga')</a>

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>

@endsection
