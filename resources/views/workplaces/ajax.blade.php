<form method="post" name="question" action="{{action('WorkplaceController@update', $workplace->id)}}" accept-charset="UTF-8">
    @method('put')
    @csrf

    <br>

    <label for="workplace_type">@lang('Typ av arbetsplats')</label>
    <select class="custom-select d-block w-100" id="workplace_type" name="workplace_type" required="">
        @foreach($workplace_types as $workplace_type)
            <option value="{{$workplace_type->id}}" {{$workplace->workplace_type_id==$workplace_type->id?"selected":""}}>{{$workplace_type->name}}</option>
        @endforeach
    </select>

    <br>

    <label>@lang('Obligatoriska spår')</label>
    @if(count($tracks) > 0)
    <div class="card">
        <div class="card-body">
            @foreach($tracks as $track)
                <div class="checkbox">
                    @if($workplace->tracks->contains('id', $track->id))
                        <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}" checked>{{$track->translateOrDefault(App::getLocale())->name}}</label>
                    @else
                        <label><input type="checkbox" name="tracks[]" value="{{$track->id}}" id="{{$track->id}}">{{$track->translateOrDefault(App::getLocale())->name}}</label>
                    @endif
                </div>
            @endforeach
            </div>
        </div>
    @endif

    <br>

    @can('manage permissions')
        <label>@lang('Administratörer')</label>
        <div id="admins_wrap">
            @if(count($workplace->workplace_admins) > 0)
                @foreach($workplace->workplace_admins as $admin)
                    <a class="list-group-item list-group-item-action">
                        <div class="row">
                            <input type="hidden" class="adminid" name="admin[{{$admin->id}}]">
                            <div class="col-lg-4 col-md-9 col-sm-7">
                                {{$admin->name}}
                            </div>
                            <div class="col-lg-1 col-md-3 col-sm-5">
                                <input type="checkbox" name="adminlevel2[{{$admin->id}}]" {{$admin->pivot->attestlevel == 2?"checked":""}}>
                            </div>
                            <div class="col-lg-1 col-md-3 col-sm-5">
                                <input type="checkbox" name="adminlevel3[{{$admin->id}}]" {{$admin->pivot->attestlevel == 3?"checked":""}}>
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

        <div id="add_admin_button" class="btn btn-primary" style="margin-bottom:15px" type="text">@lang('Lägg till administratör')</div>
    @endcan

    <br><br>

    <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
</form>

<link href="/select2/select2.min.css" rel="stylesheet" />
<script src="/select2/select2.min.js"></script>

<script type="text/javascript">

    function addselect2() {
        $('.new_admins').select2({
            ajax: {
                url: '/select2users',
                dataType: 'json'

            },
            minimumInputLength: 2
        });
    }

    $(function() {
        var wrapper = $("#admins_wrap");
        var add_button = $("#add_admin_button");

        $(add_button).click(function(e){
            e.preventDefault();
            $(wrapper).append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-4 col-md-9 col-sm-7"><select class="new_admins" name="new_admins[]"></select></div><div class="col-lg-1 col-md-3 col-sm-5"><input type="checkbox"></div><div class="col-lg-1 col-md-3 col-sm-5"><input type="checkbox"></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
            addselect2();
        });

        $(wrapper).on("click",".remove_field", function(e){
            e.preventDefault();
            var parentdiv = $(this).parent('div').parent('div').parent('a');
            var adminid = $(this).parent('div').parent('div').find('.adminid')
            var oldname = adminid.attr('name');
            parentdiv.hide();
            adminid.attr('name', 'remove_' + oldname);
        })

    });
</script>
