@extends('layouts.app')

@section('title', __('Statistik'))

@section('content')

<p>@lang('Antal personer inloggade idag: ') {{$sessions}}</p>
<p>@lang('Antal registrerade användare: ') {{$users}}</p>
<p>@lang('Antal arbetsplatser: ') {{$workplaces}}</p>
<p>@lang('Antal upplagda lektioner: ') {{$lessons}}</p>

{{--
{!! $chart->container() !!}

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>
{!! $chart->script() !!}
--}}

@endsection
