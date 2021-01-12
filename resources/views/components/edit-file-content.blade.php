<div id="file[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-header">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="file[{{$content->id}}]">
            @lang('Övrig fil')
            @if(!$content->hasTranslation(\App::getLocale()))
                (@lang('Översatt innehåll saknas - visar innehåll från standardspråk'))
            @endif
        </label>
        @if(locale_is_default())
            <a href="#" class="close remove_field" data-dismiss="alert" data-translations="{{$content->translations()->count()}}" aria-label="close">&times;</a>
        @endif
    </div>
    <div class="card-body">
        <input readonly name="file[{{$content->id}}]" class="form-control original-content" value="{{$content->filename()}}">
        <input name="replace_file[{{$content->id}}]" class="form-control" type="file" value="{{$content->filename()}}">
    </div>
</div>