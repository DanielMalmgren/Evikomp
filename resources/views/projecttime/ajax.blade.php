<form method="post" name="question" action="{{action('ProjectTimeController@store', $workplace->id)}}" accept-charset="UTF-8">
    @csrf

    <input type="hidden" name="workplace_id" value="{{$workplace->id}}">

    <select class="custom-select d-block w-100" id="type" name="type" required="">
        @foreach($project_time_types as $type)
            <option value="{{$type->id}}">{{$type->name}}</option>
        @endforeach
    </select>

    <div class="mb-3">
        <label for="date">@lang('Datum')</label>
        <input type="date" name="date" class="form-control" value="{{old('date')}}">
    </div>

    <div class="mb-3">
        <div class="row container">
            <div class="mb-3">
                <label for="starttime">@lang('Från')</label>
                <input type="time" name="starttime" class="form-control" value="{{old('starttime')}}">
            </div>
            <div class="mb-3">
                <label for="endtime">@lang('Till')</label>
                <input type="time" name="endtime" class="form-control" value="{{old('endtime')}}">
            </div>
        </div>
    </div>

    <H2>@lang('Närvarande personer')</H2>
    @foreach($workplace->users->sortBy('name') as $user)
        <div class="checkbox">
            <label><input type="checkbox" name="users[]" value="{{$user->id}}" id="{{$user->id}}">{{$user->name}}</label>
        </div>
    @endforeach

    <br><br>

    <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
</form>
