<div id="pagebreak[{{$content->id}}]" data-id="{{$content->id}}" class="card">
    <div class="card-header">
        <span class="handle"><i class="fas fa-arrows-alt-v"></i></span>
        <label class="handle" for="pagebreak[{{$content->id}}]">@lang('Sidrubrik')</label>
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
                        <input name="pagebreak[{{$content->id}}]" class="form-control original-content" value="{{$content->getTextIfExists()}}">
                    </div>
                    <div class="col-lg-2">
                        <label for="content_colors[{{$content->id}}]">@lang('Färg')</label>
                        <input name="content_colors[{{$content->id}}]" type="color" list="presetColors" value="{{$content->color->hex}}">
                        <datalist id="presetColors">
                            @foreach($colors as $color)
                                <option>{{$color->hex}}</option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>