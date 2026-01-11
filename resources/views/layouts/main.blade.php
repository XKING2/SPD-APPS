<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if(session('success'))
        <meta name="alert-success" content="{{ session('success') }}">
    @endif
    @if(session('error'))
        <meta name="alert-error" content="{{ session('error') }}">
    @endif
    @if(session('warning'))
        <meta name="alert-warning" content="{{ session('warning') }}">
    @endif
    @if(session('info'))
        <meta name="alert-info" content="{{ session('info') }}">
    @endif

    <title>Seleksi Perangkat Desa</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/user.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ujianpage.css') }}" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        /* Status Label */
        .status-label {
            font-size: 0.85rem;
            font-weight: bold;
            margin-left: 10px;
            white-space: nowrap;
        }

        /* Responsive Improvements */
        /* Mobile First Approach */
        
        /* Sidebar Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 6.5rem !important;
            }
            
            .sidebar.toggled {
                width: 0 !important;
                overflow: hidden;
            }
            
            .sidebar .sidebar-brand-text {
                display: none;
            }
            
            .sidebar.toggled .sidebar-brand-text {
                display: inline;
            }
            
            .sidebar .nav-item .nav-link span {
                font-size: 0.65rem;
                display: block;
            }
            
            .sidebar .nav-item .nav-link {
                text-align: center;
                padding: 0.75rem 1rem;
                width: 6.5rem;
            }
            
            .sidebar .nav-item .nav-link i {
                margin-right: 0;
            }
            
            .sidebar .sidebar-brand {
                height: 4.375rem;
                justify-content: center;
            }
            
            .sidebar .sidebar-divider {
                margin: 0 1rem 1rem;
            }
            
            /* Content wrapper adjustment */
            #content-wrapper {
                margin-left: 0 !important;
            }
        }

        /* Topbar Responsive */
        @media (max-width: 576px) {
            .topbar {
                padding: 0.5rem 1rem;
            }
            
            .topbar .navbar-search {
                display: none !important;
            }
            
            .topbar .navbar-nav .nav-item .nav-link {
                padding: 0.5rem;
            }
            
            .topbar .navbar-nav .nav-item .nav-link span {
                display: none;
            }
            
            .topbar-divider {
                display: none !important;
            }
            
            .topbar .dropdown-list {
                width: 20rem;
                right: 0;
                left: auto;
            }
        }

        /* Container Responsive */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .d-sm-flex {
                flex-direction: column !important;
                align-items: flex-start !important;
            }
            
            .mb-4 {
                margin-bottom: 1rem !important;
            }
        }

        /* Profile Image Responsive */
        @media (max-width: 576px) {
            .img-profile {
                width: 2rem !important;
                height: 2rem !important;
            }
        }

        /* Card Responsive */
        @media (max-width: 768px) {
            .card {
                margin-bottom: 1rem;
            }
            
            .card-header {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .card-body {
                padding: 1rem;
            }
        }

        /* Table Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Button Responsive */
        @media (max-width: 576px) {
            .btn {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
            }
        }

        /* Modal Responsive */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-content {
                border-radius: 0.5rem;
            }
            
            .modal-header,
            .modal-footer {
                padding: 0.75rem;
            }
            
            .modal-body {
                padding: 1rem;
            }
        }

        /* Footer Responsive */
        @media (max-width: 768px) {
            .sticky-footer {
                position: relative;
            }
            
            .footer .copyright {
                font-size: 0.8rem;
            }
        }

        /* Scroll to Top Button */
        @media (max-width: 576px) {
            .scroll-to-top {
                width: 2.5rem;
                height: 2.5rem;
                bottom: 1rem;
                right: 1rem;
            }
            
            .scroll-to-top i {
                font-size: 0.875rem;
            }
        }

        /* Dropdown Menu Responsive */
        @media (max-width: 576px) {
            .dropdown-menu {
                font-size: 0.875rem;
            }
            
            .dropdown-item {
                padding: 0.5rem 1rem;
            }
        }

        /* Alert Responsive */
        @media (max-width: 576px) {
            .alert {
                padding: 0.75rem;
                font-size: 0.875rem;
            }
        }

        /* Badge Responsive */
        @media (max-width: 576px) {
            .badge {
                font-size: 0.65rem;
                padding: 0.25em 0.5em;
            }
        }

        /* Form Responsive */
        @media (max-width: 768px) {
            .form-group {
                margin-bottom: 1rem;
            }
            
            .form-control {
                font-size: 0.875rem;
            }
            
            label {
                font-size: 0.875rem;
            }
        }

        /* Sidebar Brand Responsive */
        @media (max-width: 768px) {
            .sidebar-brand-icon {
                width: 35px !important;
                height: 35px !important;
            }
        }

        /* Navigation Responsive */
        @media (max-width: 768px) {
            .nav-item.disabled .nav-link {
                font-size: 0.65rem;
            }
        }

        /* Ensure content is readable on all devices */
        @media (max-width: 576px) {
            body {
                font-size: 14px;
            }
            
            h1 { font-size: 1.75rem; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.25rem; }
            h4 { font-size: 1.1rem; }
            h5 { font-size: 1rem; }
            h6 { font-size: 0.875rem; }
        }

        /* Fix overflow issues */
        body, html {
            overflow-x: hidden;
        }

        #wrapper {
            overflow-x: hidden;
        }

        /* Improve touch targets for mobile */
        @media (max-width: 768px) {
            a, button, .btn {
                min-height: 44px;
                min-width: 44px;
            }
        }

        /* Tablet Specific Adjustments */
        @media (min-width: 769px) and (max-width: 1024px) {
            .sidebar {
                width: 14rem !important;
            }
            
            .container-fluid {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }

        /* Large Screen Optimization */
        @media (min-width: 1920px) {
            .container-fluid {
                max-width: 1800px;
                margin: 0 auto;
            }
        }

        /* Sidebar Toggle Animation */
        .sidebar {
            transition: width 0.3s ease-in-out;
        }

        /* Better spacing for mobile alerts */
        @media (max-width: 576px) {
            .dropdown-list {
                max-height: 80vh;
                overflow-y: auto;
            }
        }

        /* Improve readability on small screens */
        @media (max-width: 576px) {
            .text-gray-600,
            .text-gray-500 {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center px-3" 
                href="{{ route('userdashboard') }}" style="gap: 12px;">
                <div class="sidebar-brand-icon d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; border-radius: 12px; overflow: hidden;">
                    <img src="{{ asset('images/logo1.png') }}" alt="Logo E-SPJ" style="width:100%; height:100%; object-fit:cover;">
                </div>

                <div class="sidebar-brand-text text-white fw-bold" style="font-size: 1.1rem;">
                    Si SSD
                </div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item {{ Request::routeIs('userdashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('userdashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('showbiodata') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('showbiodata') }}">
                    <i class="fas fa-user fa-sm fa-fw"></i>
                    <span>Biodata</span>
                </a>
            </li>

            @if (auth()->user()->biodata && auth()->user()->biodata->status === 'valid')
                <li class="nav-item {{ Request::routeIs('showmainujian') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('showmainujian') }}">
                        <i class="fas fa-play"></i>
                        <span>Mulai Ujian</span>
                    </a>
                </li>
            @else
                <li class="nav-item disabled">
                    <a class="nav-link text-muted" href="#" style="cursor: not-allowed;">
                        <i class="fas fa-lock"></i>
                        <span>Mulai Ujian</span>
                    </a>
                </li>
            @endif

            <hr class="sidebar-divider d-none d-md-block">
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name ?? 'User' }}</span>
                                <img class="img-profile rounded-circle"
                                    src="{{ $profileImg == 'img/undraw_profile.svg'
                                            ? asset($profileImg)
                                            : asset('storage/'.$profileImg) }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('logout')}}" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        @yield('pageheads')
                    </div>

                    <div class="row">
                        <div class="col-12">
                            @yield('content')
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Kingz X DPMD 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/sweetalert-handler.js') }}"></script>

</body>

</html>