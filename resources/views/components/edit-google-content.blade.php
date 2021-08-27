<div id="google[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-header">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="google[{{$content->id}}]">@lang('Google-fil')</label>
        @if(locale_is_default())
            <a href="#" class="close remove_field pl-3" data-dismiss="alert" data-translations="{{$content->translations()->count()}}" aria-label="close">&times;</a>
        @endif
        <a data-toggle="collapse" href="#body{{$content->id}}" id="collapstoggle{{$content->id}}">
            <i class="fas fa-chevron-up float-right text-dark"></i>
        </a>
    </div>
    <div class="collapse multi-collapse show" id="body{{$content->id}}">
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <label for="google[{{$content->id}}]">@lang('HTML-kod')</label>
                        <input name="google[{{$content->id}}]" class="form-control original-content" value="{{$content->content}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>