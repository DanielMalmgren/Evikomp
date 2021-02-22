@extends('layouts.app')

@section('title', __('Attestera projekttid'))

@section('content')

<div class="col-md-12">

    <H1>@lang('Attestera närvaro')</H1>
    @if (session()->has('authnissuer'))
        <form method="post" name="settings" action="{{action('TimeAttestLevel1Controller@store')}}" accept-charset="UTF-8">
            @csrf

            <div class="card">
                <div class="card-body">

                    <h2>{{$prev_month_str.' '.$prev_month_year}}</h2>
                    <table class="table table-sm table-responsive table-nonfluid">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Typ av aktivitet')</th>
                                @for($day = 1; $day <= $days_in_prev_month; $day++)
                                    <th scope="col" class="initial-hide nowrap text-center">{{$day}}</th>
                                @endfor
                                <th scope="col">@lang('Timmar')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prev_month_time_rows as $title => $time_row)
                                @if($time_row != end($prev_month_time_rows))
                                    <tr>
                                        <td>{{$title}}</td>
                                        @for($day = 1; $day <= $days_in_prev_month; $day++)
                                            @if(isset($time_row[$day]))
                                                <td class="initial-hide nowrap text-center">{{$time_row[$day]}}</td>
                                            @else
                                                <td class="initial-hide nowrap"></td>
                                            @endif
                                        @endfor
                                        <td class="text-center">{{$time_row[32]}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr style="font-weight:bold">
                                <td>@lang('Summa')</td>
                                @for($day = 1; $day <= $days_in_prev_month; $day++)
                                    <td class="initial-hide nowrap"></td>
                                @endfor
                                <td class="text-center">{{end($prev_month_time_rows)[32]}}</td>
                            </tr>
                            <tr>
                                <td>@lang('Tidigare attesterat')</td>
                                @for($day = 1; $day <= $days_in_prev_month; $day++)
                                    <td class="initial-hide nowrap"></td>
                                @endfor
                                <td class="text-center">{{$attested_prev_month}}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>@lang('Att attestera')</td>
                                @for($day = 1; $day <= $days_in_prev_month; $day++)
                                    <td class="initial-hide nowrap"></td>
                                @endfor
                                <th class="text-center">{{end($prev_month_time_rows)[32]-$attested_prev_month}}</td>
                            </tr>
                        </tfoot>
                    </table>
                    <br>

                    <h2>{{$this_month_str.' '.$this_month_year}}</h2>
                    <table class="table table-sm table-responsive table-nonfluid">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Typ av aktivitet')</th>
                                @for($day = 1; $day <= $days_in_this_month; $day++)
                                    <th scope="col" class="initial-hide nowrap text-center">{{$day}}</th>
                                @endfor
                                <th scope="col">@lang('Timmar')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this_month_time_rows as $title => $time_row)
                                @if($time_row != end($this_month_time_rows))
                                    <tr>
                                        <td>{{$title}}</td>
                                        @for($day = 1; $day <= $days_in_this_month; $day++)
                                            @if(isset($time_row[$day]))
                                                <td class="initial-hide nowrap text-center">{{$time_row[$day]}}</td>
                                            @else
                                                <td class="initial-hide nowrap"></td>
                                            @endif
                                        @endfor
                                        <td class="text-center">{{$time_row[32]}}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr style="font-weight:bold">
                                <td>@lang('Summa')</td>
                                @for($day = 1; $day <= $days_in_this_month; $day++)
                                    <td class="initial-hide nowrap"></td>
                                @endfor
                                <td class="text-center">{{end($this_month_time_rows)[32]}}</td>
                            </tr>
                            <tr>
                                <td>@lang('Tidigare attesterat')</td>
                                @for($day = 1; $day <= $days_in_this_month; $day++)
                                    <td class="initial-hide nowrap"></td>
                                @endfor
                                <td class="text-center">{{$attested_this_month}}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>@lang('Att attestera')</td>
                                @for($day = 1; $day <= $days_in_this_month; $day++)
                                    <td class="initial-hide nowrap"></td>
                                @endfor
                                <th class="text-center">{{end($this_month_time_rows)[32]-$attested_this_month}}</td>
                            </tr>
                        </tfoot>
                    </table>

                    <input type="hidden" name="prev_month_hours" value="{{end($prev_month_time_rows)[32]-$attested_prev_month}}">
                    <input type="hidden" name="prev_month" value="{{$prev_month}}">
                    <input type="hidden" name="prev_month_year" value="{{$prev_month_year}}">

                    <input type="hidden" name="this_month_hours" value="{{end($this_month_time_rows)[32]-$attested_this_month}}">
                    <input type="hidden" name="this_month" value="{{$this_month}}">
                    <input type="hidden" name="this_month_year" value="{{$this_month_year}}">

                    <label><input {{$already_fully_attested?"disabled checked":""}} type="checkbox" name="attest" value="attest" id="attest">@lang('Jag intygar härmed att ovanstående tidsregistrering är korrekt.')</label><br>

                </div>
            </div>

            <br>

            {{--@if($month_is_closed)
                <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Månaden är stängd för attestering')</button>
            @else--}}
                <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Attestera')</button>
            {{--@endif--}}

        </form>
    @else
        @lang('Du är inte inloggad med e-legitimation och kan därför inte attestera din tid digitalt.<br> Logga in med e-legitimation eller klicka nedan för att attestera din tid via papper.')
        <br><br>
        <a href="/manualattestxls" class="btn btn-primary">@lang('Hämta tid som Excel-fil för utskrift')</a>
    @endif
</div>

<script type="text/javascript">
    $(function() {
        $expanded = false;
        $("table").click(function() {
            if ($expanded) {
                $expanded = false;
                $(".initial-hide").animate(
                    {
                    'width':'0px',
                    'min-width':'0px',
                    'max-width':'0px'
                    },
                    "slow",
                    function() {
                        $(".initial-hide").removeClass("visible-state");
                    }
                );
            } else {
                $expanded = true;
                $(".initial-hide").addClass("visible-state");
                $(".initial-hide").animate(
                    {
                        'width':'27px',
                        'min-width':'27px',
                        'max-width':'27px'
                    },
                    "slow"
                );
            }
        });

        $("#attest").change(function() {
            if(this.checked){
                document.settings.submit.disabled = false;
            } else {
                document.settings.submit.disabled = true;
            }
        });
    });
</script>

@endsection
