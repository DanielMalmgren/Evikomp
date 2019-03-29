<div class="card">
    <div class="card-body">

        Sammanställning över {{$monthstr.' '.$year}}<br>

        Här kommer det att stå en massa info om vilka arbetsplatser som har skött sig och vilka som har slarvat...

    </div>
</div>

<br>

<div class="mb-3">
    <label><input type="checkbox" {{$month_closed?'checked disabled':''}}  name="close_month">@lang('Stäng månad för attestering')</label>
</div>
