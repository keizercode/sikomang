<div class="vertical-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-lg">
                <div class="d-flex align-items-center gap-2">
                    <img src="https://pencil-matter-70015947.figma.site/_assets/v11/e6fb723ab1735b08498a0a60cab1e81508afa7e6.png" alt="SIKOMANG Logo" height="32">
                    <h3 class="mb-0 logo-md" style="color:#147C45;">SIKOMANG</h3>

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
                        <li><a href="{{ route('admin.galleries.index') }}">Galeri</a></li>
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

<!-- Menu Laporan Masyarakat -->
<li class="nav-item">
    <a class="nav-link {{ Request::is('admin/public-reports*') ? 'active' : '' }}"
       href="{{ route('admin.public-reports.index') }}">
        <i class="mdi mdi-alert-circle-outline"></i>
        <span>Laporan Masyarakat</span>
        @php
            $pendingCount = \App\Models\PublicReport::where('status', 'pending')->count();
        @endphp
        @if($pendingCount > 0)
            <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingCount }}</span>
        @endif
    </a>
</li>

                <li>
                    {{-- <a href=
                    "{{ route('admin.settings.index') }}" class="waves-effect">
                        <i class="bx bx-cog"></i>
                        <span>Pengaturan</span>
                    </a> --}}
                </li>
            </ul>
        </div>
    </div>
</div>
