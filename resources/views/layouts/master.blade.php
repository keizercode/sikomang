<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', @$title) | SIKOMANG Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="SIKOMANG Admin Panel" name="description" />
    <meta content="DLH Jakarta" name="author" />
    <link rel="icon" type="image/png" href="https://pencil-matter-70015947.figma.site/_assets/v11/5a52c0026642845f54f76f85096c3a34c237af42.png">

    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/select2.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap-table.min.css')}}">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/alertifyjs/build/css/alertify.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/alertifyjs/build/css/themes/default.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />

    @yield('css')
</head>
<body>
    <div id="layout-wrapper">
        @include('admin.layouts.topbar')
        @include('admin.layouts.sidebar')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © SIKOMANG Admin.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Dinas Lingkungan Hidup Provinsi DKI Jakarta
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/libs/metismenujs/metismenujs.min.js')}}"></script>
    <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/libs/eva-icons/eva.min.js')}}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{asset('assets/js/bootstrap-table.min.js')}}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/alertifyjs/build/alertify.min.js') }}"></script>
    <script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>

    @yield('js')

    <script>
        $('.select2').select2();
        @if (Session::get('error'))
            alertify.error("{{ Session::get('error') }}");
        @endif
        @if (Session::get('success'))
            alertify.success("{{ Session::get('success') }}");
        @endif
        @if (Session::get('message'))
            @if (Session::get('type') == 'success')
                alertify.success("{{ Session::get('message') }}");
            @elseif (Session::get('type') == 'error')
                alertify.error("{{ Session::get('message') }}");
            @else
                alertify.warning("{{ Session::get('message') }}");
            @endif
        @endif
    </script>
</body>
</html>
