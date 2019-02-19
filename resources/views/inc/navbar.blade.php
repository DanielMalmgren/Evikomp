<!-- Mobile Header -->
<div class="wsmobileheader clearfix ">
    <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
    <span class="smllogo"><a href="#"><img src="/images/Evikomp_logga.png"></a></span>
</div>
<!-- Mobile Header -->

<div class="wsmainfull clearfix">
    <div class="wsmainwp clearfix">

        <div class="desktoplogo"><a href="/"><img src="/images/Evikomp_logga.png"></a></div>

        <!--Main Menu HTML Code-->
        <nav class="wsmenu clearfix">
            <ul class="wsmenu-list">
                @hasanyrole('Registered|Admin')
                    <li aria-haspopup="false"><a href="/" class="menuhomeicon {{ request()->is('/') ? 'active' : '' }}"><i class="fa fa-home"></i><span class="hometext">&nbsp;&nbsp;@lang('Hem')</span></a></li>
                    <!-- <li aria-haspopup="false"><a href="/userinfo" class="{{ request()->is('userinfo') ? 'active' : '' }}"></i>@lang('Användarinfo')</a></li> -->
                    <li aria-haspopup="false"><a href="/tracks" class="{{ request()->is('tracks') ? 'active' : '' }}"></i>@lang('Spår')</a></li>
                    <li aria-haspopup="false"><a href="/settings" class="{{ request()->is('settings') ? 'active' : '' }}"></i>@lang('Inställningar')</a></li>
                    @can('use administration')
                        <li aria-haspopup="true"><a href="#"><i class="fa fa-angle-right"></i>@lang('Administration')</a>
                            <ul class="sub-menu">
                                @can('list users')
                                    <li aria-haspopup="false"><a href="/listusers">@lang('Användare')</a></li>
                                @endcan
                                <li aria-haspopup="false"><a href="/workplace">@lang('Arbetsplatsinställningar')</a></li>
                            </ul>
                        </li>
                    @endcan
                    <li aria-haspopup="false"><a href="/userinfo">{{Auth::user()->name}} {{Auth::user()->workplace?"(".Auth::user()->workplace->name.")":""}}</a></li>
                @endhasanyrole
            </ul>
        </nav>
        <!--Menu HTML Code-->

    </div>
</div>

<div class="wsheader-fill"></div>
