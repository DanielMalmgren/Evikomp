@extends('layouts.app')

@section('content')

<script src="/trumbowyg/trumbowyg.min.js"></script>
<script type="text/javascript" src="/trumbowyg/langs/sv.min.js"></script>

<script type="text/javascript">
    $(function() {
        $('#limited_by_title').on('change', function() {
            var val = this.checked;
            $("#titles").toggle(this.checked);
        });

        $('#description').trumbowyg({
            btns: [
                ['formatting'],
                ['strong', 'em', 'del'],
                ['link'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList']
            ],
            lang: 'sv'
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
            <textarea rows=5 name="description" class="form-control" id="description">{{$lesson->translateOrDefault(App::getLocale())->description}}</textarea>
        </div>

        <div class="mb-3">
            <label for="video_id">@lang('Video-ID')</label>
            <input name="video_id" class="form-control" id="video_id" value="{{$lesson->video_id}}">
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

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>

@endsection
