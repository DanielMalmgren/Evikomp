<div class="card">
        <div class="card-body">

        Lite utkast till hur det skulle kunna se ut om vi fimpar Excel-varianten och istället har helt digital attestering av tiden:<br><br>

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
                @foreach($time_rows as $time_row)
                    <tr>
                        <td>{{$time_row[0]}}</td>
                        @for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
                            @if(isset($time_row[$day]))
                                <td class="initial-hide nowrap text-center">{{$time_row[$day]}}</td>
                            @else
                                <td class="initial-hide nowrap"></td>
                            @endif
                        @endfor
                        <td class="text-center">{{$time_row[32]}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>@lang('Summa')</td>
                    @for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
                        <td class="initial-hide nowrap"></td>
                    @endfor
                    <th class="text-center">{{$monthtotal}}</td>
                </tr>
            </tfoot>
        </table>
        <br>

        Nedanstående checkboxar ska förstås bara vara klickbara av person med rätt roll och de ska heller inte gå att klicka ur när man väl har godkänt.<br>
        <label><input type="checkbox" name="level1attest" value="level1attest" id="level1attest">Jag (projektdeltagare) intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
        <label><input type="checkbox" name="level1attest" value="level1attest" id="level1attest">Jag (Arbetsplatskoordinator) intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
        <label><input type="checkbox" name="level1attest" value="level1attest" id="level1attest">Jag (Chef) intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
        (Fast koordinator och chef kanske ska se mer på en översiktlig nivå för hela arbetsplatsen och attestera alla på en gång?)<br><br>

    </div>
</div>

<a href="#" class="btn btn-primary">Signera</a>

<script type="text/javascript">
    $(function() {
        $boolean = false;

        $("table").click(function() {
            if ($boolean) {
                $boolean = false;
                $(".initial-hide").animate(
                    {
                    'width':'0px',
                    'max-width':'0px'
                    },
                    "slow",
                    function() {
                        $(".initial-hide").removeClass("visible-state");
                    }
                );
            } else {
                $boolean = true;
                $(".initial-hide").addClass("visible-state");
                $(".initial-hide").animate(
                    {
                        'width':'25px',
                        'max-width':'25px'
                    },
                    "slow"
                );
            }
        });
    });
</script>
