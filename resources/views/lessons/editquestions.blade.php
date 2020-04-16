@extends('layouts.app')

@section('title', __('Redigera frågor'))

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

    <H1>@lang('Redigera frågor för lektion') {{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    @if($questions->isNotEmpty())
        @lang('Frågor')
        <div id="questionlist">
            @foreach($questions as $question)
                <div class="card" id="id-{{$question->id}}" data-question_id="{{$question->id}}">
                    <div class="card-body">
                        <a href="#" class="close remove_question" data-dismiss="alert" aria-label="close">&times;</a>
                        <a href="/test/question/{{$question->id}}/edit">
                            <h6 class="mb-3">{{$question->translateOrDefault(App::getLocale())->text}}</h6>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <br>

    <a href="/test/question/create?lesson_id={{$lesson->id}}" class="btn btn-primary">@lang('Lägg till fråga')</a>

    <a href="/lessons/{{$lesson->id}}" class="btn btn-primary">@lang('Tillbaka till lektionen')</a>

@endsection
