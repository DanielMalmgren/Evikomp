<a class="list-group-item list-group-item-action">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-4">
            <h5 class="mb-0">@lang('Namn')</h5>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 text-center">
            <h5 class="mb-0">@lang('Registrerad tid')</h5>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-3 text-center">
            <h5 class="mb-0">@lang('Deltagares attest')</h5>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-2 text-center">
            <h5 class="mb-0">@lang('Chefs attest')</h5>
        </div>
        <div class="col-lg-1 d-md-none d-sm-none d-lg-block text-center">
            <h5 class="mb-0">@lang('Radera')</h5>
        </div>
        <div class="col-lg-1 d-md-none d-sm-none d-lg-block text-center">
            <h5 class="mb-0">@lang('Info')</h5>
        </div>
    </div>
</a>
{{--<a class="list-group-item list-group-item-action">
    <div class="row">
        <div class="col-lg-4 col-md-9 col-sm-7"></div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            @lang('anställd')
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            @lang('koordinator')
        </div>
        <div class="col-lg-1 col-md-3 col-sm-5">
            @lang('chef/APK')
        </div>
    </div>
</a>--}}

<br>

@foreach($workplace->users->sortBy('name') as $user)
    @php
        $total_time = round($user->month_total_time($year, $month), 1);
        $attestedlevel1 = $user->time_attests->where('attestlevel', 1)->where('month', $month)->where('year', $year)->sum('hours');
        $attestedlevel3 = $user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->sum('hours');
    @endphp
    <a class="list-group-item list-group-item-action" id="user-{{$user->id}}">
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-4">
                <h5 class="mb-0">{{$user->name}}</h5>
            </div>
            @if($user->time_attests->where('attestlevel', 0)->where('month', $month)->where('year', $year)->isNotEmpty()) {{-- Det finns endast attest level 0 för denna person, dvs attestering sker på papper--}}
                <div class="col-lg-6 col-md-6 col-sm-8">
                    <div class="text-danger">@lang('Attesteras manuellt på papper')</div>
                </div>
            @else
                <div class="col-lg-2 col-md-2 col-sm-3 text-center">
                    <div>{{$total_time}}</div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-3 text-center">
                    <div class="{{$attestedlevel1<$total_time?'text-danger':''}}">{{$attestedlevel1}}</div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 text-center">
                    @if($user->id == Auth::user()->id || $attestedlevel1==0 || $total_time==0)
                        <input disabled type="checkbox">
                    @elseif($attestedlevel3 >= $total_time)
                        <input checked disabled type="checkbox" data-toggle="tooltip" title="@lang('Attesterat av') {{$user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->first()->attestant->name}}">
                    @else
                        <input type="checkbox" name="level3attest[]" value="{{$user->id}}" {{$attestlevel>=3?"":"disabled"}} onclick="togglesubmit(3)">
                    @endif
                </div>
            @endif

            <div class="col-lg-1 col-md-1 d-sm-none d-md-block text-center" onclick="deleteuser({{$user->id}}, '{{$user->name}}')">
                <i class="fas fa-trash"></i>
            </div>
            <div class="col-lg-1 col-md-1 d-sm-none d-md-block text-center" onclick="toggleuserdetails({{$user->id}})">
                <i class="fas fa-list"></i>
            </div>
        </div>
        <div id="details-{{$user->id}}"></div>
    </a>

    <br>
@endforeach

<div class="row">
    <div class="col-lg-5 col-md-6 col-sm-7"></div>
    <div class="col-lg-2 col-md-2 col-sm-3 text-right">
        @lang('Markera alla')
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 text-center">
        <input type="checkbox" id="selectall_level3" {{$attestlevel>=2?"":"disabled"}} onclick="toggleattests(3)">
    </div>
</div>

<br>
{{--@if($month_is_closed)
    <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Månaden är stängd för attestering')</button>
@else--}}
    <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Attestera')</button>
{{--@endif--}}

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
                cb[i].checked = ca.checked;
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
