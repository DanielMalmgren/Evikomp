@extends('layouts.app')

@section('content')

<div class="col-md-6">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Redigera spår')</H1>

    <form method="post" action="{{action('TrackController@update', $track->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @method('put')
        @csrf

        <div class="mb-3">
            <label for="name">@lang('Namn')</label>
            <input name="name" class="form-control" id="name" value="{{$track->translateOrDefault(App::getLocale())->name}}">
        </div>

        <div class="mb-3">
            <label for="subtitle">@lang('Undertitel')</label>
            <input name="subtitle" class="form-control" id="subtitle" value="{{$track->translateOrDefault(App::getLocale())->subtitle}}">
        </div>

        <div class="mb-3">
            <label for="color">@lang('Färg')</label>
            <input name="color" type="color" list="presetColors" value="{{$track->color->hex}}">
            <datalist id="presetColors">
                @foreach($colors as $color)
                    <option>{{$color->hex}}</option>
                @endforeach
            </datalist>
        </div>

        <div class="mb-3">
            <label for="icon">@lang('Ikon: ') </label>
            <img class="lessonimage" src="/storage/icons/{{$track->icon}}" style="max-width:50px">
            <input name="icon" class="form-control" type="file" accept="image/jpeg,image/png,image/gif">
        </div>

        <div class="mb-3">
            <input type="hidden" name="active" value="0">
            <label><input type="checkbox" name="active" value="1" {{$track->active?"checked":""}}>@lang('Aktiv')</label>
        </div>

        <br>

        @can('manage permissions')
            <div id="outer_wrap">
                <label>@lang('Redaktörer')</label>
                <div id="admins_wrap">
                    @if(count($track->track_admins->where('pivot.is_editor', true)) > 0)
                        @foreach($track->track_admins->where('pivot.is_editor', true) as $admin)
                            <a class="list-group-item list-group-item-action">
                                <div class="row">
                                    <input type="hidden" class="adminid" name="admin[{{$admin->id}}]">
                                    <div class="col-lg-4 col-md-9 col-sm-7">
                                        {{$admin->name}}
                                    </div>
                                    <div class="col-lg-1 col-md-3 col-sm-5">
                                        <i class="fas fa-trash remove_field"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
                <br>
                <div id="add_admin_button" class="btn btn-secondary" style="margin-bottom:15px" type="text">@lang('Lägg till redaktör')</div>

                <br><br>

                <label>@lang('Faktagranskare')</label>
                <div id="factcheckers_wrap">
                    @if(count($track->track_admins->where('pivot.is_editor', false)) > 0)
                        @foreach($track->track_admins->where('pivot.is_editor', false) as $admin)
                            <a class="list-group-item list-group-item-action">
                                <div class="row">
                                    <input type="hidden" class="adminid" name="factchecker[{{$admin->id}}]">
                                    <div class="col-lg-4 col-md-9 col-sm-7">
                                        {{$admin->name}}
                                    </div>
                                    <div class="col-lg-1 col-md-3 col-sm-5">
                                        <i class="fas fa-trash remove_field"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
                <br>
                <div id="add_factchecker_button" class="btn btn-secondary" style="margin-bottom:15px" type="text">@lang('Lägg till faktagranskare')</div>
            </div>
        @endcan

        <br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>
</div>

<script type="text/javascript">

    function addselect2() {
        $('.new_admins').select2({
            width: '100%',
            ajax: {
                url: '/select2users',
                dataType: 'json'
            },
            language: "sv",
            minimumInputLength: 3,
            theme: "bootstrap4"
        });

        /*$('.new_admins').on('select2:select', function (e) {
            var userid = e.params.data.id;
            var adminlevel = $(this).parent('div').parent('div').find('.adminlevel');
            adminlevel.attr('name', 'adminlevel[' + userid + ']');
        });*/
    }

    $(function() {
        //var wrapper = $("#admins_wrap");
        //var add_button = $("#add_admin_button");

        $("#add_admin_button").click(function(e){
            e.preventDefault();
            $("#admins_wrap").append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-9 col-md-9 col-sm-7"><select class="new_admins" name="new_admins[]"></select></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
            addselect2();
        });

        $("#add_factchecker_button").click(function(e){
            e.preventDefault();
            $("#factcheckers_wrap").append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-9 col-md-9 col-sm-7"><select class="new_admins" name="new_factcheckers[]"></select></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
            addselect2();
        });

        $("#outer_wrap").on("click",".remove_field", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div').parent('div').parent('a');
            var adminid = $(this).parent('div').parent('div').find('.adminid');
            var oldname = adminid.attr('name');
            parentdiv.hide();
            adminid.attr('name', 'remove_' + oldname);
        })

    });
</script>

@endsection
