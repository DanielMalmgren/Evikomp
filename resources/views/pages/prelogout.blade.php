@extends('layouts.app')

@section('title', __('Utloggning'))

@section('content')

<script>
    $(function() {
        $('body').on( 'click', 'a#logout', function( event ) {
            var wnd = window.open("{{env('SAML2_IDP_HOST')}}/wa/logout");
            setTimeout(function() {
                wnd.close();
                window.location.replace("/logout");
            }, 100);
            return false;
        });
    });
</script>

<h1>@lang('Du är nu på väg att logga ut...')</h1>

@lang('...men du har inte attesterat din tid för :month än. Ska du kanske göra det först?', ['month' => $monthstr])

<br><br>

<a href="/attest" class="btn btn-primary">@lang('Ja just det! Jag vill attestera nu på direkten!')</a>
<br><br>

@lang('Om du vill kan du först se på en liten film om attesteringen och varför den är viktig!')

<br><br>

<div style="width:100%;max-width:300px">
    <div class="vimeo-container">
        <iframe id="vimeo_x" src="https://player.vimeo.com/video/588350985" width="0" height="0" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
    </div>
</div>
<script type="text/javascript">
    var iframePlayer = new Vimeo.Player(document.querySelector('#vimeo_x'));
    @if(Auth::user()->use_subtitles)
        iframePlayer.enableTextTrack('{{substr(App::getLocale(), 0, 2)}}').catch(function(error) {/*Do nothing if subtitle is missing*/});
    @else
        iframePlayer.disableTextTrack().catch(function(error) {/*Do nothing if subtitle is missing*/});
    @endif
    iframePlayer.on('timeupdate', function(data){
        window.focus();
        TimeMe.resetIdleCountdown();
    });
</script>

<br>
<a href="#" id="logout" class="btn btn-secondary">@lang('Nej, jag behöver logga ut nu. Jag lovar att attestera senare!')</a>

@endsection
