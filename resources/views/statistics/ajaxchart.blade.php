<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js" charset="utf-8"></script>

<h2>{{$heading}}</h2>
<div>
    {!! $chart->container() !!}
</div>
{!! $chart->script() !!}
