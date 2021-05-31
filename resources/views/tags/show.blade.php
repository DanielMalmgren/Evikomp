@extends('layouts.app')

@section('title', '#'.$tag)

@section('content')

    <H1>
        {{'#'.$tag}}
    </H1>

    @if(count($lessons) > 0)
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <div class="list-group mb-4 lessonslist" id="lessonslist">
            @foreach($lessons as $lesson)
                @include('inc.listlesson')
            @endforeach
        </div>
    @endif

@endsection
