@extends('layouts.app')

@section('content')

<script type="text/javascript" language="javascript" src="{{asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>

<script type="text/javascript">
    $(function() {
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

        $("#questionlist").sortable({
           update: function (e, u) {
               var token = "{{ csrf_token() }}";
               var data = $(this).sortable('serialize');
                $.ajax({
                    url: '/test/question/reorder',
                    data : {_token:token,data:data},
                    type: 'POST'
                });
           }
        });
    });
</script>

    <H1>@lang('Redigera frågor för lektion')</H1>

    @if(count($lesson->questions) > 0)
        @lang('Frågor')
        <ul class="list-group mb-3" id="questionlist">
            @foreach($questions as $question)
                <li class="list-group-item d-flex justify-content-between lh-condensed" id="id-{{$question->id}}" data-question_id="{{$question->id}}">
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

    <a href="/lessons/{{$lesson->id}}" class="btn btn-primary">@lang('Tillaka till lektionen')</a>

@endsection
