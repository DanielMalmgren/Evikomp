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
                alert('Ange tidpunkterna i formatet hh:mm!');
                $(this).val('');
            }
            if(selectedValue.substring(0,2) == '00') {
                if(!confirm('Den tid du försöker ange är mitt i natten.\nÄr du säker på att det är detta du vill?')) {
                    $(this).val('');
                }
            }
        });

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
        $lessonSelect[0] = $('select[id="lessons0"]');
        $lessons[0] = $('option', $lessonSelect[0]);
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
                $("#lessons"+id).val("-1").change();
            });
        }
        addTrackChangeListener(0);
        $("#track0").change();
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

    function toggleall() {
        var ca=document.getElementById("togglecb");
        var cb=document.getElementsByName("users[]");
        var cb_length=cb.length;
        for(var i=0; i < cb_length; i++) {
            cb[i].checked = ca.checked;
        }
    }

</script>

@hasrole('Admin')
    <small><a class="black" href="/projecttime/{{$workplace->id}}">@lang('Visa befintlig projekttid.')</a></small>
    <br><br>
@endhasrole

<form method="post" name="question" action="{{action('ProjectTimeController@store')}}" accept-charset="UTF-8">
    @csrf

    <input type="hidden" name="workplace_id" value="{{$workplace->id}}">

    <select class="custom-select d-block w-100" id="type" name="type" required="">
        @foreach($project_time_types as $type)
            <option value="{{$type->id}}">{{$type->name}}</option>
        @endforeach
    </select>

    <div class="mb-3">
        <label for="date">@lang('Datum')</label>
        <input required type="date" id="date" name="date" min="{{$mindate}}" max="{{$maxdate}}" class="form-control date_or_time" value="{{old('date')??$date??''}}">
    </div>

    <div class="mb-3">
        <div class="row container">
            <div class="mb-3">
                <label for="starttime">@lang('Från')</label>
                <input required type="time" id="starttime" name="starttime" class="form-control time date_or_time" value="{{old('starttime')??$time??''}}">
            </div>
            <div class="mb-3">
                <label for="endtime">@lang('Till')</label>
                <input required type="time" name="endtime" class="form-control time" value="{{old('endtime')}}">
            </div>
        </div>
    </div>

    @if(!isset($singleuser) || !$singleuser)
        <div id="need_teacher" style="display: none;">
            <div class="mb-3">
                <input type="hidden" name="need_teacher" value="0">
                <label><input type="checkbox" name="need_teacher" value="1">@lang('Lärarstöd önskas')</label>
            </div>
        </div>

        <H2>@lang('Moduler')</H2>
        <div id="lessonwrapper">
            <div class="mb-3" id="lesson[0]" data-id="0">
                <div class="row container">
                    <div class="w-50">
                        <select class="custom-select d-block w-200 track" data-id="0" id="track0">
                            <option selected disabled>@lang('Välj ett spår')</option>
                            @foreach($tracks as $track)
                                <option value="{{$track->id}}">{{$track->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-50">
                        <select class="custom-select d-block w-200" id="lessons0" name="lessons[0]">
                            <option selected disabled value="-1">@lang('Välj en modul')</option>
                            @foreach($lessons as $lesson)
                                <option value="{{$lesson->id}}" data-track="{{$lesson->track_id}}">{{$lesson->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div id="addlessonrow">@lang('Lägg till modul')</div>
        <br>

        <H2>@lang('Närvarande personer')</H2>
        <label><input type="checkbox" id="togglecb" onclick="toggleall()">@lang('Markera alla')</label>
        @foreach($workplace->users->sortBy('name') as $user)
            <div class="checkbox">
                <label><input type="checkbox" name="users[]" {{(is_array(old('users')) && in_array($user->id, old('users'))) ? 'checked' : '' }} value="{{$user->id}}" id="{{$user->id}}">{{$user->name}}</label>
            </div>
        @endforeach

        <br><br>

        <label><input {{$workplace->workplace_admins->isEmpty()?'disabled':''}} type="checkbox" id="generate_presence_list" name="generate_presence_list" {{old('generate_presence_list') !== null ? 'checked' : '' }}>@lang('Skriv ut närvarolista')</label>

        @if($workplace->workplace_admins->isNotEmpty())
            <div id="boss_wrapper" style="display: none;">
                <label for="signing_boss">@lang('Attesterande chef')</label>
                <select class="custom-select d-block w-100" name="signing_boss">
                    @foreach($workplace->workplace_admins as $admin)
                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif
    @else
        <input type="hidden" name="users[]" value="{{\Auth::user()->id}}" id="{{\Auth::user()->id}}">
    @endif

    <br>

    <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
</form>

@if($workplace->workplace_admins->count() > 1)
    <script type="text/javascript">
        $(function() {
            $('#generate_presence_list').on('change', function() {
                var val = this.checked;
                $("#boss_wrapper").toggle(this.checked);
            });
        });
    </script>
@endif
