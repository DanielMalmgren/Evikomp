
@extends('layouts.app')

@section('title', __('Enkät'))

@section('content')

    <script src="/trumbowyg/trumbowyg.min.js"></script>
    <script type="text/javascript" src="/trumbowyg/langs/sv.min.js"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <form method="post" action="{{action('PollController@update', $poll->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @method('put')
        @csrf

        <h1>@lang('Redigera enkät')</h1>
        @lang('Adress till denna enkät:') <a href="{{env('APP_URL')}}/poll/{{$poll->id}}">{{env('APP_URL')}}/poll/{{$poll->id}}</a><br><br>

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{$poll->translateOrDefault(App::getLocale())->name}}">
        </div>

        <div class="mb-3">
            <label for="infotext">@lang('Informationstext före')</label>
            <textarea rows="4" name="infotext" class="form-control twe">{!!$poll->translateOrDefault(App::getLocale())->infotext!!}</textarea>
        </div>

        <div class="mb-3">
            <label for="infotext2">@lang('Informationstext efter')</label>
            <textarea rows="4" name="infotext2" class="form-control twe">{!!$poll->translateOrDefault(App::getLocale())->infotext2!!}</textarea>
        </div>


        <div class="card">
            <div class="card-header">
                @lang('Målgrupp') <br>
            </div>
            <div class="card-body">
                <select id="workplaces" name="workplaces[]" multiple="multiple">
                @foreach($workplaces as $workplace)
                    <option value="{{$workplace->id}}" data-section="{{$workplace->municipality->name}}" {{$poll->workplaces->contains('id', $workplace->id)?"selected":""}}>{{$workplace->name}}</option>
                @endforeach
                </select>

                <div class="mb-3">
                    <select class="custom-select d-block w-100" name="scope_terms_of_employment" required="">
                        <option value="0">@lang('Samtliga')</option>
                        <option value="1" {{$poll->scope_terms_of_employment==1?"selected":""}}>@lang('Tillsvidareanställning')</option>
                        <option value="2" {{$poll->scope_terms_of_employment==2?"selected":""}}>@lang('Tidsbegränsad anställning')</option>
                        <option value="3" {{$poll->scope_terms_of_employment==3?"selected":""}}>@lang('Vet ej')</option>
                    </select>
                </div>

                <div class="mb-3">
                    <select class="custom-select d-block w-100" name="scope_full_or_part_time" required="">
                        <option value="0">@lang('Samtliga')</option>
                        <option value="1" {{$poll->scope_full_or_part_time==1?"selected":""}}>@lang('Deltid')</option>
                        <option value="2" {{$poll->scope_full_or_part_time==2?"selected":""}}>@lang('Heltid')</option>
                        <option value="3" {{$poll->scope_full_or_part_time==3?"selected":""}}>@lang('Vet ej')</option>
                    </select>
                </div>

                <br>

                @lang('mellan')
                <input type="date" name="active_from" class="form-control" value="{{$poll->active_from}}">
                @lang('och')
                <input type="date" name="active_to" class="form-control" value="{{$poll->active_to}}">
            </div>
        </div>

        <br>

        <div class="card">
            <div class="card-header">
                @lang('Kopplad till modul') <br>
            </div>
            <div class="card-body">
                <div id="lessons_wrap">
                    @if($poll->lessons->isNotEmpty())
                        @foreach($poll->lessons as $lesson)
                            <a class="list-group-item list-group-item-action">
                                <div class="row">
                                    <input type="hidden" class="adminid" name="lesson[{{$lesson->id}}]">
                                    <div class="col-lg-4 col-md-9 col-sm-7">
                                        {{$lesson->translateOrDefault(App::getLocale())->name}}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        @lang('Inte kopplad till någon modul. För att göra detta, se inställningarna för den modul du vill koppla!')
                    @endif
                </div>
            </div>
        </div>

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

    <a href="/pollquestion/create/{{$poll->id}}" class="btn btn-primary">@lang('Lägg till fråga')</a>

    <br><br>

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

        $('.twe').trumbowyg({
            btns: [
                ['formatting'],
                ['strong', 'em', 'del'],
                ['link'],
                ['justifyLeft', 'justifyCenter'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['fullscreen']
            ],
            lang: 'sv',
            removeformatPasted: true,
            minimalLinks: true
        });
    </script>


    <link href="/tree-multiselect/jquery.tree-multiselect.min.css" rel="stylesheet">
    <script src="/tree-multiselect/jquery.tree-multiselect.min.js"></script>
    <script type="text/javascript">
    	$("select#workplaces").treeMultiselect({
            startCollapsed: true,
            hideSidePanel: true
        });
    </script>

@endsection
