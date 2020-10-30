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
        
        $("#number_of_questions").change(function(e){
            e.preventDefault();
            console.log("Du ändrade till " + $("#number_of_questions").val());
            var number_of_questions = $("#number_of_questions").val();
            var token = "{{ csrf_token() }}";
            $.ajax({
                url: '/test/{{$lesson->id}}/number_of_questions',
                data : {_token:token,number_of_questions:number_of_questions},
                type: 'POST'
            });
        });
    });
</script>

    <H1>@lang('Redigera frågor för lektion') {{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    @if($questions->isEmpty())
        @lang('Denna lektion har inga frågor. Du kan kopiera samtliga frågor ifrån en annan lektion genom att välja nedan.')

        <form method="post" action="{{action('LessonController@replicateQuestions')}}" accept-charset="UTF-8">
            @csrf
            <input type="hidden" name="targetlesson" value="{{$lesson->id}}">
            <div class="mb-3">
                <select class="custom-select d-block w-100" name="sourcelesson" id="sourcelesson" required="" onchange="this.form.submit()">
                    <option disabled selected>@lang('Välj lektion att kopiera ifrån')</option>
                    @foreach($lessonsWithQuestions as $sourcelesson)
                        <option value="{{$sourcelesson->id}}">{{$sourcelesson->translateOrDefault(App::getLocale())->name}} ({{$sourcelesson->track->translateOrDefault(App::getLocale())->name}})</option>
                    @endforeach
                </select>
            </div>
        </form>

    @else
        <div class="mb-3">
            <label for="number_of_questions">@lang('Antal frågor som behöver besvaras')</label>
            <select class="custom-select d-block w-100" name="number_of_questions" id="number_of_questions" required="">
                @for ($i = 1; $i <= $lesson->questions->count(); $i++)
                    <option value="{{$i}}" {{$i==$lesson->number_of_questions?"selected":""}}>{{$i}} (@lang('i slumpvis ordning'))</option>
                @endfor
                <option value="0" {{0==$lesson->number_of_questions?"selected":""}}>@lang("Samtliga (i ordning enligt nedan)")</option>
            </select>
        </div>

        @lang('Frågor')
        <div id="questionlist">
            @foreach($questions as $question)
                <div class="card" id="id-{{$question->id}}" data-question_id="{{$question->id}}">
                    <div class="card-body">
                        @if(locale_is_default())
                            <a href="#" class="close remove_question" data-dismiss="alert" aria-label="close">&times;</a>
                        @endif
                        <a href="/test/question/{{$question->id}}/edit">
                            <h6 class="mb-3">{{$question->translateOrDefault(App::getLocale())->text}}</h6>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <br>

    @if(locale_is_default())
        <a href="/test/question/create?lesson_id={{$lesson->id}}" class="btn btn-primary">@lang('Lägg till fråga')</a>
    @endif

    <a href="/lessons/{{$lesson->id}}" class="btn btn-primary">@lang('Tillbaka till lektionen')</a>

@endsection
