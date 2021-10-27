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
            <div class="col-lg-6 col-md-6 col-sm-6">
                @if(isset($logrow->subject_id))
                    @php
                        if($logrow->subject)
                            $class = get_class($logrow->subject);
                        else
                            $class = 'deleted';
                    @endphp
                    @switch($class)
                        @case('App\Lesson')
                            <a href="/lessons/{{$logrow->subject_id}}">{{$logrow->subject->translateOrDefault(App::getLocale())->name}}</a>
                            <a href="{{request()->fullUrlWithQuery(['subject_id' => $logrow->subject_id, 'subject_type' => $logrow->subject_type])}}"><i class="fas fa-filter"></i></a>
                            @break
                        @case('App\Track')
                            <a href="/tracks/{{$logrow->subject_id}}">{{$logrow->subject->translateOrDefault(App::getLocale())->name}}</a>
                            <a href="{{request()->fullUrlWithQuery(['subject_id' => $logrow->subject_id, 'subject_type' => $logrow->subject_type])}}"><i class="fas fa-filter"></i></a>
                            @break
                        @case('App\Workplace')
                            {{$logrow->subject->name}}
                            <a href="{{request()->fullUrlWithQuery(['subject_id' => $logrow->subject_id, 'subject_type' => $logrow->subject_type])}}"><i class="fas fa-filter"></i></a>
                            @break
                        @case('deleted')
                            @lang('Objektet har tagits bort')
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
