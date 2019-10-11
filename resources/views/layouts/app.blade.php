<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('css/bootstrap.css')}}">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('css/footer.css')}}">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('css/custom.css')}}">

        <script type="text/javascript" language="javascript" src="{{asset('js/jquery.min.js')}}"></script>
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('font-awesome/css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/dropdown-effects/fade-down.css')}}" />
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/webslidemenu.css')}}">
        <script type="text/javascript" language="javascript" src="{{asset('webslidemenu/webslidemenu.js')}}"></script>
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/color-skins/black-orange.css')}}" />
        <link rel="stylesheet" href="/trumbowyg/ui/trumbowyg.min.css">

        <script src="/js/timeme.min.js"></script>
        <script type="text/javascript">
            TimeMe.initialize({
                idleTimeoutInSeconds: 300 // seconds
            });

            window.onbeforeunload = function(){
                var time = Math.ceil(TimeMe.getTimeOnCurrentPageInSeconds());
                var token = "{{ csrf_token() }}";
                $.ajax({
                    url: '/activetime',
                    data : {_token:token,time:time},
                    type: 'POST'
                });
            };

            jQuery(document).ready(function ($) {
                $('.feedback').show();
                $(window).on('resize scroll load', function() {
                    if ($('footer').isInViewport()) {
                        $('.feedback').addClass("visible-footer");
                    } else {
                        $('.feedback').removeClass("visible-footer");
                    }
                });
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
            <div class="feedback"><a href="/feedback"><img src="/images/Speech_balloon.png"></a></div>
        @endif
        @include('inc.footer')
    </body>
</html>
