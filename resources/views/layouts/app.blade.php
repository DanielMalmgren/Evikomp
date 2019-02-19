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

        <script src="/js/timeme.min.js"></script>
        <script type="text/javascript">
            TimeMe.initialize({
                idleTimeoutInSeconds: 60 // seconds
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
        </script>

        <title>@yield('title') - Evikomp</title>

    </head>
    <body>
        @include('inc.navbar')
        <div class="container main clearfix">
            @include('inc.messages')
            @yield('content')
        </div>
        @include('inc.footer')
    </body>
</html>
