<!DOCTYPE html>

<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <title>CMananger</title>
    <link rel="shortcut icon" type="image/png" href="{{asset('favicon.ico')}}"/>


    <!-- External stylesheets -->
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,600,700,300&subset=latin" rel="stylesheet" type="text/css">
    <link href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- Core stylesheets -->
    <link href="{{ asset('pixel/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('pixel/css/pixeladmin.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('pixel/css/widgets.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('pixel/css/app.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme -->
    <link href="{{ asset('pixel/css/themes/default.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Pace.js -->
    <script src="{{ asset('pixel/pace/pace.min.js') }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="{{ asset('/pixel/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/pixel/js/pixeladmin.min.js') }}"></script>

    <!-- General scripts -->
    <script src="{{ asset('/pixel/js/app.js') }}"></script>


@section('css')
    @show

</head>
<body>
<!-- Nav -->
<nav class="px-nav px-nav-left">
    <button type="button" class="px-nav-toggle" data-toggle="px-nav">
        <span class="px-nav-toggle-arrow"></span>
        <span class="navbar-toggle-icon"></span>
        <span class="px-nav-toggle-label font-size-11">HIDE MENU</span>
    </button>

    <ul class="px-nav-content">
        @php
            $active = \Illuminate\Support\Facades\Request::route()->getName();
            if(strpos($active, 'admin.member') !== false){
                $active_members = "active";
            }elseif(strpos($active, 'admin.email') !== false){
                 $active_emails = "active";
            }elseif(strpos($active, 'admin.order') !== false){
                 $active_orders = "active";
            }else {
                $active_dashboard = "active";
            }
        @endphp

        <li class="px-nav-item">
            <a href="#"><i class="px-nav-icon ion-ios-pulse-strong"></i><span class="px-nav-label">Dashboards</span></a>
        </li>
        <li class="px-nav-item {{ $active_orders or "" }}">
            <a href="{{ route('admin.order.index') }}"><i class="px-nav-icon fa fa-shopping-cart"></i><span class="px-nav-label">Orders</span></a>
        </li>
        @if(auth()->user()->group_id == 1)
        <li class="px-nav-item {{ $active_members or "" }}">
            <a href="{{ route('admin.member') }}"><i class="px-nav-icon fa fa-users"></i><span class="px-nav-label">Members</span></a>
        </li>
        <li class="px-nav-item {{ $active_emails or "" }}">
            <a href="{{ route('admin.email') }}"><i class="px-nav-icon fa fa-envelope"></i><span class="px-nav-label">Emails</span></a>
        </li>
        @endif

    </ul>
</nav>
<!-- Navbar -->
<nav class="navbar px-navbar">
    <!-- Header -->
    <div class="navbar-header">
        <a class="navbar-brand px-demo-brand" href="{{ route('admin.index') }}">CMANAGER</a>
    </div>

    <!-- Navbar togglers -->
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#px-demo-navbar-collapse" aria-expanded="false"><i class="navbar-toggle-icon"></i></button>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="px-demo-navbar-collapse">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="{{ route('admin.getsetting') }}">Configs</a>
            </li>

        </ul>

        <ul class="nav navbar-nav navbar-right">

            <li>
                <form class="navbar-form" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Search" style="width: 140px;">
                    </div>
                </form>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="dropdown-icon fa fa-user"></i>
                    <span>{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="dropdown-icon fa fa-power-off"></i> {{ __('Logout') }}
                        </a>
                    </li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>

        </ul>
    </div><!-- /.navbar-collapse -->
</nav>
<!-- Content -->
<div class="px-content">
    @yield('content')
</div>

<!-- Footer -->
<footer class="px-footer px-footer-bottom">
    Copyright 2018 by CManager
</footer>

<!-- ==============================================================================
|
|  SCRIPTS
|
=============================================================================== -->


@section('javascript')
@show
</body>
</html>
