<!-- Mobile Header -->
<div class="wsmobileheader clearfix ">
    <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
    <span class="smllogo"><h1><a href="#">Evikomp</a></h1></span>
</div>
<div class="wsmobileheader-fill"></div>
<!-- Mobile Header -->

<div class="wsmainfull clearfix">
    <div class="wsmainwp clearfix">

        <div class="desktoplogo"><h1><a href="/">Evikomp</a></h1></div>

        <!--Main Menu HTML Code-->
        <nav class="wsmenu clearfix">
            <ul class="wsmenu-list">
                @hasanyrole('Registered|Admin')
                    <li aria-haspopup="false"><a href="/" class="menuhomeicon {{ request()->is('/') ? 'active' : '' }}"><i class="fa fa-home"></i><span class="hometext">&nbsp;&nbsp;Home</span></a></li>
                    <li aria-haspopup="false"><a href="/userinfo" class="{{ request()->is('userinfo') ? 'active' : '' }}"><i class="fa"></i>@lang('Användarinfo')</a></li>
                    <li aria-haspopup="false"><a href="/tracks" class="{{ request()->is('tracks') ? 'active' : '' }}"><i class="fa"></i>@lang('Spår')</a></li>
                    <li aria-haspopup="false"><a href="/settings" class="{{ request()->is('settings') ? 'active' : '' }}"><i class="fa"></i>@lang('Inställningar')</a></li>
                    @can('use administration')
                        <li aria-haspopup="true"><a href="#"><i class="fa fa-angle-right"></i>@lang('Administration')</a>
                            <ul class="sub-menu">
                                @can('list users')
                                    <li aria-haspopup="false"><a href="/listusers">@lang('Användare')</a></li>
                                @endcan
                            <li aria-haspopup="false"><a href="#">@lang('Spår')</a></li>
                            </ul>
                        </li>
                    @endcan
                @endhasanyrole
            </ul>
        </nav>
        <!--Menu HTML Code-->

    </div>
</div>
