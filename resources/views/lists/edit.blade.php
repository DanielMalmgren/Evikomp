@extends('layouts.app')

@section('title', __('Redigera lista'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

<div class="col-md-8">

    <H1>@lang('Redigera lista')</H1>

    <form method="post" action="{{action('ListController@update', $list->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <div class="mb-5">
            <label for="name">@lang('Namn')</label>
            <input name="name" required class="form-control" value="{{$list->name}}">
        </div>

        <H2>@lang('Moduler i denna lista')</H2>
        <div id="lessonwrapper">
            @foreach($list->lessons as $pt_lesson)
                <div class="mb-3" id="lesson[{{$pt_lesson->id}}]" data-id="{{$pt_lesson->id}}">
                    <div class="row container">
                        <div class="w-45">
                            <select class="custom-select d-block track" data-id="{{$pt_lesson->id}}" id="track{{$pt_lesson->id}}">
                                @foreach($tracks as $track)
                                    <option {{$track->id==$pt_lesson->track->id?"selected":""}} value="{{$track->id}}">{{$track->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-45">
                            <select class="custom-select d-block" id="lessons{{$pt_lesson->id}}" name="lessons[{{$pt_lesson->id}}]">
                                <option disabled value="-1">@lang('Välj en modul')</option>
                                @foreach($lessons as $lesson)
                                    <option {{$lesson->id==$pt_lesson->id?"selected":""}} value="{{$lesson->id}}" data-track="{{$lesson->track_id}}">{{$lesson->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{--<div class="w-10">
                            <i class="fas fa-trash"></i>
                        </div>--}}
                    </div>
                </div>
            @endforeach
        </div>
        <div id="addlessonrow">@lang('Lägg till modul')</div>
        <br>

        <H2>@lang('Delning av listan')</H2>

        Listan är delad med {{$list->users->count()}} användare och {{$list->workplaces->count()}} arbetsplatser.<br>

        Användare:<br>

        <div id="users_wrap">
            @foreach($list->users as $user)
                <a class="list-group-item list-group-item-action">
                    <div class="row">
                        <input type="hidden" class="userid" name="users[{{$user->id}}]">
                        <div class="col-lg-4 col-md-9 col-sm-7">
                            {{$user->name}}
                        </div>
                        <div class="col-lg-1 col-md-3 col-sm-5">
                            <i class="fas fa-trash remove_field"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div id="add_user_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till användare')</div>

        <br><br>

        Arbetsplatser:<br>
        <select id="workplaces" name="workplaces[]" multiple="multiple">
            @foreach($workplaces as $workplace)
                <option value="{{$workplace->id}}" data-section="{{$workplace->municipality->name}}" {{$list->workplaces->contains('id', $workplace->id)?"selected":""}}>{{$workplace->name}}</option>
            @endforeach
        </select>

        <br><br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>
</div>

<script type="text/javascript">

    function deletelesson(lesson_id) {
        if(confirm('@lang("Vill du verkligen modulen från listan?")')) {
            console.log("TODO!");
        }
    }

    $(function() {

        $('#addlessonrow').click(function () {
            var wrapper = $("#lessonwrapper");
            new_id = getfreeid();
            $(wrapper).append(`
                <div class="mb-3" id="lesson[`+new_id+`]" data-id="`+new_id+`">
                    <div class="row container">
                        <div class="w-50">
                            <select class="custom-select d-block w-200 track" id="track`+new_id+`" data-id="`+new_id+`">
                                <option selected disabled>@lang('Välj ett spår')</option>
                                @foreach($tracks as $track)
                                    <option value="{{$track->id}}">{{$track->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-50">
                            <select class="custom-select d-block w-200" id="lessons`+new_id+`" name="lessons[`+new_id+`]">
                                <option selected disabled value="-1">@lang('Välj en modul')</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{$lesson->id}}" data-track="{{$lesson->track_id}}">{{$lesson->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            `);
            $lessonSelect[new_id] = $('select[id="lessons'+new_id+'"]');
            $lessons[new_id] = $('option', $lessonSelect[new_id]);
            addTrackChangeListener(new_id);
            $("#track"+new_id).change();
        });

        var $lessonSelect = [];
        var $lessons = [];

        @foreach($list->lessons as $pt_lesson)
            $lessonSelect[{{$pt_lesson->id}}] = $('select[id="lessons{{$pt_lesson->id}}"]');
            $lessons[{{$pt_lesson->id}}] = $('option', $lessonSelect[{{$pt_lesson->id}}]);
        @endforeach
        function addTrackChangeListener(id) {
            $('.track').on('change', function() {
                var id=$(this).data('id');
                $lessons[id].detach();
                var val = $(this).val();
                $lessons[id].each(function() {
                    if($(this).is('[data-track="' + val + '"]') || $(this).val() == "-1") {
                        $(this).appendTo($lessonSelect[id]);
                    }
                });
                //$("#lessons"+id).val("-1").change();
            });
        }
        @foreach($list->lessons as $pt_lesson)
            addTrackChangeListener({{$pt_lesson->id}});
            //$("#track{{$pt_lesson->id}}").change();
        @endforeach

    });

    function getfreeid() {
        for(;;) {
            testnumber = Math.floor((Math.random() * 1000) + 1);
            hit = 0;
            $('#lessonwrapper').children().each(function() {
                if($(this).data("id") == testnumber) {
                    hit=1;
                    return false;
                }
            });
            if(hit==0) {
                return testnumber;
            }
        }
    }


    function addselect2() {
        $('.new_users').select2({
            width: '100%',
            ajax: {
                url: '/select2users',
                dataType: 'json'
            },
            language: "sv",
            minimumInputLength: 3,
            theme: "bootstrap4"
        });
    }

    $(function() {
        var wrapper = $("#users_wrap");
        var add_button = $("#add_user_button");

        $(add_button).click(function(e){
            e.preventDefault();
            $(wrapper).append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-4 col-md-9 col-sm-7"><select class="new_users" name="new_users[]"></select></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
            addselect2();
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div').parent('div').parent('a');
            var userid = $(this).parent('div').parent('div').find('.userid');
            var oldname = adminid.attr('name');
            parentdiv.hide();
            userid.attr('name', 'remove_' + oldname);
        })

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
