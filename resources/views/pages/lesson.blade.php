@extends('layouts.app')

@section('content')

    <H1>{{$lesson->translateOrDefault(App::getLocale())->name}}</H1>

    {{$lesson->translateOrDefault(App::getLocale())->description}}

@endsection
