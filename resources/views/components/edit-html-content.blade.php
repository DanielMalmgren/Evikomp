<div id="html[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-body">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="html[{{$content->id}}]">
            @lang('Text')
            @if(!$content->hasTranslation(\App::getLocale()))
                (@lang('Översatt innehåll saknas - visar innehåll från standardspråk'))
            @endif
        </label>
        @if(locale_is_default())
            <a href="#" class="close remove_field" data-dismiss="alert" data-translations="{{$content->translations()->count()}}" aria-label="close">&times;</a>
        @endif
        <textarea rows="4" name="html[{{$content->id}}]" class="form-control twe original-content">{!!$content->text!!}</textarea>
    </div>
</div>