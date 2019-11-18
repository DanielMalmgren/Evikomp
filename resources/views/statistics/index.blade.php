@extends('layouts.app')

@section('title', __('Statistik'))

@section('content')

<H1>@lang('Statistik')</H1>
<p>@lang('Antal personer inloggade idag: ') {{$sessions}}</p>
<p>@lang('Antal registrerade användare: ') {{$users}}</p>
<p>@lang('Antal arbetsplatser: ') {{$workplaces}}</p>
<p>@lang('Antal upplagda lektioner: ') {{$lessons}}</p>
<p>@lang('Antal timmar i plattformen hittills: ') {{$totalactivehours}}</p>
<p>@lang('Antal timmar manuellt registrerade hittills: ') {{$totalprojecthours}}</p>
<p>@lang('Antal timmar attesterade av deltagare hittills: ') {{$attestedhourslevel1}}</p>
<p>@lang('Antal timmar attesterade av arbetsplatskoordinatorer hittills: ') {{$attestedhourslevel2}}</p>
<p>@lang('Antal timmar attesterade av chefer hittills: ') {{$attestedhourslevel3}} ({{round($attestedhourslevel3/100, 1)}} @lang('procent av') 10 000)</p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

<br>
<h2>@lang('Inloggningar senaste två veckorna')</h2>
<div>
    {!! $loginshistorychart->container() !!}
</div>
{!! $loginshistorychart->script() !!}

<br>
<h2>@lang('Totalt slutattesterad tid per arbetsplats')</h2>
<div>
    {!! $timeperworkplacechart->container() !!}
</div>
{!! $timeperworkplacechart->script() !!}

@endsection
