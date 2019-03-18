<div class="card">
    <div class="card-body">

        <table class="table table-sm table-responsive table-nonfluid">
            <thead>
                <tr>
                    <th scope="col">@lang('Typ av aktivitet')</th>
                    @for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
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
                            @for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
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
                    @for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
                        <td class="initial-hide nowrap"></td>
                    @endfor
                    <th class="text-center">{{end($time_rows)[32]}}</td>
                </tr>
            </tfoot>
        </table>
        <br>

        <input type="hidden" name="hours" value="{{end($time_rows)[32]}}">

        @if(Auth::user()->time_attests->where('attestlevel', 1)->where('month', $month)->where('year', $year)->count() > 0)
            <label><input disabled checked type="checkbox" name="attest" value="attest" id="attest">Jag intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
        @else
            <label><input type="checkbox" name="attest" value="attest" id="attest">Jag intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
        @endif

    </div>
</div>

<br>

{{--<a href="#" class="btn btn-primary">@lang('Attestera')</a>--}}

<button class="btn btn-primary btn-lg btn-block" disabled id="submit" name="submit" type="submit">@lang('Attestera')</button>

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
