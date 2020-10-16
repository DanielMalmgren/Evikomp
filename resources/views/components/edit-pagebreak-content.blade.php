<div id="pagebreak[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-body">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="pagebreak[{{$content->id}}]">@lang('Sidrubrik')</label>
        @if(locale_is_default())
            <a href="#" class="close remove_field" data-dismiss="alert" data-translations="{{$content->translations()->count()}}" aria-label="close">&times;</a>
        @endif
        <input name="pagebreak[{{$content->id}}]" class="form-control original-content" value="{{$content->getTextIfExists()}}">
    </div>
</div>