@extends('layouts.app')

@section('title', __('Redigera lärtillfälle'))

@section('content')

<script type="text/javascript">
    $(function() {
        $('.time').focusout(function(){
            var selectedValue = $(this).val();
            if(selectedValue.length == 4) {
                if(isNaN(selectedValue)) {
                    $(this).val('0'.concat(selectedValue));
                } else {
                    $(this).val(selectedValue.substring(0,2).concat(':', selectedValue.substring(2,4)));
                }
                selectedValue = $(this).val();
            }
            if(selectedValue != '' && selectedValue.substring(2,3) != ':') {
                alert('Tidpunkterna måste vara i formatet hh:mm!');
                $(this).val('');
            }
            if(selectedValue.substring(0,2) == '00') {
                if(!confirm('Den tid du försöker ange är mitt i natten.\nÄr du säker på att det är detta du vill?')) {
                    $(this).val('');
                }
            }
        });

        $('#training_coordinator').change(function () {
            var id = $(this).val();

            $('#teacher').find('option').remove();
            $.ajax({
                url:'/workplace/'+id+'/getusers',
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.users != null) {
                        len = response.users.length;
                    }
                    if (len>0) {
                        var option = "<option selected disabled value='-1'>@lang('Välj lärare')</option>"; 
                        $("#teacher").append(option);
                        for (var i = 0; i<len; i++) {
                            var id = response.users[i].id;
                            var name = response.users[i].name;

                            var option = "<option value='"+id+"'>"+name+"</option>"; 

                            $("#teacher").append(option);
                        }
                    }
                }
            })
        });

        @if(!$teacher_assigned)
            $('.date_or_time').change(function () {
                var date=document.getElementById("date");
                var time=document.getElementById("starttime");
                var combined = new Date(date.value + ' ' + time.value);
                if(date.value=='' || time.value=='') {
                    console.log("Inte färdigvalt");
                } else if(combined < new Date()) {
                    console.log("Dåtid");
                    $("#need_teacher").hide();
                } else {
                    console.log("Framtid");
                    $("#need_teacher").show();
                }
            });
        @endif

        $("#date").change();

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

        @foreach($project_time->lessons as $pt_lesson)
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
        @foreach($project_time->lessons as $pt_lesson)
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

    function deleteprojecttime() {
        if(confirm('Vill du verkligen radera detta lärtillfälle?')) {
            var token = "{{ csrf_token() }}";
            $.ajax({
                url: '/projecttime/{{$project_time->id}}',
                data : {_token:token},
                type: 'DELETE',
                success: function(result) {
                    console.log(result)
                }
            })
            .always(function() {
                window.location='/projecttime/';
            });
        }
    }
</script>

<div class="col-md-8 mb-3">

    <H1>@lang('Redigera lärtillfälle') - {{$project_time->workplace->name}}</H1>

    <form method="post" name="question" action="{{action('ProjectTimeController@update', $project_time->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <select {{$can_edit?'':'disabled'}} class="custom-select d-block w-100" id="type" name="type">
            @foreach($project_time_types as $type)
                <option {{$type->id==$project_time->project_time_type_id?"selected":""}} value="{{$type->id}}">{{$type->name}}</option>
            @endforeach
        </select>

        <br>

        <div class="mb-3">
            <label for="date">@lang('Datum')</label>
            <input {{$can_edit&&!$teacher_assigned?'':'disabled'}} type="date" id="date" name="date" min="{{$mindate}}" max="{{$maxdate}}" class="form-control date_or_time" value="{{$project_time->date}}">
        </div>

        <div class="mb-3">
            <div class="row container">
                <div class="mb-3">
                    <label for="starttime">@lang('Från')</label>
                    <input {{$can_edit&&!$teacher_assigned?'':'disabled'}} type="time" id="starttime" name="starttime" class="form-control time date_or_time" value="{{substr($project_time->starttime, 0, 5)}}">
                </div>
                <div class="mb-3">
                    <label for="endtime">@lang('Till')</label>
                    <input {{$can_edit&&!$teacher_assigned?'':'disabled'}} type="time" name="endtime" class="form-control time" value="{{substr($project_time->endtime, 0, 5)}}">
                </div>
            </div>
        </div>

        <div id="need_teacher">
            <H2>@lang('Lärarstöd')</H2>
            <div class="mb-3">
                @if($can_edit&&!$teacher_assigned)
                    <input type="hidden" name="need_teacher" value="0">
                    <label><input type="checkbox" name="need_teacher" value="1" {{$project_time->need_teacher?"checked":""}}>@lang('Lärarstöd önskas')</label>
                @else
                    <input type="hidden" name="need_teacher" value="{{$project_time->need_teacher?"1":"0"}}">
                    <label><input disabled type="checkbox" name="need_teacher" value="1" {{$project_time->need_teacher?"checked":""}}>@lang('Lärarstöd önskas')</label>
                @endif
            </div>
            <div class="row container">
                <div class="w-50">
                    <select {{$can_change_training_coordinator?'':'disabled'}} class="custom-select d-block" id="training_coordinator" name="training_coordinator">
                        @if($project_time->training_coordinator_id===null)
                            <option selected disabled>@lang('Välj utbildningssamordnare')</option>
                        @endif
                        @foreach($training_coordinators as $coordinator)
                            <option {{$project_time->training_coordinator_id==$coordinator->id?"selected":""}} value="{{$coordinator->id}}">{{$coordinator->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-50">
                    <select {{$can_change_teacher?'':'disabled'}} class="custom-select d-block" id="teacher" name="teacher">
                        @if(isset($teachers))
                            <option selected disabled>@lang('Välj lärare')</option>
                            @foreach($teachers as $teacher)
                                <option {{$project_time->teacher_id==$teacher->id?"selected":""}} value="{{$teacher->id}}">{{$teacher->name}}</option>
                            @endforeach
                        @else
                            <option selected disabled>@lang('Välj utbildningssamordnare först')</option>
                        @endif
                    </select>
                </div>
            </div>
            <br>
            @if($can_see_contact_info)
                <label>@lang('Kontaktinformation arbetsplats')</label><br>
                <div class="card">
                    <div class="card-body">
                        @foreach($project_time->workplace->workplace_admins as $admin)
                            {{$admin->name}} {{$admin->email}} {{$admin->mobile}}<br>
                        @endforeach
                    </div>
                </div>
                <br>
                @isset($project_time->teacher)
                    <label>@lang('Kontaktinformation lärare')</label><br>
                    <div class="card">
                        <div class="card-body">
                            {{$project_time->teacher->name}} {{$project_time->teacher->email}} {{$project_time->teacher->mobile}}<br>
                        </div>
                    </div>
                @endisset
            @endif
        </div>
        <br><br>

        <H2>@lang('Moduler')</H2>
        <div id="lessonwrapper">
            @foreach($project_time->lessons as $pt_lesson)
                <div class="mb-3" id="lesson[{{$pt_lesson->id}}]" data-id="{{$pt_lesson->id}}">
                    <div class="row container">
                        <div class="w-50">
                            <select {{$can_edit?'':'disabled'}} class="custom-select d-block track" data-id="{{$pt_lesson->id}}" id="track{{$pt_lesson->id}}">
                                @foreach($tracks as $track)
                                    <option {{$track->id==$pt_lesson->track->id?"selected":""}} value="{{$track->id}}">{{$track->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-50">
                            <select {{$can_edit?'':'disabled'}} class="custom-select d-block" id="lessons{{$pt_lesson->id}}" name="lessons[{{$pt_lesson->id}}]">
                                <option disabled value="-1">@lang('Välj en modul')</option>
                                @foreach($lessons as $lesson)
                                    <option {{$lesson->id==$pt_lesson->id?"selected":""}} value="{{$lesson->id}}" data-track="{{$lesson->track_id}}">{{$lesson->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($can_edit)
            <div id="addlessonrow">@lang('Lägg till modul')</div>
        @endif
        <br>

        @if($can_see_collegues)
            <H2>@lang('Närvarande personer')</H2>
            @foreach($workplace->users->sortBy('name') as $user)
                <div class="checkbox">
                    <label><input {{$can_edit?'':'disabled'}} type="checkbox" name="users[]" {{$project_time->users->contains('id',$user->id) ? 'checked' : '' }} value="{{$user->id}}" id="{{$user->id}}">{{$user->name}}</label>
                </div>
            @endforeach
        @else
            <input type="hidden" name="users[]" value="{{$user->id}}" id="{{$user->id}}">
        @endif

        <br>

        {{--@if(!$can_edit)
            <input type="hidden" name="starttime" value="{{substr($project_time->starttime, 0, 5)}}">
            <input type="hidden" name="endtime" value="{{substr($project_time->endtime, 0, 5)}}">
        @endif--}}

        <button {{$can_edit||$can_change_teacher?'':'disabled'}} class="btn btn-primary btn-lg" id="submit" name="submit" type="submit">@lang('Spara')</button>
        <button {{$can_edit&&!$teacher_assigned?'':'disabled'}} type="button" class="btn btn-lg btn-danger" onclick="deleteprojecttime()">@lang('Radera lärtillfälle')</button>
    </form>

</div>

@endsection
