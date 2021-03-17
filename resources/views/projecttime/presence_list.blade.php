@extends('layouts.pdfapp')

@section('title', 'Evikomp närvarolista')

@section('content')

    <style>
        @page { margin: 150px 25px; }
        header { position: fixed; top: -110px; left: 0px; right: 0px; height: 100px; }
        footer { position: fixed; bottom: -110px; left: 0px; right: 0px; height: 100px; }
        h1 { margin-top: -20px;}
        p.font-weight-bold {font-weight: bold;}
        .linerow {height: 30px;border-spacing: 0 1em;} 
        .nounderscore {border-bottom: 0px;float:left;white-space:nowrap;}
        .underscore {border-bottom: 1px solid #000; overflow: hidden;}
    </style>

    <header>
        <img height="40" src="{{public_path(env('HEADER_LOGO'))}}">
    </header>

    <footer>
        <img height="100" style="float:right" src="{{public_path('/images/EU_logga.png')}}">
        <p style="position:absolute;bottom:10;">id: {{$project_time->id}}</p>
    </footer>

    <H1>@lang('Evikomp närvarolista')</H1>

    <p class="font-weight-bold">
        {{$project_time->date}}
        {{\Carbon\Carbon::createFromFormat('H:i:s',$project_time->starttime)->format('h:i')}} -
        {{\Carbon\Carbon::createFromFormat('H:i:s',$project_time->endtime)->format('h:i')}}
    </p>
    @lang('Projekt: ') Evikomp 2.0 (2020/00088)<br>
    @lang('Kommun: ') {{$project_time->workplace->municipality->name}} ({{$project_time->workplace->municipality->orgnummer}})<br>
    @lang('Arbetsplats: ') {{$project_time->workplace->name}}<br>
    @lang('Aktivitet: ') {{$project_time->project_time_type->name}}<br>

    <br>

    <H2>@lang('Närvarande')</H2>
    <br>
    @foreach($project_time->users as $user)
        <table width="100%">
            <tr width="100%" class="linerow">
                <td class="nounderscore">{{$user->name}} {{$user->personid}}</td>
                <td class="underscore" width="99%"></td>
            </tr>
        </table>
        <br><br>
    @endforeach

    <H2>@lang('Chef/arbetsplatskoordinator')</H2>
    <br><br>
    <table width="100%">
        <tr width="100%" class="linerow">
            <td class="underscore" width="45%"></td>
            <td class="nounderscore" width="20px"></td>
            @if($signing_boss)
                <td class="nounderscore" width="45%">{{$signing_boss->name}}</td>
            @else
                <td class="underscore" width="45%"></td>
            @endif
        </tr>
        <tr width="100%" class="linerow">
            <td class="nounderscore" width="45%">@lang('Underskrift')</td>
            <td class="nounderscore" width="50px"></td>
            <td class="nounderscore" width="45%">@lang('Namnförtydligande')</td>
        </tr>
    </table>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "@lang('sida') {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>

@endsection
