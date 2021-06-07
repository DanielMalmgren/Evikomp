@extends('layouts.app')

@section('title', __('Listor'))

@section('content')

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <H1>@lang('Mina listor')</H1>

    @forelse($my_lists as $list)
        <a class="list-group-item list-group-item-action" onClick="window.location='/lists/{{$list->id}}'">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    {{$list->name}}
                </div>
                <div class="col-lg-3 col-md-3 col-sm-2">
                    {{$list->lessons->count()}}@lang(' moduler')
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1">
                    <i class="fas fa-copy" data-toggle="tooltip" title="@lang('Kopiera')" onClick="window.location='/lists/{{$list->id}}/replicate'"></i>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1">
                    <i class="fas fa-edit" data-toggle="tooltip" title="@lang('Redigera')" onClick="window.event.stopPropagation();window.event.cancelBubble=true;window.location='/lists/{{$list->id}}/edit';"></i>
                </div>
            </div>
        </a>
    @empty
        @lang('Du har inte skapat några listor än, klicka på "Skapa ny lista" för att skapa din första lista!')<br>
    @endforelse

    <br>

    <a href="/lists/create" class="btn btn-primary">@lang('Skapa ny lista')</a>

    <br><br>

    <H1>@lang('Listor delade med mig')</H1>

    @if($shared_lists->isEmpty())
        @lang("Ingen har delat någon lista med dig")
    @else
        @foreach($shared_lists as $list)
            <a class="list-group-item list-group-item-action" onClick="window.location='/lists/{{$list->id}}'">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        {{$list->name}}
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-2">
                        {{$list->user->name}}
                    </div>
                </div>
            </a>
        @endforeach
    @endif

@endsection
