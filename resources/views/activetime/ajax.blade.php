Lite utkast till hur det skulle kunna se ut om vi fimpar Excel-varianten och istället har helt digital attestering av tiden:<br><br>

<div class="card">
    <div class="card-body">

        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th scope="col">@lang('Typ av aktivitet')</th>
                    @for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
                        <th scope="col">{{$day}}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($time_rows as $time_row)
                    <tr>
                        @for($day = 0; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
                            @if(isset($time_row[$day]))
                                <td>{{$time_row[$day]}}</td>
                            @else
                                <td></td>
                            @endif
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
<br>

Nedanstående checkboxar ska förstås bara vara klickbara av person med rätt roll och de ska heller inte gå att klicka ur när man väl har godkänt.<br>
  <label><input type="checkbox" name="level1attest" value="level1attest" id="level1attest">Jag (projektdeltagare) intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
  <label><input type="checkbox" name="level1attest" value="level1attest" id="level1attest">Jag (Arbetsplatskoordinator) intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
  <label><input type="checkbox" name="level1attest" value="level1attest" id="level1attest">Jag (Chef) intygar härmed att ovanstående tidsregistrering är korrekt.</label><br>
(Fast koordinator och chef kanske ska se mer på en översiktlig nivå för hela arbetsplatsen och attestera alla på en gång?)<br><br>

<a href="#" class="btn btn-primary">Signera</a>
