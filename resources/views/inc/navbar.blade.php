<link href="/select2/select2.min.css" rel="stylesheet" />
<link href="/select2/select2-bootstrap4.min.css" rel="stylesheet" />
<script src="/select2/select2.min.js"></script>
<script src="/select2/i18n/sv.js"></script>

<script>
    $(function() {
        $( 'body').on( 'click', 'a#logout', function( event ) {
            window.location.replace("/logout");
            var wnd = window.open("{{env('SAML2_IDP_HOST')}}/wa/logout");
            wnd.close();
            return false;
        });

        $('.global-search').select2({
            width: '240px',
            placeholder: "Sök",
            ajax: {
                url: '/select2search',
                dataType: 'json'
            },
            language: "sv",
            minimumInputLength: 3,
            //https://stackoverflow.com/questions/46069939/select2-remove-inputtooshort-text
            //TODO: Kolla upp hur jag får nedanstående att funka och samtidigt svenska...
            language: {
                inputTooShort: function(args) {
                    return "";
                }
            },
            theme: "bootstrap4"
        });

        $('.global-search').on('select2:select', function (e) {
            var lesson_id = e.params.data.id;
            window.location = "/lessons/"+lesson_id;
        });
    });
</script>

<!-- Mobile Header -->
<div class="wsmobileheader clearfix ">
    <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
    <span class="smllogo"><a href="#"><img src="/images/Evikomp_logga_lab.png"></a></span>
</div>
<!-- Mobile Header -->

<div class="wsmainfull clearfix">
    <div class="wsmainwp clearfix">

        <div class="desktoplogo"><a href="/"><img src="{{env('HEADER_LOGO')}}"></a></div>

        <!--Main Menu HTML Code-->
        <nav class="wsmenu clearfix">
            <ul class="wsmenu-list">
                @hasanyrole('Registrerad|Admin')
                    <li aria-haspopup="false"><a href="/" class="menuhomeicon {{ request()->is('/') ? 'active' : '' }}"><i class="fa fa-home"></i><span class="hometext">&nbsp;&nbsp;@lang('Hem')</span></a></li>
                    <li aria-haspopup="false"><a href="/tracks" class="{{ request()->is('tracks') ? 'active' : '' }}"></i>@lang('Spår')</a></li>
                    @can('use administration')
                        <li aria-haspopup="true"><a href="#"><i class="fa fa-angle-right"></i>@lang('Administration')</a>
                            <ul class="sub-menu">
                                @can('manage users')
                                    <li aria-haspopup="false"><a href="/listusers">@lang('Användare')</a></li>
                                @endcan
                                @canany(['add workplaces','edit workplaces'])
                                    <li aria-haspopup="false"><a href="/workplace">@lang('Arbetsplatsinställningar')</a></li>
                                @endcanany
                                <li aria-haspopup="false"><a href="/projecttime/create">@lang('Registrera projekttid')</a></li>
                                <li aria-haspopup="false"><a href="/timeattest/create">@lang('Attestera projekttid')</a></li>
                                @can('export ESF report')
                                    <li aria-haspopup="false"><a href="/timesummary">@lang('Sammanställning till ESF')</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcan
                    <li aria-haspopup="true"><a href="#"><i class="fa fa-angle-right"></i>{{Auth::user()->firstname}}</a>
                        <ul class="sub-menu">
                            <li aria-haspopup="false"><a href="/settings">@lang('Inställningar')</a></li>
                            <li aria-haspopup="false"><a href="/feedback">@lang('Feedback')</a></li>
                            <li aria-haspopup="false"><a href="/projecttime/createsingleuser">@lang('Registrera projekttid')</a></li>
                            <li aria-haspopup="false"><a href="/timeattestlevel1/create">@lang('Attestera projekttid')</a></li>
                            <li aria-haspopup="false"><a href="#" id="logout">@lang('Logga ut')</a></li>
                        </ul>
                    </li>
                @endhasanyrole
                <li aria-haspopup="true"><a href="#"><i class="fa fa-angle-right"></i>@lang('Hjälp')</a>
                    <ul class="sub-menu">
                        <li aria-haspopup="false"><a target="_blank" href="/pdf/Evikomp%20användarmanual.pdf">@lang('Användarmanual')</a></li>
                        @can('use administration')
                            <li aria-haspopup="false"><a target="_blank" href="/pdf/Evikomp%20administratörsmanual.pdf">@lang('Administratörsmanual')</a></li>
                        @endcan
                        @hasrole('Admin')
                            <li aria-haspopup="false"><a target="_blank" href="/pdf/Evikomp%20intern%20manual.pdf">@lang('Intern manual')</a></li>
                        @endhasrole
                        <li aria-haspopup="false"><a target="_blank" href="https://www.linkoping.se/utforarwebben/vard-stod-och-omsorg/forskning-och-utveckling/pagaende-projekt/evikomp/">@lang('Om Evikomp')</a></li>
                    </ul>
                </li>
                {{--<li aria-haspopup="false"><select class="global-search"></select></li>--}}
            </ul>
        </nav>
        <!--Menu HTML Code-->

    </div>
</div>

<div class="wsheader-fill"></div>
