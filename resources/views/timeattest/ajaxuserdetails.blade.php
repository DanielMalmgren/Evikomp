<div class="card">
    <div class="card-body">

        <table class="table table-sm table-responsive table-nonfluid">
            <thead>
                <tr>
                    <th scope="col">@lang('Typ av aktivitet')</th>
                    @for($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $day++)
                        <th scope="col" class="nowrap text-center">{{$day}}</th>
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
                                    <td class="nowrap text-center">{{$time_row[$day]}}</td>
                                @else
                                    <td class="nowrap"></td>
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
                        <td class="nowrap"></td>
                    @endfor
                    <th class="text-center">{{end($time_rows)[32]}}</td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>
