@extends('layouts.app')

@section('content')

<div class="col-md-6">

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Redigera spår')</H1>

    <form method="post" action="{{action('TrackController@update', $track->id)}}" accept-charset="UTF-8">
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
            <input type="hidden" name="active" value="0">
            <label><input type="checkbox" name="active" value="1" {{$track->active?"checked":""}}>@lang('Aktiv')</label>
        </div>

        <br>

        @can('manage permissions')
            <label>@lang('Redaktörer')</label>
            <div id="admins_wrap">
                @if(count($track->track_admins) > 0)
                    @foreach($track->track_admins as $admin)
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

            <div id="add_admin_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till redaktör')</div>
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

        $('.new_admins').on('select2:select', function (e) {
            var userid = e.params.data.id;
            var adminlevel = $(this).parent('div').parent('div').find('.adminlevel');
            adminlevel.attr('name', 'adminlevel[' + userid + ']');
        });
    }

    $(function() {
        var wrapper = $("#admins_wrap");
        var add_button = $("#add_admin_button");

        $(add_button).click(function(e){
            e.preventDefault();
            $(wrapper).append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-9 col-md-9 col-sm-7"><select class="new_admins" name="new_admins[]"></select></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
            addselect2();
        });

        $(wrapper).on("click",".remove_field", function(e){
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
