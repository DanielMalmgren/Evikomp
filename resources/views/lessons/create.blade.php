@extends('layouts.app')

@section('content')

<script type="text/javascript">
    $(function() {
        var $titlesDiv = $('select[id="titles"]');
        $('#limited_by_title').on('change', function() {
            var val = this.checked;
            $("#titles").toggle(this.checked);
        });

        var wrapper = $("#questionlist");
        $(wrapper).on("click",".remove_question", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div').parent();
            var questionId = parentdiv.data('question_id');
            console.log(questionId);
            var token = "{{ csrf_token() }}";
            $.ajax({
                url: '/test/question/'+questionId,
                data : {_token:token},
                type: 'DELETE'
            });
            parentdiv.css("cssText", "display: none !important;");
        });
    });
</script>

    <H1>@lang('Lägg till lektion')</H1>

    <form method="post" action="{{action('LessonController@store')}}" accept-charset="UTF-8">
        @csrf

        <input type="hidden" name="track_id" value="{{$track->id}}">

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name">
        </div>

        <div class="mb-3">
            <label for="description">@lang('Beskrivning')</label>
            <textarea rows=5 name="description" class="form-control" id="description"></textarea>
        </div>

        <div class="mb-3">
            <input type="hidden" name="active" value="0">
            <label><input type="checkbox" name="active" value="1" checked>@lang('Aktiv')</label>
        </div>

        <div class="mb-3">
            <input type="hidden" name="limited_by_title" value="0">
            <label><input type="checkbox" name="limited_by_title" id="limited_by_title" value="1">@lang('Begränsad enbart till vissa befattningar')</label>
        </div>

        <div id="titles" style="display: none;">
            @foreach($titles as $title)
                <label><input type="checkbox" name="titles[]" value="{{$title->id}}">{{$title->workplace_type->name}} - {{$title->name}}</label><br>
            @endforeach
        </div>

        {{--
        @if(count($lesson->questions) > 0)
            @lang('Frågor')
            <ul class="list-group mb-3" id="questionlist">
                @foreach($lesson->questions as $question)
                    <li class="list-group-item d-flex justify-content-between lh-condensed" data-question_id="{{$question->id}}">
                        <div>
                        <a href="/test/question/{{$question->id}}/edit">
                            <h6 class="my-0">{{$question->translateOrDefault(App::getLocale())->text}}</h6>
                        </a>
                        <button class="btn btn-default btn-danger remove_question" type="button">X</button>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
        <a href="/test/question/create?lesson_id={{$lesson->id}}" class="btn btn-primary">@lang('Lägg till fråga')</a>
        --}}

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>

@endsection
