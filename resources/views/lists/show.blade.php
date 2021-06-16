@extends('layouts.app')

@section('title', $list->name)

@section('content')

    <H1>
        {{$list->name}}
    </H1>

    @if(count($lessons) > 0)
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <div class="list-group mb-4 lessonslist" id="lessonslist">
            @foreach($lessons as $lesson)
                @include('inc.listlesson')
            @endforeach
        </div>
    @endif

    @if($can_edit)
        <a href="/lists/{{$list->id}}/edit" class="btn btn-secondary">@lang('Redigera denna lista')</a>
    @endif

@endsection
