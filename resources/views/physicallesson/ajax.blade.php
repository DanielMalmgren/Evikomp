<form method="post" name="question" action="{{action('PhysicalLessonController@store')}}" accept-charset="UTF-8">
    @csrf

    <input type="hidden" name="workplace_id" value="{{$workplace->id}}">

    <div class="mb-3">
        <label for="time">@lang('Tid')</label>
        <input type="time" name="time" class="form-control" id="time">
    </div>

    <H2>Närvarande personer</H2>
    @foreach($workplace->users->sortBy('name') as $user)
        <div class="checkbox">
            <label><input type="checkbox" name="users[]" value="{{$user->id}}" id="{{$user->id}}">{{$user->name}}</label>
        </div>
    @endforeach

    <br><br>

    <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
</form>
