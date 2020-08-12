
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <form method="post" action="{{action('PollController@update', $poll->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{$poll->translateOrDefault(App::getLocale())->name}}">
        </div>

        <div class="mb-3">
            <label for="infotext">@lang('Informationstext')</label>
            <input name="infotext" class="form-control" id="infotext" value="{{$poll->translateOrDefault(App::getLocale())->infotext}}">
        </div>

        @lang('Aktiverad för följande kommuner:') <br>
        @foreach($municipalities as $municipality)
            <label><input type="checkbox" {{$poll->municipalities->contains('id', $municipality->id)?"checked":""}} name="municipalities[]" value="{{$municipality->id}}">{{$municipality->name}}</label>
        @endforeach

        <br><br>

        @lang('mellan')
        <input type="date" name="active_from" class="form-control" value="{{$poll->active_from}}">
        @lang('och')
        <input type="date" name="active_to" class="form-control" value="{{$poll->active_to}}">

        <br>

        <button class="btn btn-primary btn-lg btn-primary" type="submit">@lang('Spara')</button>

    </form>

    <br><br>

    <h1>@lang('Enkätens frågor')</h1>

    <div class="list-group mb-4" id="pollquestionslist">
        @foreach($poll->poll_questions->sortBy('order') as $question)
            <a href="/pollquestion/{{$question->id}}/edit" class="list-group-item list-group-item-action" id="id-{{$question->id}}">
                <div class="row">
                    @if($question->type == 'pagebreak')
                        <hr style="width:95%">
                    @else
                        {{$question->translateOrDefault(App::getLocale())->text}} -
                        {{$question->compulsory?__("Obligatorisk"):__("Frivillig")}}
                        @if($question->type == 'freetext')
                            @lang('fritextfråga')
                        @elseif($question->max_alternatives == 1)
                            @lang('envalsfråga med :alternatives alternativ', ['alternatives' => count($question->alternatives_array)])
                        @else
                            @lang('flervalsfråga med :alternatives alternativ', ['alternatives' => count($question->alternatives_array)])
                        @endif
                        {{$question->display_criteria!=''?'(har visningskriterium)':''}}
                    @endif
                </div>
            </a>
        @endforeach
    </div>

    <br>

    <a href="/poll/{{$poll->id}}/exportresponses" class="btn btn-primary">@lang('Exportera enkätsvar')</a>

    <script type="text/javascript" language="javascript" src="{{asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
    <script type="text/javascript">
        $(function() {
            $("#pollquestionslist").sortable({
            update: function (e, u) {
                var token = "{{ csrf_token() }}";
                var data = $(this).sortable('serialize');
                $.ajax({
                    url: '/pollquestion/reorder',
                    data : {_token:token,data:data},
                    type: 'POST'
                });
            }
            });
        });
    </script>

@endsection
