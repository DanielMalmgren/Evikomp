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
    });

    function toggleall() {
        var ca=document.getElementById("togglecb");
        var cb=document.getElementsByName("users[]");
        var cb_length=cb.length;
        for(var i=0; i < cb_length; i++) {
            cb[i].checked = ca.checked;
        }
    }

</script>

<form method="post" name="question" action="{{action('ProjectTimeController@store')}}" accept-charset="UTF-8">
    @csrf

    <input type="hidden" name="workplace_id" value="{{$workplace->id}}">
    <input type="hidden" name="return_url" value="/projecttime/create">

    <select class="custom-select d-block w-100" id="type" name="type" required="">
        @foreach($project_time_types as $type)
            <option value="{{$type->id}}">{{$type->name}}</option>
        @endforeach
    </select>

    <div class="mb-3">
        <label for="date">@lang('Datum')</label>
        <input type="date" name="date" min="{{$mindate}}" max="{{date("Y-m-d")}}" class="form-control" value="{{old('date')}}">
    </div>

    <div class="mb-3">
        <div class="row container">
            <div class="mb-3">
                <label for="starttime">@lang('Från')</label>
                <input type="time" name="starttime" class="form-control time" value="{{old('starttime')}}">
            </div>
            <div class="mb-3">
                <label for="endtime">@lang('Till')</label>
                <input type="time" name="endtime" class="form-control time" value="{{old('endtime')}}">
            </div>
        </div>
    </div>

    <H2>@lang('Närvarande personer')</H2>
    <label><input type="checkbox" id="togglecb" onclick="toggleall()">@lang('Markera alla')</label>
    @foreach($workplace->users->sortBy('name') as $user)
        <div class="checkbox">
            <label><input type="checkbox" name="users[]" {{(is_array(old('users')) && in_array($user->id, old('users'))) ? 'checked' : '' }} value="{{$user->id}}" id="{{$user->id}}">{{$user->name}}</label>
        </div>
    @endforeach

    <br><br>

    <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
</form>
