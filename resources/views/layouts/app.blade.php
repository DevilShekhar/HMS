<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Dashboard</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">
    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- Add this to your <head> section if SweetAlert is not included -->


    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bundles/summernote/summernote-bs4.css') }}">
    @yield('style')

</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <nav class="navbar navbar-expand-lg main-navbar sticky">
                <div class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li>
                            <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn">
                                <i data-feather="align-justify"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link nav-link-lg fullscreen-btn">
                                <i data-feather="maximize"></i>
                            </a>
                        </li>
                        <li>
                            <form class="form-inline mr-auto">
                                <div class="search-element">
                                    <input class="form-control" type="search" placeholder="Search" aria-label="Search"
                                        data-width="200">
                                    <button class="btn" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </li>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    <!-- Messages -->
                    <li class="dropdown dropdown-list-toggle">
                        <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle">
                            <i data-feather="mail"></i>
                            <span class="badge headerBadge1">6</span>
                        </a>
                        <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                            <div class="dropdown-header">
                                Messages
                                <div class="float-right">
                                    <a href="#">Mark All As Read</a>
                                </div>
                            </div>
                            <div class="dropdown-list-content dropdown-list-message">
                                <a href="#" class="dropdown-item">
                                    <span class="dropdown-item-avatar text-white">
                                        <img alt="image" src="assets/img/users/user-1.png" class="rounded-circle">
                                    </span>
                                    <span class="dropdown-item-desc">
                                        <span class="message-user">John Deo</span>
                                        <span class="time messege-text">Please check your mail !!</span>
                                        <span class="time">2 Min Ago</span>
                                    </span>
                                </a>
                            </div>
                            <div class="dropdown-footer text-center">
                                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </li>
                    <!-- Notifications -->
                    <li class="dropdown dropdown-list-toggle">
                        <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
                            <i data-feather="bell" class="bell"></i>
                        </a>
                        <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                            <div class="dropdown-header">
                                Notifications
                                <div class="float-right">
                                    <a href="#">Mark All As Read</a>
                                </div>
                            </div>
                            <div class="dropdown-list-content dropdown-list-icons">
                                <a href="#" class="dropdown-item dropdown-item-unread">
                                    <span class="dropdown-item-icon bg-primary text-white">
                                        <i class="fas fa-code"></i>
                                    </span>
                                    <span class="dropdown-item-desc">
                                        Template update is available now!
                                        <span class="time">2 Min Ago</span>
                                    </span>
                                </a>
                            </div>
                            <div class="dropdown-footer text-center">
                                <a href="#">View All <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </li>
                    <!-- User -->
                    <li class="dropdown">

                        @php
                            $user = session('user');
                        @endphp

                        <a href="#" data-toggle="dropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user">

                            @if ($user && !empty($user['profile_photo']))
                                <img src="{{ env('API_BASE_URL') }}/storage/{{ $user['profile_photo'] }}"
                                    alt="{{ $user['name'] }}" class="rounded-circle mb-3">
                            @else
                                <img src="{{ asset('assets/img/user.png') }}" class="user-img-radious-style"
                                    alt="Default User">
                            @endif
                            <span class="d-sm-none d-lg-inline-block text-dark">

                                {{ Auth::user()?->name ?? 'Guest' }}

                            </span>

                        </a>

                        <div class="dropdown-menu dropdown-menu-right pullDown">

                            <div class="dropdown-title">

                                @if (auth()->check())
                                    Role :
                                    {{ auth()->user()->role ?? 'No Role' }}
                                @else
                                    Guest
                                @endif
                                <a href="profile.html" class="dropdown-item has-icon"> <i
                                        class="far
										fa-user"></i> Profile
                                </a>
                                <a href="timeline.html" class="dropdown-item has-icon"> <i class="fas fa-bolt"></i>
                                    Activities
                                </a> <a href="#" class="dropdown-item has-icon"> <i class="fas fa-cog"></i>
                                    Settings
                                </a>
                                <div class="dropdown-divider"></div>

                                @if (auth()->user()->role == 'super_admin' ?? 'N/A')
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf

                                        <button type="submit" class="dropdown-item has-icon text-danger">

                                            <i class="fas fa-sign-out-alt"></i>
                                            Logout

                                        </button>
                                    </form>
                                @else
                                    <form
                                        action="{{ route('restaurant.logout', [
                                            'restaurant' => optional(auth()->user()->restaurant)->slug,
                                        ]) }}"
                                        method="POST">

                                        @csrf

                                        <button type="submit" class="dropdown-item has-icon text-danger">

                                            <i class="fas fa-sign-out-alt"></i>
                                            Logout

                                        </button>

                                    </form>
                                @endif

                            </div>



                        </div>

                    </li>

                </ul>
            </nav>
            <!-- NAVBAR END -->
            <!-- SIDEBAR START -->

            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">

                    @php
                        $restaurantSlug = auth()->check() ? optional(auth()->user()->restaurant)->slug : null;
                        $BranchSlug = auth()->check() ? optional(auth()->user()->branch)->slug : null;
                    @endphp

                    <div class="sidebar-brand">
                        <a href="#">
                            <img alt="image" src="{{ asset('assets/img/logo.png') }}" class="header-logo">
                            <span class="logo-name">EHT</span>
                        </a>
                    </div>

                    <ul class="sidebar-menu">

                        {{-- Dashboard --}}
                        <li
                            class="{{ request()->routeIs('dashboard') || request()->routeIs('restaurant.dashboard') ? 'active' : '' }}">
                            @if (auth()->user()->role == 'super_admin')
                                <a href="{{ route('dashboard') }}" class="nav-link">
                                    <i data-feather="monitor"></i>
                                    <span>Dashboard</span>
                                </a>
                            @elseif($BranchSlug)
                                <a href="{{ route('restaurant.dashboard', [
                                    'branch' => $BranchSlug,
                                ]) }}"
                                    class="nav-link">
                                    <i data-feather="monitor"></i>
                                    <span>Dashboard</span>
                                </a>
                            @elseif($restaurantSlug)
                                <a href="{{ route('restaurant.dashboard', [
                                    'restaurant' => $restaurantSlug,
                                ]) }}"
                                    class="nav-link">
                                    <i data-feather="monitor"></i>
                                    <span>Dashboard</span>
                                </a>
                            @endif
                        </li>

                        {{-- User Management existing code --}}

                        @if (in_array(auth()->user()->role, ['super_admin', 'owner', 'branch_manager']))
                            <li class="dropdown"> <a href="#" class="nav-link has-dropdown"> <i
                                        class="fas fa-users"></i> <span>User Management</span> </a>

                                <ul class="dropdown-menu">

                                    @if (auth()->user()->role == 'super_admin')
                                        @can('view-user')
                                            <li>
                                                <a href="{{ route('users.index') }}">
                                                    User List
                                                </a>
                                            </li>
                                        @endcan

                                        @can('create-user')
                                            <li>
                                                <a href="{{ route('users.create') }}">
                                                    Create User
                                                </a>
                                            </li>
                                        @endcan

                                        @can('view-role')
                                            <li>
                                                <a href="{{ route('roles.index') }}">
                                                    Manage Role
                                                </a>
                                            </li>
                                        @endcan

                                        @can('view-permission')
                                            <li>
                                                <a href="{{ route('permissions.index') }}">
                                                    Manage Permission
                                                </a>
                                            </li>
                                        @endcan
                                    @elseif($restaurantSlug)
                                        <li>
                                            <a
                                                href="{{ route('restaurant.users.index', [
                                                    'restaurant' => $restaurantSlug,
                                                ]) }}">
                                                User List
                                            </a>
                                        </li>

                                        <li>
                                            <a
                                                href="{{ route('restaurant.users.create', [
                                                    'restaurant' => $restaurantSlug,
                                                ]) }}">
                                                Create User
                                            </a>
                                        </li>
                                    @endif

                                </ul>
                            </li>


                        @endif


                        {{-- Restaurants --}}
                        @can('view-restaurant')
                            <li class="{{ request()->routeIs('restaurants.*') ? 'active' : '' }}">
                                <a href="{{ route('restaurants.index') }}" class="nav-link">
                                    <i data-feather="home"></i>
                                    <span>Restaurants</span>
                                </a>
                            </li>
                        @endcan
                        {{-- @endif --}}
                        @can('view-branch')
                            @php
                                $route =
                                    auth()->user()->role === 'super_admin'
                                        ? route('branches.index')
                                        : route('restaurant.branches.index', [
                                            'restaurant' => optional(auth()->user()->restaurant)->slug,
                                        ]);
                            @endphp

                            <li
                                class="{{ request()->routeIs('branches.*') || request()->routeIs('restaurant.branches.*') ? 'active' : '' }}">
                                <a href="{{ $route }}" class="nav-link">
                                    <i data-feather="git-branch"></i>
                                    <span>Branches</span>
                                </a>
                            </li>
                        @endcan
                        {{-- Categories --}}
                        @if (in_array(auth()->user()->role, ['owner', 'branch_manager']))
                            @can('view-category')
                                <li class="dropdown">
                                    <a href="#" class="nav-link has-dropdown">
                                        <i data-feather="grid"></i>
                                        <span>Categories</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a
                                                href="{{ route('restaurant.categories.index', [
                                                    'restaurant' => optional(auth()->user()->restaurant)->slug,
                                                ]) }}">
                                                Category List
                                            </a>
                                        </li>
                                        <li>
                                            <a
                                                href="{{ route('restaurant.categories.create', [
                                                    'restaurant' => optional(auth()->user()->restaurant)->slug,
                                                ]) }}">
                                                Create Category
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                        @endif
                        {{-- Menu Management --}}
                        @if (in_array(auth()->user()->role, ['owner', 'branch_manager']))
                            @can('view-menu')
                                <li class="dropdown">
                                    <a href="#" class="nav-link has-dropdown">
                                        <i data-feather="coffee"></i>
                                        <span>Menu Management</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a
                                                href="{{ route('restaurant.menu-items.index', ['restaurant' => optional(auth()->user()->restaurant)->slug]) }}">
                                                Menu List</a>
                                        </li>
                                        <li>
                                            <a
                                                href="{{ route('restaurant.menu-items.create', ['restaurant' => optional(auth()->user()->restaurant)->slug]) }}">
                                                Add Menu Item </a>
                                        </li>
                                    </ul>
                                </li>
                            @endcan
                        @endif
                        <li class="dropdown">
                            <a href="#" lass="nav-link has-dropdown">
                                <i data-feather="archive"></i>
                                <span>
                                    Inventory Management
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route(
                                        'restaurant.inventory.index',
                                        [
                                            'restaurant' => optional(
                                                auth()->user()->restaurant
                                            )->slug
                                        ]
                                    ) }}">
                                        Inventory List
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route(
                                        'restaurant.inventory.create',
                                        [
                                            'restaurant' => optional(
                                                auth()->user()->restaurant
                                            )->slug
                                        ]
                                    ) }}">
                                        Add Inventory Item
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- Order Management --}}
                        @can('view-order')
                            <li class="dropdown">
                                <a href="#" class="nav-link has-dropdown">
                                    <i data-feather="shopping-cart"></i>
                                    <span>Order Management</span>
                                </a>
                                @if (auth()->user()->restaurant)
                                    @can('view-order')
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a
                                                    href="{{ route('restaurant.orders.index', ['restaurant' => auth()->user()->restaurant->slug]) }}">
                                                    Order List
                                                </a>
                                            </li>
                                        @endcan
                                        @can('create-order')
                                            <li>
                                                <a
                                                    href="{{ route('restaurant.orders.create', ['restaurant' => auth()->user()->restaurant->slug]) }}">
                                                    Create Order
                                                </a>
                                            </li>
                                        </ul>
                                    @endcan
                                @endcan
                            @endif
                        </li>
                        {{-- Restaurant Info --}}
                        @if (auth()->user()->role != 'super_admin')
                            <li class="menu-header">
                                Session
                            </li>
                        @endif
                        {{-- Logout --}}
                        <li>

                            @if (auth()->user()->role == 'super_admin')
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="nav-link">

                                    <i data-feather="log-out"></i>
                                    <span>Logout</span>

                                </a>
                            @else
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('restaurant-logout-form').submit();"
                                    class="nav-link">

                                    <i data-feather="log-out"></i>
                                    <span>Logout</span>

                                </a>
                            @endif

                        </li>

                    </ul>

                </aside>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>

            @if (auth()->check() && auth()->user()->role != 'super_admin' && $restaurantSlug)
                <form id="restaurant-logout-form"
                    action="{{ route('restaurant.logout', [
                        'restaurant' => $restaurantSlug,
                    ]) }}"
                    method="POST" style="display:none;">
                    @csrf
                </form>
            @endif


            <main id="main-contentmain" class="main-content">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/export-tables/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/export-tables/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/export-tables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/export-tables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/export-tables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/datatables.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/bundles/summernote/summernote-bs4.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('scripts')
</body>

</html>
