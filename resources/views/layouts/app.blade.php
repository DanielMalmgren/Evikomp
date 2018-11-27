<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{asset('css/app.css')}}">

        <script type="text/javascript" language="javascript" src="{{asset('js/jquery.min.js')}}"></script>
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('font-awesome/css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/dropdown-effects/fade-down.css')}}" />
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/webslidemenu.css')}}">
        <script type="text/javascript" language="javascript" src="{{asset('webslidemenu/webslidemenu.js')}}"></script>
        <link rel="stylesheet" type="text/css" media="all" href="{{asset('webslidemenu/color-skins/black-blue.css')}}" />

        <title>Evikomp</title>

    </head>
    <body>
        @include('inc.navbar')
        <div class="container clearfix">
            @include('inc.messages')
            @yield('content')
        </div>
    </body>
</html>
