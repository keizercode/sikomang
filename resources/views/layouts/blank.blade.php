<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'SIKOMANG Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIKOMANG - Sistem Informasi dan Komunikasi Mangrove DKI Jakarta" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="https://pencil-matter-70015947.figma.site/_assets/v11/5a52c0026642845f54f76f85096c3a34c237af42.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .bg-overlay {
            position: absolute;
            height: 100%;
            width: 100%;
            right: 0;
            bottom: 0;
            left: 0;
            top: 0;
            opacity: 0.9;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .auth-logo img {
            max-height: 80px;
        }

        .input-custom-icon {
            position: relative;
        }

        .input-custom-icon .bx,
        .input-custom-icon .mdi {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.25rem;
            color: #6c757d;
        }

        .input-custom-icon input {
            padding-left: 45px;
        }

        .auth-pass-inputgroup {
            position: relative;
        }

        .auth-pass-inputgroup button {
            text-decoration: none;
            border: none;
            background: transparent;
        }

        .btn-primary {
            background: #009966;
            border-color: #009966;
        }

        .btn-primary:hover {
            background: #007a52;
            border-color: #007a52;
        }
    </style>

    @yield('css')
</head>

<body>
    @yield('content')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Show flash messages -->
    @if(session('message'))
    <script>
        Swal.fire({
            icon: '{{ session("type", "info") }}',
            title: '{{ session("type") == "success" ? "Berhasil!" : "Perhatian!" }}',
            text: '{{ session("message") }}',
            confirmButtonColor: '#009966'
        });
    </script>
    @endif

    @yield('js')
</body>
</html>
