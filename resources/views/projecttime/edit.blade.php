@extends('layouts.app')

@section('title', __('Ändra projekttid'))

@section('content')

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
                alert('Tidpunkterna måste vara i formatet hh:mm!');
                $(this).val('');
            }
            if(selectedValue.substring(0,2) == '00') {
                if(!confirm('Den tid du försöker ange är mitt i natten.\nÄr du säker på att det är detta du vill?')) {
                    $(this).val('');
                }
            }
        });
    });
</script>

<div class="col-md-5 mb-3">

    <H1>@lang('Ändra projekttid')</H1>

    <form method="post" name="question" action="{{action('ProjectTimeController@update', $project_time->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <input type="hidden" name="workplace_id" value="{{$workplace->id}}">
        <select class="custom-select d-block w-100" id="type" name="type" required="">
            @foreach($project_time_types as $type)
                <option {{$type->id==$project_time->project_time_type_id?"selected":""}} value="{{$type->id}}">{{$type->name}}</option>
            @endforeach
        </select>

        <br>

        <div class="mb-3">
            <label for="date">@lang('Datum')</label>
            <input type="date" name="date" min="{{$mindate}}" max="{{date("Y-m-d")}}" class="form-control" value="{{$project_time->date}}">
        </div>

        <div class="mb-3">
            <div class="row container">
                <div class="mb-3">
                    <label for="starttime">@lang('Från')</label>
                    <input type="time" name="starttime" class="form-control time" value="{{substr($project_time->starttime, 0, 5)}}">
                </div>
                <div class="mb-3">
                    <label for="endtime">@lang('Till')</label>
                    <input type="time" name="endtime" class="form-control time" value="{{substr($project_time->endtime, 0, 5)}}">
                </div>
            </div>
        </div>

        @if($project_time->workplace->workplace_admins->contains('id', Auth::user()->id) || $user->hasRole('Admin'))
            <H2>@lang('Närvarande personer')</H2>
            @foreach($workplace->users->sortBy('name') as $user)
                <div class="checkbox">
                    <label><input type="checkbox" name="users[]" {{$project_time->users->contains('id',$user->id) ? 'checked' : '' }} value="{{$user->id}}" id="{{$user->id}}">{{$user->name}}</label>
                </div>
            @endforeach
        @else
            <input type="hidden" name="users[]" value="{{$user->id}}" id="{{$user->id}}">
        @endif

        <br>

        <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
    </form>


</div>

@endsection
