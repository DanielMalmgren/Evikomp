@extends('layouts.app')

@section('title', __('Skapa lista'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

<div class="col-md-8">

    <H1>@lang('Skapa lista')</H1>

    <form method="post" action="{{action('ListController@store')}}" accept-charset="UTF-8">
        @csrf

        <div class="mb-5">
            <label for="name">@lang('Namn')</label>
            <input name="name" required class="form-control">
        </div>

        This page is not finished yet!

        <br><br>

        <button class="btn btn-primary btn-lg btn-block" type="submit">@lang('Spara')</button>
    </form>
</div>

@endsection
