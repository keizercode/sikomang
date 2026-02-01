<header id="page-topbar" class="isvertical-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">

                    <span class="logo-lg">
                        <img src="https://pencil-matter-70015947.figma.site/_assets/v11/5a52c0026642845f54f76f85096c3a34c237af42.png" alt="" height="26">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect vertical-menu-btn">
                <i class="bx bx-menu align-middle"></i>
            </button>

            <!-- Page title -->
            <div class="page-title-box align-self-center d-none d-md-block">
                <h4 class="page-title mb-0">{{@$title}}</h4>
            </div>
        </div>

        <div class="d-flex">
            <!-- User Dropdown -->
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item user text-start d-flex align-items-center"
                        id="page-header-user-dropdown-v" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img
                    src="https://res.cloudinary.com/dmcvht1vr/image/upload/v1769914005/user_xq4ytt.png"
                    class="rounded-circle header-profile-user"
                    alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-2 fw-medium font-size-15">
                        {{ session('name') ?? 'Admin' }}
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end pt-0">
                    <div class="p-3 border-bottom">
                        <h6 class="mb-0">{{ session('name') ?? 'Admin' }}</h6>
                        <p class="mb-0 font-size-11 text-muted">{{ session('email') ?? '' }}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('admin.profile') ?? '#' }}">
                        <i class="mdi mdi-account-circle text-muted font-size-16 align-middle me-2"></i>
                        <span class="align-middle">Profile</span>
                    </a>
                   <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                        <i class="mdi mdi-cog text-muted font-size-16 align-middle me-2"></i>
                        <span class="align-middle">Pengaturan</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                    <i class="mdi mdi-logout text-muted font-size-16 align-middle me-2"></i>
                    <span class="align-middle">Logout</span>
                    </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</header>
