
@foreach($workplace->users->sortBy('name') as $user)
    <a class="list-group-item list-group-item-action">
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
                            <input checked disabled type="checkbox" name="level2attest[]" value="{{$user->id}}" {{$attestlevel >= 2?"":"disabled"}} onclick="togglesubmit(2)">
                        @else
                            <input type="checkbox" name="level2attest[]" value="{{$user->id}}" {{$attestlevel >= 2?"":"disabled"}} onclick="togglesubmit(2)">
                        @endif
                    </div>
                    <div class="col-lg-1 col-md-3 col-sm-5">
                        @if($user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->count() > 0)
                            <input checked disabled type="checkbox" name="level3attest[]" value="{{$user->id}}" {{$attestlevel >= 3?"":"disabled"}} onclick="togglesubmit(3)">
                        @else
                            <input type="checkbox" name="level3attest[]" value="{{$user->id}}" {{$attestlevel >= 3?"":"disabled"}} onclick="togglesubmit(3)">
                        @endif
                    </div>
                @else
                    <div class="col-lg-4 col-md-2 col-sm-2">
                        <div class="text-danger">Ej attesterad</div>
                    </div>
                @endif

            <div class="col-lg-1 col-md-3 col-sm-5">
                <i class="fas fa-trash"></i>
            </div>
            <div class="col-lg-1 col-md-3 col-sm-5" onclick="toggleuserdetails({{$user->id}})">
                <i class="fas fa-list"></i>
            </div>
        </div>
    </a>

    <div id="details-{{$user->id}}"></div>

    <br>
@endforeach

<div class="row">
    <div class="col-lg-3"></div>
    <div class="col-lg-2">
        @lang('Markera alla')
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1">
        <input type="checkbox" id="selectall_level2" {{$attestlevel >= 2?"":"disabled"}} onclick="toggleattests(2)">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1">
        <input type="checkbox" id="selectall_level3" {{$attestlevel >= 3?"":"disabled"}} onclick="toggleattests(3)">
    </div>
</div>

<br>
<button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Attestera')</button>

<script type="text/javascript">

    function toggleattests(level) {
        var ca=document.getElementById("selectall_level"+level);
        var cb=document.getElementsByName("level"+level+"attest[]");
        var cb_length=cb.length;
        for(var i=0; i < cb_length; i++) {
            if(cb[i].disabled == false) {
                cb[i].checked = ca.checked;
            }
        }
        document.settings.submit.disabled = !ca.checked;
    }

{{--TODO: Behöver fixa lite mer med det här, blir inte helt rätt när man klickar i och ur igen--}}
    function togglesubmit(level) {
        var total=0;
        var cb=document.getElementsByName("level"+level+"attest[]");
        var cb_length=cb.length;
        for(var i=0; i < cb_length; i++) {
            if(cb[i].checked) {
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
