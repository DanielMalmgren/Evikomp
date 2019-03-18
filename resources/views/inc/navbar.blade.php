<script>
    $(function() {
        $( 'body').on( 'click', 'a#logout', function( event ) {
            window.location.replace("/logout");
            var wnd = window.open("{{env('SAML2_IDP_HOST')}}/wa/logout");
            wnd.close();
            return false;
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
                                <li aria-haspopup="false"><a href="/timesummary">@lang('Sammanställning till ESF')</a></li>
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
            </ul>
        </nav>
        <!--Menu HTML Code-->

    </div>
</div>

<div class="wsheader-fill"></div>
