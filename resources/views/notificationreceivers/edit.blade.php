@extends('layouts.app')

@section('title', __('Redigera notifieringsmottagare'))

@section('content')

<link href="/tree-multiselect/jquery.tree-multiselect.min.css" rel="stylesheet">
<script src="/tree-multiselect/jquery.tree-multiselect.min.js"></script>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Redigera notifieringsmottagare för '){{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    <form method="post" name="notificationreceivers" action="{{action('NotificationReceiversController@update', $lesson->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @method('put')
        @csrf

        <div id="notification_receivers_wrap">
            @foreach($lesson->notification_receivers->unique('user_id') as $notification_receiver)

                @php
                    $this_users_workplaces = $lesson->notification_receivers->where('user_id', $notification_receiver->user->id)
                @endphp

                <div id="user[{{$notification_receiver->user_id}}]" data-id="{{$notification_receiver->user_id}}" class="card">
                    <div class="card-header">
                        <label class="handle">
                            {{$notification_receiver->user->name}}
                        </label>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body{{$notification_receiver->user_id}}" id="collapstoggle{{$notification_receiver->user_id}}">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body{{$notification_receiver->user_id}}">
                        <div class="card-body">
                            <select id="workplaces{{$notification_receiver->user_id}}" name="workplaces[{{$notification_receiver->user_id}}][]" multiple="multiple">
                            @foreach($workplaces as $workplace)
                                <option value="{{$workplace->id}}" data-section="{{$workplace->municipality->name}}" {{$this_users_workplaces->contains('workplace_id', $workplace->id)?"selected":""}}>{{$workplace->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <script type="text/javascript">
                    $("select#workplaces{{$notification_receiver->user_id}}").treeMultiselect({
                        startCollapsed: true,
                        hideSidePanel: true
                    });
                </script>

            @endforeach
        </div>

        <br>

        <div id="add_notification_receiver_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till notifieringsmottagare')</div>

        <br>

        <button class="btn btn-primary btn-lg btn-primary" type="submit">@lang('Spara')</button><br>

    </form>

<script type="text/javascript">

    function getfreeid() {
        for(;;) {
            testnumber = Math.floor((Math.random() * 1000) + 1);
            hit = 0;
            $('#notification_receivers_wrap').children().each(function() {
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
        $('.new_notification_receivers').select2({
            width: '500px',
            ajax: {
                url: '/select2users',
                dataType: 'json'
            },
            language: "{{substr(App::getLocale(), 0, 2)}}",
            minimumInputLength: 3,
            theme: "bootstrap4"
        });
    }

    $(function() {
        var wrapper = $("#notification_receivers_wrap");
        var add_button = $("#add_notification_receiver_button");

        $(add_button).click(function(e){
            e.preventDefault();
            new_id = getfreeid();
            $(wrapper).append(`
                <div id="user[`+new_id+`]" data-id="`+new_id+`" class="card">
                    <div class="card-header">
                        <select class="new_notification_receivers" name="new_notification_receivers[`+new_id+`]"></select>
                        <a href="#" class="close remove_field pl-3" data-dismiss="alert" aria-label="close">&times;</a>
                        <a data-toggle="collapse" href="#body`+new_id+`" id="collapstoggle`+new_id+`">
                            <i class="fas fa-chevron-up float-right text-dark"></i>
                        </a>
                    </div>
                    <div class="collapse multi-collapse show" id="body`+new_id+`">
                        <div class="card-body">
                            <select id="workplaces`+new_id+`" name="workplaces[`+new_id+`][]" multiple="multiple">
                            @foreach($workplaces as $workplace)
                                <option value="{{$workplace->id}}" data-section="{{$workplace->municipality->name}}">{{$workplace->name}}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            `);
            addselect2();
            $("select#workplaces"+new_id).treeMultiselect({
                startCollapsed: true,
                hideSidePanel: true
            });
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            $(this).parent('div').parent('div').remove();
        })

    });
</script>

@endsection
