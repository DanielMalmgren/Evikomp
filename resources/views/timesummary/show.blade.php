@extends('layouts.app')

@section('title', __('Sammanställning till ESF'))

@section('content')

    <div class="col-md-5 mb-3">

        <H1>@lang('Sammanställning till ESF')</H1>
        <form method="get" name="settings" action="{{action('TimeSummaryController@export')}}" accept-charset="UTF-8">
            @csrf

            <div class="mb-3">
                <label for="year">@lang('År')</label>
                <select class="custom-select d-block w-100" id="year" name="year" required="">
                    <option value="{{date('Y')-1}}">{{date('Y')-1}}</option>
                    <option selected value="{{date('Y')}}">{{date('Y')}}</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="month">@lang('Månad')</label>
                <select class="custom-select d-block w-100" id="month" name="month" required="">
                    @for ($i = 1; $i < 12; $i++)
                        <option value="{{$i}}" {{$i==date('n')-1?"selected":""}}>{{strftime('%B', strtotime('2000-'.$i.'-15'))}}</option>
                    @endfor
                </select>
            </div>

            <br>
            <button class="btn btn-primary btn-lg btn-block" name="submit" type="submit">@lang('Hämta sammanställningen')</button>
        </form>
    </div>
@endsection
