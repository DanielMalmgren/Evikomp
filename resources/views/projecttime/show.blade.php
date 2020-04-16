@extends('layouts.app')

@section('title', __('Visa projekttid'))

@section('content')

<div class="col-md-12">

    <H1>@lang('Visa registrerad tid') - {{$monthstr.' '.$year}}</H1>

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
        </div>
    </div>

    <br>
    <a href="/projecttime/{{$previous_year}}/{{$previous_month}}" class="btn btn-primary">@lang('Föregående månad')</a>
    @isset($next_year)
        <a href="/projecttime/{{$next_year}}/{{$next_month}}" class="btn btn-primary">@lang('Nästkommande månad')</a>
    @endisset

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
    });
</script>

@endsection
