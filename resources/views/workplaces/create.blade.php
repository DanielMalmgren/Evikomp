@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>Skapa ny arbetsplats</H1>

    <div id="settings">

        <form method="post" name="question" action="{{action('WorkplaceController@store')}}" accept-charset="UTF-8">
            @csrf

            <div class="mb-3">
                <label for="name">@lang('Namn')</label>
                <input name="name" class="form-control" id="name">
            </div>

            <div class="mb-3">
                <label for="municipality">@lang('Kommun')</label>
                <select class="custom-select d-block w-100" name="municipality" id="municipality" required="">
                    @foreach($municipalities as $municipality)
                        <option value="{{$municipality->id}}">{{$municipality->name}}</option>
                    @endforeach
                </select>
            </div>

            <label for="workplace_type">@lang('Typ av arbetsplats')</label>
            <select class="custom-select d-block w-100" id="workplace_type" name="workplace_type" required="">
                @foreach($workplace_types as $workplace_type)
                    <option value="{{$workplace_type->id}}">{{$workplace_type->name}}</option>
                @endforeach
            </select>

            <br>

            <label>@lang('Obligatoriska spår')</label>
            @if(count($tracks) > 0)
                <div class="card">
                    <div class="card-body">
                        @foreach($tracks as $track)
                            <div class="checkbox">
                                <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}">{{$track->translateOrDefault(App::getLocale())->name}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <br>

            @can('manage permissions')
                <label>@lang('Administratörer')</label>
                <div id="admins_wrap"></div>

                <br>

                <div id="add_admin_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till administratör')</div>
            @endcan

            <br><br>

            <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
        </form>

        <link href="/select2/select2.min.css" rel="stylesheet" />
        <link href="/select2/select2-bootstrap4.min.css" rel="stylesheet" />
        <script src="/select2/select2.min.js"></script>
        <script src="/select2/i18n/sv.js"></script>

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
                    $(wrapper).append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-4 col-md-9 col-sm-7"><select class="new_admins" name="new_admins[]"></select></div><div class="col-lg-3 col-md-3 col-sm-5 adminleveldiv"><select class="custom-select d-block w-100 adminlevel" name="adminlevel[]"><option value="2">@lang('Arbetsplatskoordinator')</option><option value="3">@lang('Chef')</option></select></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
                    addselect2();
                });

                $(wrapper).on("click",".remove_field", function(e){
                    e.preventDefault();
                    var parentdiv = $(this).parent('div').parent('div').parent('a');
                    var adminid = $(this).parent('div').parent('div').find('.adminid');
                    var adminleveldiv = $(this).parent('div').parent('div').find('.adminleveldiv');
                    adminleveldiv.remove();
                    var oldname = adminid.attr('name');
                    parentdiv.hide();
                    adminid.attr('name', 'remove_' + oldname);
                })

            });
        </script>

    </div>

@endsection
