
@extends('layouts.app')

@section('title', __('Nyhet'))

@section('content')

    <H1>{{$announcement->heading}}</H1>

    <H2>{{$announcement->preamble}}</H2>

    {!!$announcement->bodytext!!}

@endsection
