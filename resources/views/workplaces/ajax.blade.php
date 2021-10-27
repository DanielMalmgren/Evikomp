<form method="post" name="question" action="{{action('WorkplaceController@update', $workplace->id)}}" accept-charset="UTF-8">
    @method('put')
    @csrf

    <br>

    <div class="mb-3">
        <label for="name">@lang('Namn')</label>
        <input name="name" class="form-control" id="name" value="{{$workplace->name}}">
    </div>

    <label for="workplace_type">@lang('Typ av arbetsplats')</label>
    <select class="custom-select d-block w-100" id="workplace_type" name="workplace_type" required="">
        @foreach($workplace_types as $workplace_type)
            <option value="{{$workplace_type->id}}" {{$workplace->workplace_type_id==$workplace_type->id?"selected":""}}>{{$workplace_type->name}}</option>
        @endforeach
    </select>

    <br>

    <div class="mb-3">
        <input type="hidden" name="includetimeinreports" value="0">
        <label><input type="checkbox" name="includetimeinreports" value="1" {{$workplace->includetimeinreports?"checked":""}}>@lang('Inkludera i tidrapporter')</label>
    </div>

    <div class="mb-3">
        <input type="hidden" name="send_attest_reminders" value="0">
        <label><input type="checkbox" name="send_attest_reminders" value="1" {{$workplace->send_attest_reminders?"checked":""}}>@lang('Skicka attestpåminnelser')</label>
    </div>

    <div class="mb-3">
        <input type="hidden" name="training_coordinator" value="0">
        <label><input type="checkbox" name="training_coordinator" value="1" {{$workplace->training_coordinator?"checked":""}}>@lang('Utbildningsanordnare')</label>
    </div>

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

    <label>@lang('Registrerade personer')</label>
    @if(count($workplace->users) > 0)
        @foreach($workplace->users->sortBy('name') as $user)
            <a class="list-group-item list-group-item-action" id="user-{{$user->id}}">
                <div class="row">
                    <div class="col-lg-4 col-md-9 col-sm-7">
                        {{$user->name}}
                    </div>
                    <div class="col-lg-1 col-md-3 col-sm-5">
                        <i class="far fa-chart-bar" onClick="window.location='/users/{{$user->id}}'"></i>
                    </div>
                    <div class="col-lg-1 col-md-3 col-sm-5">
                        <i class="fas fa-user-edit" onClick="window.location='/settings/{{$user->id}}'"></i>
                    </div>
                    <div class="col-lg-1 col-md-3 col-sm-5" onclick="deleteuser({{$user->id}}, '{{$user->name}}')">
                        <i class="fas fa-trash"></i>
                    </div>
                </div>
            </a>
        @endforeach
    @else
        <br>
        @lang('Inga personer har registrerat sig på denna arbetsplats')
        <br>
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
                            <div class="col-lg-3 col-md-3 col-sm-5 adminleveldiv">
                                {{--<select class="custom-select d-block w-100" name="adminlevel[{{$admin->id}}]">
                                    <option value="2" {{$admin->pivot->attestlevel==2?"selected":""}}>@lang('Arbetsplatskoordinator')</option>
                                    <option value="3" {{$admin->pivot->attestlevel==3?"selected":""}}>@lang('Chef')</option>
                                </select>--}}
                                <input type="hidden" name="adminlevel[{{$admin->id}}]" value="3">
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

    <button class="btn btn-primary btn-lg" id="submit" name="submit" type="submit">@lang('Spara')</button>
    @can('add workplaces')
        <button type="button" class="btn btn-lg btn-danger" onclick="deleteworkplace()" {{$deleteable?'':'disabled'}}>@lang('Radera arbetsplats')</button>
    @endcan
    @hasrole('Admin')
        <a href="/log?subject_id={{$workplace->id}}&subject_type=App\Workplace" class="btn btn-secondary">@lang('Visa logg')</a>
    @endhasrole

</form>

<link href="/select2/select2.min.css" rel="stylesheet" />
<link href="/select2/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="/select2/select2.min.js"></script>
<script src="/select2/i18n/sv.js"></script>

<script type="text/javascript">

    function deleteuser(user_id, user_name) {
        if(confirm('Vill du verkligen radera '+user_name+' ifrån {{$workplace->name}}?')) {
            $("#user-"+user_id).remove();
            var token = "{{ csrf_token() }}";
            $.ajax({
                url: '/user/'+user_id,
                data : {_token:token},
                type: 'DELETE'
            });
        }
    }

    function deleteworkplace() {
        if(confirm('Vill du verkligen radera {{$workplace->name}}?')) {
            var token = "{{ csrf_token() }}";
            $.ajax({
                url: '/workplace/{{$workplace->id}}',
                data : {_token:token},
                type: 'DELETE',
                success: function(result) {
                    console.log(result)
                }
            })
            .always(function() {
                location.reload();
            });
        }
    }

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
            $(wrapper).append('<a class="list-group-item list-group-item-action"><div class="row"><div class="col-lg-4 col-md-9 col-sm-7"><select class="new_admins" name="new_admins[]"></select></div><div class="col-lg-3 col-md-3 col-sm-5 adminleveldiv"><select class="custom-select d-block w-100 adminlevel" name="adminlevel[]"><option value="3">@lang('Chef')</option></select></div><div class="col-lg-1 col-md-3 col-sm-5"><i class="fas fa-trash remove_field"></i></div></div></a>');
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
