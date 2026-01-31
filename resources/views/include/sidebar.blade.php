<div class="vertical-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="https://pencil-matter-70015947.figma.site/_assets/v11/5a52c0026642845f54f76f85096c3a34c237af42.png" alt="" height="26">
            </span>
            <span class="logo-lg">
                <div class="d-flex align-items-center gap-3">
                    <div><img src="https://pencil-matter-70015947.figma.site/_assets/v11/5a52c0026642845f54f76f85096c3a34c237af42.png" alt="" height="28"></div>
                    <div><h3 class="mb-0 logo-md">SIKOMANG</h3></div>
                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
                        <i class="bx bx-menu align-middle"></i>
                    </button>
                </div>
            </span>
        </a>
    </div>

    <div data-simplebar class="sidebar-menu-scroll">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Monitoring Mangrove -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-map"></i>
                        <span>Monitoring Mangrove</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.monitoring.index') }}">Semua Lokasi</a></li>
                        <li><a href="{{ route('admin.monitoring.create') }}">Tambah Lokasi</a></li>
                        <li><a href="{{ route('admin.monitoring.damages') }}">Data Kerusakan</a></li>
                        <li><a href="{{ route('admin.monitoring.reports') }}">Laporan Monitoring</a></li>
                    </ul>
                </li>

                <!-- Content Management -->
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-file"></i>
                        <span>Konten</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.articles.index') }}">Artikel</a></li>
                        <li><a href="{{ route('admin.gallery.index') }}">Galeri</a></li>
                    </ul>
                </li>

                <!-- Management -->
                <li class="menu-title">Manajemen</li>

                <li>
                    <a href="{{ route('admin.users.index') }}" class="waves-effect">
                        <i class="bx bx-user"></i>
                        <span>Manajemen User</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.settings.index') }}" class="waves-effect">
                        <i class="bx bx-cog"></i>
                        <span>Pengaturan</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
