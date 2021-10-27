@extends('layouts.app')

@section('title', __('Logg'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Logg')</H1>

    @forelse($logrows as $logrow)
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-2">
                {{GmtTimeToLocalTime($logrow->created_at)}}
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                @if(isset($logrow->causer_id))
                    <a href="/users/"{{$logrow->causer_id}}>{{$logrow->causer->name}}</a>
                    <a href="{{request()->fullUrlWithQuery(['user' => $logrow->causer_id])}}"><i class="fas fa-filter"></i></a>
                @endif
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2">
                @lang('log.'.$logrow->description)
                    <a href="{{request()->fullUrlWithQuery(['description' => $logrow->description])}}"><i class="fas fa-filter"></i></a>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-2">
                @if(isset($logrow->subject_id))
                    @php
                        $class = get_class($logrow->subject);
                    @endphp
                    @switch($class)
                        @case('App\Lesson')
                            <a href="/lessons/{{$logrow->subject_id}}">{{$logrow->subject->translateOrDefault(App::getLocale())->name}}</a>
                            <a href="{{request()->fullUrlWithQuery(['lesson' => $logrow->subject_id])}}"><i class="fas fa-filter"></i></a>
                            @break
                        @case('App\Track')
                            Second case...
                            @break
                        @default
                            Default case...
                    @endswitch
                @endif
            </div>
        </div>
    @empty
        @lang('Det är tomt i loggen!')<br>
    @endforelse

@endsection
