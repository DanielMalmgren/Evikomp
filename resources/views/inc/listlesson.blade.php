<a href="/lessons/{{$lesson->id}}" class="list-group-item list-group-item-action {{$lesson->active?"":"list-group-item-secondary"}}"  id="id-{{$lesson->id}}">
    <div class="row">
        <div class="col-10">
            <h5 class="mb-0">
                {{$lesson->translateOrDefault(App::getLocale())->name}}
                @if($lesson->active == 0)
                    - inaktiv
                @endif
            </h5>
        </div>
        <div class="col-2">
            @if($lesson->finished())
                <small data-toggle="tooltip" title="@lang('Markerad som färdig')"><i class="fas fa-check"></i></small>
            @endif
        </div>
        {{--<div class="col-lg-1 col-md-2 col-sm-2">
            <small>@lang('Betyg'): {{$lesson->rating()}}</small>
        </div>
        <div class="col-lg-2 col-md-3 col-sm-5 test-stars">
                @if($lesson->lesson_results->where('user_id', Auth::user()->id)->first())
                @php
                    $percent = $lesson->lesson_results->where('user_id', Auth::user()->id)->first()->personal_best_percent;
                @endphp
                @if($percent>49)
                    <img src="/images/Star_happy_small.png">
                @else
                    <img src="/images/Star_unhappy_small.png">
                @endif
                @if($percent>74)
                    <img src="/images/Star_happy_small.png">
                @else
                    <img src="/images/Star_unhappy_small.png">
                @endif
                @if($percent==100)
                    <img src="/images/Star_happy_small.png">
                @else
                    <img src="/images/Star_unhappy_small.png">
                @endif
            @endif
        </div>--}}
    </div>

</a>
