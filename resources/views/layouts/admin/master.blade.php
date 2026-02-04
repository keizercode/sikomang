<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'SIKOMANG Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIKOMANG - Sistem Informasi dan Komunikasi Mangrove DKI Jakarta" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="https://pencil-matter-70015947.figma.site/_assets/v11/5a52c0026642845f54f76f85096c3a34c237af42.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- Bootstrap Table -->
    <link href="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css" rel="stylesheet">

    <!-- Alertify -->
    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Admin CSS -->
{{-- @vite(['resources/css/admin/dashboard.css']) --}}
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --primary-color: #009966;
            --sidebar-width: 250px;
            --topbar-height: 70px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar */
        .vertical-menu {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: #2a3042;
            box-shadow: 0 2px 4px rgba(15,34,58,.12);
            transition: all 0.3s ease;
            z-index: 1001;
            overflow-y: auto;
        }

        .vertical-menu .navbar-brand-box {
            padding: 1.5rem 1rem;
            background: #2a3042;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .vertical-menu .logo-md {
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-menu-scroll {
            height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .sidebar-menu-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-menu-scroll::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }

        #sidebar-menu {
            padding: 1rem 0;
        }

        .metismenu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .metismenu li {
            position: relative;
        }

        .metismenu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #a6b0cf;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .metismenu a:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        .metismenu a.active {
            color: #fff;
            background: rgba(0,153,102,0.2);
            border-left: 3px solid var(--primary-color);
        }

        .metismenu a i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            min-width: 24px;
        }

        .metismenu .sub-menu {
            list-style: none;
            padding-left: 0;
            background: rgba(0,0,0,0.1);
            display: none;
        }

        .metismenu .sub-menu.mm-show {
            display: block;
        }

        .metismenu .sub-menu a {
            padding-left: 3.5rem;
            font-size: 0.9rem;
        }

        .metismenu .has-arrow::after {
            content: "\F0142";
            font-family: "Material Design Icons";
            position: absolute;
            right: 1.5rem;
            transition: transform 0.3s ease;
        }

        .metismenu .has-arrow.mm-active::after {
            transform: rotate(90deg);
        }

        .menu-title {
            padding: 1rem 1.5rem 0.5rem;
            color: #6c757d;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Topbar */
        .isvertical-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            box-shadow: 0 2px 4px rgba(15,34,58,.12);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            height: var(--topbar-height);
        }

        .page-title-box {
            padding: 0 1rem;
        }

        .page-title {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: #495057;
        }

        .header-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: transparent;
            border: none;
            color: #495057;
            cursor: pointer;
        }

        .header-item:hover {
            background: #f8f9fa;
        }

        .header-profile-user {
            width: 36px;
            height: 36px;
            object-fit: cover;
        }

        .vertical-menu-btn {
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            transition: all 0.3s ease;
        }

        .page-content {
            padding: 1.5rem;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Badges */
        .badge-soft-primary { background: rgba(0,153,102,0.1); color: var(--primary-color); }
        .badge-soft-success { background: rgba(25,135,84,0.1); color: #198754; }
        .badge-soft-danger { background: rgba(220,53,69,0.1); color: #dc3545; }
        .badge-soft-warning { background: rgba(255,193,7,0.1); color: #ffc107; }
        .badge-soft-info { background: rgba(13,202,240,0.1); color: #0dcaf0; }

        /* Avatar */
        .avatar {
            width: 48px;
            height: 48px;
        }

        .avatar-title {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background: #007a52;
            border-color: #007a52;
        }

        /* Table */
        .table {
            margin-bottom: 0;
        }

        .table-centered th,
        .table-centered td {
            vertical-align: middle;
        }

        /* Dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .vertical-menu {
                left: -var(--sidebar-width);
            }

            .vertical-menu.show {
                left: 0;
            }

            .isvertical-topbar {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .vertical-menu-btn {
                display: block;
            }
        }
    </style>

    @yield('css')
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- Sidebar -->
        @include('components.admin.sidebar')

        <!-- Topbar -->
        @include('components.admin.topbar')

        <!-- Main Content -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Table -->
    <script src="https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js"></script>

    <!-- Alertify -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Metismenu -->
    <script>
        $(document).ready(function() {
            // Toggle submenu
            $('.has-arrow').on('click', function(e) {
                e.preventDefault();
                $(this).toggleClass('mm-active');
                $(this).next('.sub-menu').slideToggle(200).toggleClass('mm-show');
            });

            // Mobile menu toggle
            $('.vertical-menu-btn').on('click', function() {
                $('.vertical-menu').toggleClass('show');
            });

            // Set active menu
            var currentUrl = window.location.href;
            $('.metismenu a').each(function() {
                if ($(this).attr('href') === currentUrl) {
                    $(this).addClass('active');
                    $(this).parents('.sub-menu').addClass('mm-show');
                    $(this).parents('.sub-menu').prev('.has-arrow').addClass('mm-active');
                }
            });
        });
    </script>

    <!-- Show flash messages -->
    @if(session('message'))
    <script>
        @if(session('type') == 'success')
            alertify.success('{{ session("message") }}');
        @elseif(session('type') == 'error')
            alertify.error('{{ session("message") }}');
        @else
            alertify.message('{{ session("message") }}');
        @endif
    </script>
    @endif

    @yield('js')
</body>
</html>
