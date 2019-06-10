<div class="card">
    <div class="card-body">

        Sammanställning över {{$monthstr.' '.$year}}<br>

        <table class="table">
            <thead>
              <tr>
                <th scope="col">Arbetsplats</th>
                <th scope="col">Upparbetad tid</th>
                <th scope="col">Attesterad tid</th>
              </tr>
            </thead>
            <tbody>
            @foreach($workplaces->sortBy('name') as $workplace)
              <tr>
                <th scope="row">{{$workplace->name}} ({{$workplace->municipality->name}})</th>
                <td>{{round($workplace->project_times->where('month', $month)->where('year', $year)->sum('minutes')/60, 1)}} + {{round($workplace->month_active_time($month, $year)/60, 1)}}</td> {{--TODO: Why doesn't the active time sum up exactly with the one on attest page?--}}
                <td>{{$workplace->month_attested_time($month, $year, 3)}}</td>
              </tr>
            @endforeach
            </tbody>
          </table>

    </div>
</div>

<br>

<div class="mb-3">
    <label><input type="checkbox" {{$month_closed?'checked disabled':''}}  name="close_month">@lang('Stäng månad för attestering')</label>
</div>
