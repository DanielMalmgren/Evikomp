@extends('layouts.app')

@section('title', __('Logg'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Logg')</H1>

    <table class="table table-striped">
        <tbody>

            @forelse($logrows as $logrow)
                <tr>
                    <td style="max-width:200px">
                        {{GmtTimeToLocalTime($logrow->created_at)}}
                    </td>
                    <td>
                        @if(isset($logrow->causer_id))
                            <a href="/users/{{$logrow->causer_id}}">{{$logrow->causer->name}}</a>
                            <a href="{{request()->fullUrlWithQuery(['user' => $logrow->causer_id, 'page' => null])}}"><i class="fas fa-filter"></i></a>
                        @endif
                    </td>
                    <td style="max-width:100px">
                        @lang('log.'.$logrow->description)
                            <a href="{{request()->fullUrlWithQuery(['description' => $logrow->description, 'page' => null])}}"><i class="fas fa-filter"></i></a>
                    </td>
                    <td>
                        @if(isset($logrow->subject_id))
                            @if($logrow->subject)
                                @if($logrow->subject instanceof \App\Interfaces\ModelInfo)
                                    @if($logrow->subject->hasUrl())
                                        <a href="{{$logrow->subject->modelUrl()}}">{{$logrow->subject->modelName()}}</a>
                                    @else
                                        {{$logrow->subject->modelName()}}
                                    @endif
                                    <a href="{{request()->fullUrlWithQuery(['subject_id' => $logrow->subject_id, 'subject_type' => $logrow->subject_type, 'page' => null])}}"><i class="fas fa-filter"></i></a>
                                @else
                                    Okänd objektstyp!
                                @endif
                            @else
                                @lang('Objektet har tagits bort')
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                @lang('Det finns ingenting i loggen för detta urval!')<br>
            @endforelse

        </tbody>
    </table>

    <br>
    <a href="{{$logrows->previousPageUrl()}}" class="btn btn-secondary"><span class="fa fa-chevron-left"></span></a>
    <a href="{{$logrows->nextPageUrl()}}" class="btn btn-secondary"><span class="fa fa-chevron-right"></span></a>

    @if($filtered)
        <a href="/log" class="btn btn-secondary">@lang('Rensa filter')</a>
    @endif

@endsection
