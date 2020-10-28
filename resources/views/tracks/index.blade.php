@extends('layouts.app')

@section('title', __('Spår'))

@section('content')

    <H1>@lang('Spår')</H1>
    @if($showall)
        <small><a class="black" href="/settings">@lang('Visar nu tillfälligt samtliga spår. För att lägga till spår permanent i listan, gå till dina inställningar.')</a></small>
    @else
        <small><a class="black" href="?showall=1">@lang('Visar endast dina valda spår. Klicka här för att visa samtliga spår.')</a></small>
    @endif

    @if(count($tracks) > 0)
        <ul class="list-group mb-3 tracks" id="tracks">
            @foreach($tracks as $track)
                <li class="list-group-item d-flex justify-content-between lh-condensed nopadding" style="margin-top:7px;border-style:solid;border-width:3px;border-color:{{$track->color->hex}}" id="id-{{$track->id}}">
                    <a href="/tracks/{{$track->id}}">
                        <h6 class="my-0">
                            @if(isset($track->icon) && $track->icon != '')
                                <img class="lessonimage" src="/storage/icons/{{$track->icon}}" style="max-width:30px;margin-right:10px">
                            @endif
                            {{$track->translateOrDefault(App::getLocale())->name}}
                            @if($track->active == 0)
                                - inaktiv
                            @endif
                        </h6>
                        <small class="text-muted">{{$track->translateOrDefault(App::getLocale())->subtitle}}</small>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <br>
        @lang('Du har inga spår valda. Gå till dina inställningar för att välja vilka spår som ska visas här!')
    @endif

    @can('manage tracks')
        @if($showall)
            <script type="text/javascript" language="javascript" src="{{asset('vendor/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
            <script type="text/javascript">
                $(function() {
                    $("#tracks").sortable({
                    update: function (e, u) {
                        var token = "{{ csrf_token() }}";
                        var data = $(this).sortable('serialize');
                        $.ajax({
                            url: '/tracks/reorder',
                            data : {_token:token,data:data},
                            type: 'POST'
                        });
                    }
                    });
                });
            </script>
        @endif

        <a href="/tracks/create" class="btn btn-primary">@lang('Lägg till spår')</a>
    @endcan

@endsection
