
<div class="card bg-transparent">
    <div class="card-body bg-transparent">

        @foreach($users as $user)
            @php
                $time_rows = $user->time_rows($year, $month);
                $time = end($time_rows)[32];
                if($time > 0) {
                    if($user->time_attests->where('attestlevel', 0)->where('month', $month)->where('year', $year)->isNotEmpty()) {
                        $bgclass = "bg-info";
                    } elseif($user->time_attests->where('attestlevel', 1)->where('month', $month)->where('year', $year)->isEmpty()) {
                        $bgclass = "bg-danger";
                    } elseif($user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->isEmpty()) {
                        $bgclass = "bg-warning";
                    } else {
                        $bgclass = "bg-success";
                    }
                } else {
                    $bgclass = "";
                }
            @endphp
            <a class="list-group-item list-group-item-action {{$bgclass}}" id="user-{{$user->id}}">
                <div class="row bg-transparent">
                    <div class="col-lg-5 col-md-8 col-sm-6 bg-transparent">
                        <h5 class="mb-0">{{$user->name}}</h5>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 bg-transparent">
                        <small>{{$time}}</small>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-3 bg-transparent">
                        @if($user->time_attests->where('attestlevel', 1)->where('month', $month)->where('year', $year)->isNotEmpty())
                            <small>{{$user->time_attests->where('attestlevel', 1)->where('month', $month)->where('year', $year)->first()->hours}}</small>
                        @else
                            <small>0</small>
                        @endif
                        <i class="fas fa-arrow-right"></i>
                        @if($user->time_attests->where('attestlevel', 2)->where('month', $month)->where('year', $year)->isNotEmpty())
                            <small>{{$user->time_attests->where('attestlevel', 2)->where('month', $month)->where('year', $year)->first()->hours}}</small>
                        @else
                            <small>0</small>
                        @endif
                        <i class="fas fa-arrow-right"></i>
                        @if($user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->isNotEmpty())
                            <small>{{$user->time_attests->where('attestlevel', 3)->where('month', $month)->where('year', $year)->first()->hours}}</small>
                        @else
                            <small>0</small>
                        @endif
                        @if($user->time_attests->where('attestlevel', 0)->where('month', $month)->where('year', $year)->isNotEmpty())
                            <small>({{$user->time_attests->where('attestlevel', 0)->where('month', $month)->where('year', $year)->first()->hours}})</small>
                        @endif
                    </div>


                </div>
            </a>
        @endforeach

    </div>
</div>
