<!-- Mobile Header -->
<div class="wsmobileheader clearfix ">
    <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
    <span class="smllogo"><h1><a href="#">Evikomp</a></h1></span>
</div>
<!-- Mobile Header -->

<div class="wsmainfull clearfix">
        <div class="wsmainwp clearfix">

          <div class="desktoplogo"><h1><a href="/">Evikomp</a></h1></div>

            <!--Main Menu HTML Code-->
            <nav class="wsmenu clearfix">
                <ul class="wsmenu-list">

                    <li aria-haspopup="true"><a href="/" class="active menuhomeicon"><i class="fa fa-home"></i><span class="hometext">&nbsp;&nbsp;Home</span></a></li>
                    @can('list users')
                        <li aria-haspopup="true"><a href="/listusers"><i class="fa fa-angle-right"></i>@lang('Lista användare')</a></li>
                    @endcan
                    <li aria-haspopup="false"><a href="/userinfo"><i class="fa fa-angle-right"></i>@lang('Användarinfo')</a></li>
                    <li aria-haspopup="true"><a href="/tracks"><i class="fa fa-angle-right"></i>@lang('Spår')</a></li>
                    <li aria-haspopup="true"><a href="/settings"><i class="fa fa-angle-right"></i>@lang('Inställningar')</a></li>
                    </li>

                </ul>
            </nav>
            <!--Menu HTML Code-->

        </div>
    </div>
</div>
