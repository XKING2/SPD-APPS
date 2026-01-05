<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Seleksi Perangkat Desa</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/footer.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/penguji.css') }}" rel="stylesheet">
    <style>
        .status-label {
            font-size: 0.85rem;
            font-weight: bold;
            margin-left: 10px;
            white-space: nowrap;
        }
    </style>


</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion position-fixed" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center px-3" href="" style="gap: 12px;">

                <div class="sidebar-brand-icon d-flex align-items-center justify-content-center"
                    style="width: 40px; height: 40px; border-radius: 12px;overflow: hidden;flex-shrink: 0;">
                    
                </div>

                <div class="sidebar-brand-text text-white fw-bold" style="font-size: 1.1rem;">
                    E-SPD
                </div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item {{ Request::routeIs('pengujidashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pengujidashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('addseleksi') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('addseleksi') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tambah Seleksi</span>
                </a>
            </li>

            <li class="nav-item {{ Request::routeIs('showtpuMain') || Request::routeIs('showWwnMain') || Request::routeIs('showPrakMain') || Request::routeIs('showOrbMain') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBeritaAcara"
                    aria-expanded="{{ Request::routeIs('showtpuMain') || Request::routeIs('showWwnMain') || Request::routeIs('showPrakMain') || Request::routeIs('showOrbMain') ? 'true' : 'false' }}"
                    aria-controls="collapseBeritaAcara">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Nilai Ujian</span>
                </a>
                <div id="collapseBeritaAcara"
                    class="collapse {{ Request::routeIs('showtpuMain') || Request::routeIs('showWwnMain') || Request::routeIs('showPrakMain') || Request::routeIs('showOrbMain') ? 'show' : '' }}"
                    aria-labelledby="headingBeritaAcara" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ Request::routeIs('showtpuMain') ? 'active' : '' }}" href="{{ route('showtpuMain') }}">TPU</a>
                        <a class="collapse-item {{ Request::routeIs('showPrakMain') ? 'active' : '' }}" href="{{ route('showPrakMain') }}">Praktik</a>
                        <a class="collapse-item {{ Request::routeIs('showWwnMain') ? 'active' : '' }}" href="{{ route('showWwnMain') }}">Wawancara</a>
                        <a class="collapse-item {{ Request::routeIs('showOrbMain') ? 'active' : '' }}" href="{{ route('showOrbMain') }}">Observasi</a>
                    </div>
                </div>
             </li>


             <li class="nav-item {{ Request::routeIs('showbiodata') || Request::routeIs('showverivikasi') ? 'active' : '' }}">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTambahSoal"
                    aria-expanded="{{ Request::routeIs('showbiodata') || Request::routeIs('showverivikasi') ? 'true' : 'false' }}"
                    aria-controls="collapseTambahSoal">
                    <i class="fas fa-fw fa-file-alt"></i>
                    <span>Tambah Soal</span>
                </a>
                <div id="collapseTambahSoal"
                    class="collapse {{ Request::routeIs('tambahtpu') || Request::routeIs('tambahwawan') ? 'show' : '' }}"
                    aria-labelledby="headingBeritaAcara" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item {{ Request::routeIs('tambahtpu') ? 'active' : '' }}" href="{{ route('tambahtpu') }}">TPU</a>
                        <a class="collapse-item {{ Request::routeIs('tambahwawan') ? 'active' : '' }}" href="{{ route('tambahwawan') }}">Wawancara</a>
                    </div>
                </div>
             </li>

            <li class="nav-item {{ Request::routeIs('generate.page') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('generate.page') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Generate Perengkingan</span>
                </a>
            </li>


            <hr class="sidebar-divider d-none d-md-block">
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
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
                            <!-- Dropdown - Messages -->
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
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
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
                                    src="{{ asset('images/logo1.png') }}">
                            </a>
                            <!-- Dropdown - User Information -->
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
            </div>
        </div>

    </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Kingz X DPMD 2025</span>
                    </div>
                </div>
            </footer>

        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

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
                    <a class="btn btn-primary" href="{{ route ('login')}}">Logout</a>
                </div>
            </div>
        </div>
    </div>

     
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

</body>

</html>