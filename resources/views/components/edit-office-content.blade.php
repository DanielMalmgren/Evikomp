<div id="office[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-header">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="office[{{$content->id}}]">
            @lang('Office-fil')
            @if(!$content->hasTranslation(\App::getLocale()))
                (@lang('Översatt innehåll saknas - visar innehåll från standardspråk'))
            @endif
            - {{$content->summary}}
        </label>
        @if(locale_is_default())
            <a href="#" class="close remove_field pl-3" data-dismiss="alert" data-translations="{{$content->translations()->count()}}" aria-label="close">&times;</a>
        @endif
        <a data-toggle="collapse" href="#body{{$content->id}}" id="collapstoggle{{$content->id}}">
            <i class="fas fa-chevron-up float-right text-dark"></i>
        </a>
    </div>
    <div class="collapse multi-collapse show" id="body{{$content->id}}">
        <div class="card-body">
            <input readonly name="office[{{$content->id}}]" class="form-control original-content" value="{{$content->filename()}}">
            <input name="replace_file[{{$content->id}}]" class="form-control" type="file" accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.presentationml.presentation" value="{{$content->filename()}}">
        </div>
    </div>
</div>