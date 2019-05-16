<a class="list-group-item list-group-item-action">
    <div class="row">
        <div class="col-lg-3 col-md-7 col-sm-5">
            <h5 class="mb-0">@lang('Namn')</h5>
        </div>
        <div class="col-lg-1 col-md-2 col-sm-2">
            <h5 class="mb-0">@lang('Timmar')</h5>
        </div>
        <div class="col-lg-3 col-md-9 col-sm-15">
            <h5 class="mb-0">@lang('Attesterad')</h5>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            <h5 class="mb-0">@lang('Radera')</h5>
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            <h5 class="mb-0">@lang('Info')</h5>
        </div>
    </div>
</a>
<a class="list-group-item list-group-item-action">
    <div class="row">
        <div class="col-lg-4 col-md-9 col-sm-7"></div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            @lang('anställd')
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            @lang('koordinator')
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            @lang('chef')
        </div>
    </div>
</a>

<br>

@foreach($workplace->users->sortBy('name') as $user)
    <a class="list-group-item list-group-item-action" id="user-{{$user->id}}">
        <div class="row">
            <div class="col-lg-3 col-md-7 col-sm-5">
                <h5 class="mb-0">{{$user->name}}</h5>
            </div>

                @if($user->time_attests->where('month', $month)->where('year', $year)->count() > 0)
                    <div class="col-lg-1 col-md-2 col-sm-2">
                        <small>{{$user->time_attests->where('attestlevel', 1)->where('month', $month)->where('year', $year)->first()->hours}}</small>
                    </div>
                    <div class="col-lg-1 col-md-3 col-sm-5">
                        <input checked disabled type="checkbox">
                    </div>
                    <div class="col-lg-1 col-md-3 col-sm-5">
                        @if($user->time_attests->where('attestlevel', 2)->where('month', $month)->where('year', $year)->count() > 0)
                            <input checked disabled type="checkbox" id="level2attest-{{$user->id}}" name="level2attest[]" value="{{$user->id}}" {{$attestlevel>=2&&!$month_is_closed?"":"disabled"}} onclick="togglesubmit(2)">
                        @else
                            <input type="checkbox" id="level2attest-{{$user->id}}" name="level2attest[]" value="{{$user->id}}" {{$attestlevel>=2&&!$month_is_closed?"":"disabled"}} onclick="togglesubmit(2)">
                        @endif
                    </div>
                    <div class="col-lg-1 col-md-3 col-sm-5">
                        @if($user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->count() > 0)
                            <input checked disabled type="checkbox" name="level3attest[]" value="{{$user->id}}" {{$attestlevel>=3&&!$month_is_closed?"":"disabled"}} onclick="togglesubmit(3)">
                        @else
                            <input type="checkbox" name="level3attest[]" value="{{$user->id}}" {{$attestlevel>=3&&!$month_is_closed?"":"disabled"}} onclick="togglesubmit(3)">
                        @endif
                    </div>
                @else
                    <div class="col-lg-4 col-md-2 col-sm-2">
                        <div class="text-danger">Ej attesterad</div>
                    </div>
                @endif

            <div class="col-lg-1 col-md-3 col-sm-5" onclick="deleteuser({{$user->id}}, '{{$user->name}}')">
                <i class="fas fa-trash"></i>
            </div>
            <div class="col-lg-1 col-md-3 col-sm-5" onclick="toggleuserdetails({{$user->id}})">
                <i class="fas fa-list"></i>
            </div>
        </div>
        <div id="details-{{$user->id}}"></div>
    </a>

    <br>
@endforeach

<div class="row">
    <div class="col-lg-3"></div>
    <div class="col-lg-2">
        @lang('Markera alla')
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1">
        <input type="checkbox" id="selectall_level2" {{$attestlevel>=2&&!$month_is_closed?"":"disabled"}} onclick="toggleattests(2)">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1">
        <input type="checkbox" id="selectall_level3" {{$attestlevel>=3&&!$month_is_closed?"":"disabled"}} onclick="toggleattests(3)">
    </div>
</div>

<br>
@if($month_is_closed)
    <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Månaden är stängd för attestering')</button>
@else
    <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Attestera')</button>
@endif

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

    function toggleattests(level) {
        var ca=document.getElementById("selectall_level"+level);
        var cb=document.getElementsByName("level"+level+"attest[]");
        var cb_length=cb.length;
        for(var i=0; i < cb_length; i++) {
            if(cb[i].disabled == false) {
                if(level == 2 || !ca.checked) {
                    cb[i].checked = ca.checked;
                } else {
                    var user_id=cb[i].value;
                    var c2=document.getElementById("level2attest-"+user_id);
                    if(c2.checked) {
                        cb[i].checked = ca.checked;
                    }
                }
            }
        }
        togglesubmit(level);
    }

    function togglesubmit(level) {
        var total=0;
        var cb=document.getElementsByName("level"+level+"attest[]");
        var cb_length=cb.length;
        for(var i=0; i < cb_length; i++) {
            if(cb[i].checked && !cb[i].disabled) {
                total=total+1;
            }
            if(total == 0){
                document.settings.submit.disabled = true;
            } else {
                document.settings.submit.disabled = false;
            }
        }
    }

    function toggleuserdetails(user_id) {
        if($("#details-"+user_id).is(':empty')) {
            $("#details-"+user_id).load("/timeattestajaxuserdetails/"+user_id+"/{{$year}}/{{$month}}");
        } else {
            $("#details-"+user_id).empty();
        }
    }

</script>
