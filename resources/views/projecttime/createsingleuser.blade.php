@extends('layouts.app')

@section('title', __('Registrera projekttid'))

@section('content')

<div class="col-md-5 mb-3">

    <H1>@lang('Registrera projekttid')</H1>

    <form method="post" name="question" action="{{action('ProjectTimeController@store', $workplace->id)}}" accept-charset="UTF-8">
        @csrf

        <input type="hidden" name="workplace_id" value="{{$workplace->id}}">

        <select class="custom-select d-block w-100" id="type" name="type" required="">
            @foreach($project_time_types as $type)
                <option value="{{$type->id}}">{{$type->name}}</option>
            @endforeach
        </select>

        <br>

        <div class="mb-3">
            <label for="date">@lang('Datum')</label>
            <input type="date" name="date" min="{{$mindate}}" max="{{date("Y-m-d")}}" class="form-control" value="{{old('date')}}">
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

        <input type="hidden" name="users[]" value="{{$user->id}}" id="{{$user->id}}">

        <br>

        <button class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Spara')</button>
    </form>


</div>

@endsection
