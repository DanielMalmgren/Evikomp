@extends('layouts.pdfapp')

@section('title', 'Evikomp närvarolista')

@section('content')

    <style>
        @page { margin: 100px 25px; }
        header { position: fixed; top: -60px; left: 0px; right: 0px; height: 100px; }
        footer { position: fixed; bottom: -60px; left: 0px; right: 0px; height: 100px; }
        .linerow {height: 30px;border-spacing: 0 1em;} 
        .nounderscore {border-bottom: 0px;float:left;white-space:nowrap;}
        .underscore {border-bottom: 1px solid #000; overflow: hidden;}
    </style>

    <header>
        <img height="40" src="{{public_path(env('HEADER_LOGO'))}}">
    </header>

    <footer>
        <img height="100" style="float:right" src="{{public_path('/images/EU_logga.png')}}">
    </footer>

    <H1>@lang('Evikomp närvarolista')</H1>

    @lang('Kommun: ') {{$project_time->workplace->municipality->name}}<br>
    @lang('Arbetsplats: ') {{$project_time->workplace->name}}<br>
    @lang('Aktivitet: ') {{$project_time->project_time_type->name}}<br>
    @lang('Datum: ') {{$project_time->date}}<br>
    @lang('Starttid: ') {{$project_time->starttime}}<br>
    @lang('Sluttid: ') {{$project_time->endtime}}<br>

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
            <td class="underscore" width="45%"></td>
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
