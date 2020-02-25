<div class="card">
    <div class="card-body">

        @lang('Sammanställning över '){{$monthstr.' '.$year}}<br>

        <table class="table">
            <thead>
              <tr>
                <th scope="col">@lang('Arbetsplats')</th>
                <th scope="col">@lang('Upparbetad tid')</th>
                <th scope="col">@lang('Attesterad tid')</th>
              </tr>
            </thead>
            <tbody>
            @foreach($workplaces->sortBy('name') as $workplace)
                @php
                    $pt = round($workplace->project_times->where('month', $month)->where('year', $year)->sum('minutes_total')/60, 1);
                    $at = round($workplace->month_active_time($month, $year)/60, 1);
                    $attested = $workplace->month_attested_time($month, $year, 3)+$workplace->month_attested_time($month, $year, 0);
                    if($pt+$at-$attested > 10) {
                        $tableclass = "table-danger";
                    } elseif($pt+$at-$attested > 2) {
                        $tableclass = "table-warning";
                    } elseif ($pt+$at > 0) {
                        $tableclass = "table-success";
                    } else {
                        $tableclass = "";
                    }
                @endphp
                <tr class="{{$tableclass}}" onclick="togglewpdetails({{$workplace->id}})"> {{--If generated time differs more than one hour from attested time something is wrong--}}
                    <th scope="row">{{$workplace->name}} ({{$workplace->municipality->name}})</th>
                    <td>{{$pt}} + {{$at}}</td>
                    <td>
                        {{$workplace->month_attested_time($month, $year, 1)}} <i class="fas fa-arrow-right"></i>
                        {{$workplace->month_attested_time($month, $year, 2)}} <i class="fas fa-arrow-right"></i>
                        {{$workplace->month_attested_time($month, $year, 3)}}
                        @if($workplace->month_attested_time($month, $year, 0) > 0)
                            ({{$workplace->month_attested_time($month, $year, 0)}})
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="nopadding {{$tableclass}}">
                        <div id="details-{{$workplace->id}}" class="bg-transparent"></div>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Totalt</th>
                    <th>{{$projecthours}}</th>
                    <th>{{$attestedhourslevel1}} <i class="fas fa-arrow-right"></i> {{$attestedhourslevel2}} <i class="fas fa-arrow-right"></i> {{$attestedhourslevel3}}</th>
                </tr>
            </tfoot>
          </table>

    </div>
</div>

<br>

<div class="mb-3">
    <label><input type="checkbox" {{$month_closed?'checked disabled':''}}  name="close_month">@lang('Stäng månad för attestering')</label>
</div>

<script type="text/javascript">

    function togglewpdetails(workplace_id) {
        if($("#details-"+workplace_id).is(':empty')) {
            $("#details-"+workplace_id).load("/timesummarywpdetails/"+workplace_id+"/{{$year}}/{{$month}}");
        } else {
            $("#details-"+workplace_id).empty();
        }
    }

</script>
