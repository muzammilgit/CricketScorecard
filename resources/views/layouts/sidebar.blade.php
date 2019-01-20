
    <!--Start sidebar-wrapper-->
    <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
        <div class="brand-logo">
            <a href="">
                <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
                <h5 class="logo-text">MCL Scorer</h5>
            </a>
        </div>
        <ul class="sidebar-menu do-nicescrol">
            <li class="sidebar-header">MAIN NAVIGATION</li>
            {{--<li>--}}
                {{--<a href="{{url('/home')}}" class="waves-effect">--}}
                    {{--<i class="icon-home"></i> <span>Dashboard</span>--}}
                {{--</a>--}}
            {{--</li>--}}
            <li>
                <a href="{{ url('/teams') }}" class="waves-effect">
                    <i class="icon-briefcase"></i>
                    <span>Teams</span></i>
                </a>
            </li>
            <li>
                <a href="{{ url('/players') }}" class="waves-effect">
                    <i class="icon-calendar"></i> <span>Players</span>
                    {{--<small class="badge float-right badge-info">New</small>--}}
                </a>
            </li>

            <li>
                <a href="{{ url('/matches') }}" class="waves-effect">
                    <i class="icon-envelope"></i>
                    <span>Matches</span>
                    {{--<small class="badge float-right badge-warning">12</small>--}}
                </a>
            </li>

            <li>
                <a href="{{ url('/contactUs') }}" class="waves-effect">
                    <i class="icon-layers"></i>
                    <span>Contact Form details</span>
                </a>
            </li>

        </ul>

    </div>
    <!--End sidebar-wrapper-->

    <!--Start topbar header-->
    <header class="topbar-nav">
        <nav class="navbar navbar-expand fixed-top bg-white">
            <ul class="navbar-nav mr-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link toggle-menu" href="javascript:void(0);">
                        <i class="icon-menu menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <form class="search-bar">
                        <input type="text" class="form-control" placeholder="Enter keywords">
                        <a href="javascript:void(0);"><i class="icon-magnifier"></i></a>
                    </form>
                </li>
            </ul>

            <ul class="navbar-nav align-items-center right-nav-link">

                <li class="nav-item language">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret waves-effect" data-toggle="dropdown" href="#"><i class="flag-icon flag-icon-gb"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="dropdown-item"> <i class="flag-icon flag-icon-gb mr-2"></i> English</li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
                        <span class="user-profile"><img src="{{asset('assets/images/avatars/avatar-17.png')}}" class="img-circle" alt="user avatar"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="dropdown-item user-details">
                            <a href="javaScript:void(0);">
                                <div class="media">
                                    <div class="avatar"><img class="align-self-start mr-3" src="{{asset('assets/images/avatars/avatar-17.png')}}" alt="user avatar"></div>
                                    <div class="media-body">
                                        <h6 class="mt-2 user-title">Admin</h6>
                                        <p class="user-subtitle">admin@co.in</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li class="dropdown-item"><a href="{{ route('logout') }}"><i class="icon-power mr-2"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
    <!--End topbar header-->

    <div class="clearfix"></div>