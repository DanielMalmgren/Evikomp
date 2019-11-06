@extends('layouts.app')

@section('title', __('Statistik'))

@section('content')

<p>@lang('Antal personer inloggade idag: ') {{$sessions}}</p>
<p>@lang('Antal registrerade användare: ') {{$users}}</p>
<p>@lang('Antal arbetsplatser: ') {{$workplaces}}</p>
<p>@lang('Antal upplagda lektioner: ') {{$lessons}}</p>
<p>@lang('Antal timmar i plattformen hittills: ') {{$totalactivehours}}</p>
<p>@lang('Antal timmar manuellt registrerade hittills: ') {{$totalprojecthours}}</p>
<p>@lang('Antal timmar attesterade av deltagare hittills: ') {{$attestedhourslevel1}}</p>
<p>@lang('Antal timmar attesterade av arbetsplatskoordinatorer hittills: ') {{$attestedhourslevel2}}</p>
<p>@lang('Antal timmar attesterade av chefer hittills: ') {{$attestedhourslevel3}}</p>

{!! $chart->container() !!}

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
{{--<script src="/js/highcharts.js"></script>--}}
{!! $chart->script() !!}


@endsection
