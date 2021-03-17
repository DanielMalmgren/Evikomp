@extends('layouts.app')

@section('title', __('Skapa attest utifrån närvarolista'))

@section('content')

<div class="col-md-5 mb-3">

    <H1>@lang('Skapa attest från närvarolista')</H1>

    <form method="post" name="question" action="{{action('TimeAttestController@from_list', $project_time->id)}}" accept-charset="UTF-8">
        @method('put')
        @csrf

        <br>

        <input disabled class="form-control" value="{{$project_time->workplace->name}}">

        <br>

        <input disabled class="form-control" value="{{$project_time->project_time_type->name}}">

        <br>

        <div class="mb-3">
            <label>@lang('Datum')</label>
            <input disabled class="form-control" value="{{$project_time->date}}">
        </div>

        <div class="mb-3">
            <div class="row container">
                <div class="mb-3">
                    <label>@lang('Från')</label>
                    <input type="time" disabled class="form-control time" value="{{substr($project_time->starttime, 0, 5)}}">
                </div>
                <div class="mb-3">
                    <label>@lang('Till')</label>
                    <input type="time" disabled class="form-control time" value="{{substr($project_time->endtime, 0, 5)}}">
                </div>
            </div>
        </div>

        <label for="signing_boss">@lang('Attesterande chef')</label>
        <select class="custom-select d-block w-100" name="signing_boss">
            @foreach($project_time->workplace->workplace_admins as $admin)
                <option value="{{$admin->id}}">{{$admin->name}}</option>
            @endforeach
        </select>

        <br>

        <H2>@lang('Närvarande personer')</H2>
        @foreach($project_time->workplace->users->sortBy('name') as $user)
            <div class="checkbox">
                <label><input type="checkbox" name="users[]" {{$project_time->users->contains('id',$user->id) ? 'checked' : '' }} value="{{$user->id}}" id="{{$user->id}}">{{$user->name}}</label>
            </div>
        @endforeach

        <br>

        <button {{$project_time->time_attests->isNotEmpty()?"disabled":""}} class="btn btn-primary btn-lg btn-block" id="submit" name="submit" type="submit">@lang('Attestera')</button>
    </form>


</div>

@endsection
