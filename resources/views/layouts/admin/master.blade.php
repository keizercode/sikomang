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

    @vite(['resources/css/admin/dashboard.css', 'resources/js/ckeditor.js'])

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

    <!-- jQuery (before closing body tag) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Modern Admin CSS -->
    <style>
        /* ===========================
           CSS VARIABLES
           =========================== */
        :root {
            /* Colors from Home Page */
            --color-primary: #009966;
            --color-primary-dark: #2d5c54;
            --color-primary-light: #5a9e91;
            --color-secondary: #242621;
            --color-navbar: #003d28;
            --color-muted: #4c5250;
            --color-background: #fbfbfb;

            /* UI Colors */
            --color-surface: #ffffff;
            --color-border: #e5e7eb;
            --color-hover: #f3f4f6;
            --color-text: #1f2937;
            --color-text-light: #6b7280;

            /* Layout */
            --sidebar-width: 260px;
            --topbar-height: 72px;
            --transition-speed: 0.3s;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);

            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

        /* ===========================
           BASE STYLES
           =========================== */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--color-background);
            color: var(--color-secondary);
            -webkit-font-smoothing: antialiased;
        }

        /* ===========================
           SIDEBAR
           =========================== */
        .vertical-menu {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--color-surface);
            border-right: 1px solid var(--color-border);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1001;
            overflow-y: auto;
        }

        .vertical-menu .navbar-brand-box {
            padding: 1.5rem 1.25rem;
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
        }

        .vertical-menu .logo-md {
            color: var(--color-secondary);
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
        }

        .sidebar-menu-scroll {
            height: calc(100vh - 90px);
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.75rem 0;
        }

        .sidebar-menu-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu-scroll::-webkit-scrollbar-thumb {
            background: var(--color-border);
            border-radius: 2px;
        }

        #sidebar-menu {
            padding: 0;
        }

        .metismenu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .metismenu li {
            position: relative;
            margin-bottom: 0.25rem;
        }

        .metismenu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.25rem;
            margin: 0 0.75rem;
            color: var(--color-text-light);
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: var(--radius-md);
        }

        .metismenu a:hover {
            color: var(--color-primary);
            background: rgba(0, 153, 102, 0.05);
        }

        .metismenu a.active {
            color: var(--color-primary);
            background: rgba(0, 153, 102, 0.1);
            font-weight: 600;
        }

        .metismenu a i {
            font-size: 1.25rem;
            margin-right: 0.875rem;
            min-width: 24px;
        }

        .metismenu .sub-menu {
            list-style: none;
            padding: 0.5rem 0;
            margin: 0 0.75rem;
            background: var(--color-hover);
            border-radius: var(--radius-md);
            display: none;
        }

        .metismenu .sub-menu.mm-show {
            display: block;
        }

        .metismenu .sub-menu a {
            padding: 0.625rem 1rem 0.625rem 3rem;
            font-size: 0.813rem;
            margin: 0.125rem 0.5rem;
        }

        .metismenu .has-arrow::after {
            content: "\F0142";
            font-family: "Material Design Icons";
            position: absolute;
            right: 1.25rem;
            transition: transform 0.3s ease;
        }

        .metismenu .has-arrow.mm-active::after {
            transform: rotate(90deg);
        }

        .menu-title {
            padding: 1.5rem 1.25rem 0.5rem;
            color: var(--color-muted);
            font-size: 0.688rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===========================
           TOPBAR
           =========================== */
        .isvertical-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            box-shadow: var(--shadow-sm);
            z-index: 1000;
            transition: all var(--transition-speed) ease;
        }

        .navbar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: var(--topbar-height);
        }

        .page-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-secondary);
        }

        .header-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            background: transparent;
            border: none;
            color: var(--color-text);
            cursor: pointer;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .header-item:hover {
            background: var(--color-hover);
        }

        .header-profile-user {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 2px solid var(--color-border);
            border-radius: 50%;
        }

        .vertical-menu-btn {
            display: none;
        }

        /* ===========================
           MAIN CONTENT
           =========================== */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            transition: all var(--transition-speed) ease;
        }

        .page-content {
            padding: 2rem;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* ===========================
           CARDS
           =========================== */
        .card {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
            background: var(--color-surface);
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }

        .card-header {
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--color-secondary);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ===========================
           BADGES
           =========================== */
        .badge-soft-primary { background: rgba(0, 153, 102, 0.1); color: var(--color-primary); }
        .badge-soft-success { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .badge-soft-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
        .badge-soft-warning { background: rgba(245, 158, 11, 0.1); color: #d97706; }
        .badge-soft-info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

        /* ===========================
           BUTTONS
           =========================== */
        .btn {
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--color-primary-dark);
            border-color: var(--color-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 153, 102, 0.2);
        }

        /* ===========================
           TABLES
           =========================== */
        .table {
            margin-bottom: 0;
        }

        .table-centered th,
        .table-centered td {
            vertical-align: middle;
        }

        /* ===========================
           DROPDOWN
           =========================== */
        .dropdown-menu {
            border: 1px solid var(--color-border);
            box-shadow: var(--shadow-lg);
            border-radius: var(--radius-lg);
        }

        /* ===========================
           AVATAR
           =========================== */
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

        /* ===========================
           RESPONSIVE
           =========================== */
        @media (max-width: 991.98px) {
            .vertical-menu {
                left: calc(-1 * var(--sidebar-width));
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
