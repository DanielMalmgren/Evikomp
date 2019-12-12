<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('css/bootstrap.css')}}">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('css/footer.css')}}">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('css/custom.css')}}">

        <script type="text/javascript" language="javascript" src="{{asset('js/jquery-3.4.1.min.js')}}"></script>
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('font-awesome/css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/dropdown-effects/fade-down.css')}}" />
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/webslidemenu.css')}}">
        <script type="text/javascript" language="javascript" src="{{asset('webslidemenu/webslidemenu.js')}}"></script>
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/color-skins/black-orange.css')}}" />
        <link rel="stylesheet" href="/trumbowyg/ui/trumbowyg.min.css">

        <script src="/js/timeme.min.js"></script>
        <link href="/select2/select2.min.css" rel="stylesheet" />
        <link href="/select2/select2-bootstrap4.min.css" rel="stylesheet" />
        <script src="/select2/select2.min.js"></script>
        <script src="/select2/i18n/{{substr(App::getLocale(), 0, 2)}}.js"></script>

        <script type="text/javascript">
            TimeMe.initialize({
                idleTimeoutInSeconds: 300 // seconds
            });

            function sendActiveTime(time) {
                var token = "{{ csrf_token() }}";
                console.log('Skickar tid '+time);
                $.ajax({
                    url: '/activetime',
                    data : {_token:token,time:time},
                    type: 'POST'
                });
            }

            if(navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/Firefox/i) || navigator.userAgent.match(/Macintosh/i)) {
                setInterval(function() {
                    if(!document.hidden) {
                        sendActiveTime(10);
                    }
                }, 10000);
            } else {
                window.onbeforeunload = function(){
                    sendActiveTime(Math.ceil(TimeMe.getTimeOnCurrentPageInSeconds()));
                };
            }

            jQuery(window).on('load resize scroll ajaxComplete mousewheel touchstart touchend', function () {
                if ($('footer').isInViewport()) {
                    $('.feedback').addClass("visible-footer");
                } else {
                    $('.feedback').removeClass("visible-footer");
                }
                $('.feedback').show();
            });

            jQuery.fn.isInViewport = function() {
                var elementTop = jQuery(this).offset().top;
                var elementBottom = elementTop + jQuery(this).outerHeight();
                var viewportTop = jQuery(window).scrollTop();
                var viewportBottom = viewportTop + jQuery(window).height();
                return elementBottom > viewportTop && elementTop < viewportBottom;
            };
        </script>

        <title>@yield('title') - Evikomp</title>

    </head>
    <body>
        @include('inc.navbar')
        <div class="container main clearfix">
            @include('inc.messages')
            @yield('content')
        </div>
        @if(!\Request::is('feedback'))
            <div class="feedback"><a href="/feedback"><img src="/images/Speech_balloons/Speech_balloon_{{App::getLocale()}}.png"></a></div>
        @endif
        @include('inc.footer')
    </body>
</html>
