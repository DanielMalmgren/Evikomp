@extends('layouts.app')

@section('title', __('Attestera projekttid'))

@section('content')

<div class="col-md-12">

    <H1>@lang('Attestera närvaro') - {{$monthstr.' '.$year}}</H1>
    <form method="post" name="settings" action="{{action('TimeAttestLevel1Controller@store')}}" accept-charset="UTF-8">
        @csrf

        <div class="card">
            <div class="card-body">

                <table class="table table-sm table-responsive table-nonfluid">
                    <thead>
                        <tr>
                            <th scope="col">@lang('Typ av aktivitet')</th>
                            @for($day = 1; $day <= $days_in_month; $day++)
                                <th scope="col" class="initial-hide nowrap text-center">{{$day}}</th>
                            @endfor
                            <th scope="col">@lang('Timmar')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($time_rows as $title => $time_row)
                            @if($time_row != end($time_rows))
                                <tr>
                                    <td>{{$title}}</td>
                                    @for($day = 1; $day <= $days_in_month; $day++)
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
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('Summa')</td>
                            @for($day = 1; $day <= $days_in_month; $day++)
                                <td class="initial-hide nowrap"></td>
                            @endfor
                            <th class="text-center">{{end($time_rows)[32]}}</td>
                        </tr>
                    </tfoot>
                </table>
                <br>

                <input type="hidden" name="hours" value="{{end($time_rows)[32]}}">
                <input type="hidden" name="month" value="{{$month}}">
                <input type="hidden" name="year" value="{{$year}}">

                <label><input {{$already_attested?"disabled checked":""}} {{$month_is_closed?"disabled":""}} type="checkbox" name="attest" value="attest" id="attest">@lang('Jag intygar härmed att ovanstående tidsregistrering är korrekt.')</label><br>

            </div>
        </div>

        <br>

        @if($month_is_closed)
            <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Månaden är stängd för attestering')</button>
        @else
            <button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Attestera')</button>
        @endif

    </form>
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
