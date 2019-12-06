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
<p>@lang('Antal timmar attesterade av chefer hittills: ') {{$attestedhourslevel3}} ({{round($attestedhourslevel3/100, 1)}} @lang('procent av') 10 000)</p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/js/bootstrap.bundle.min.js"></script>

<ul class="nav nav-tabs tabs-up" id="charts">
    <li><a href="/statistics/ajaxchart/1" data-target="#chart1" class="media_node active span" id="charttab1" data-toggle="tabajax" rel="tooltip"> Chart 1 </a></li>
    <li><a href="/statistics/ajaxchart/2" data-target="#chart2" class="media_node span" id="charttab2" data-toggle="tabajax" rel="tooltip"> Chart 2 </a></li>
    <li><a href="/statistics/ajaxchart/3" data-target="#chart3" class="media_node span" id="charttab3" data-toggle="tabajax" rel="tooltip"> Chart 3 </a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="chart1"></div>
    <div class="tab-pane" id="chart2"></div>
    <div class="tab-pane" id="chart3"></div>
</div>

<script type="text/javascript">
    $('[data-toggle="tabajax"]').click(function(e) {
        var $this = $(this),
            loadurl = $this.attr('href'),
            targ = $this.attr('data-target');

        $.get(loadurl, function(data) {
            $(targ).html(data);
        });

        $this.tab('show');
        return false;
    });
</script>

@endsection
