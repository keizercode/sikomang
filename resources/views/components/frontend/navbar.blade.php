<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 md:h-20">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img
                    src="https://res.cloudinary.com/dmcvht1vr/image/upload/v1770193394/logo_sikomang_ao9jdj.png"
                    alt="SIKOMANG Logo"
                    class="h-10 w-10 md:h-12 md:w-12"
                >

                {{-- Vertical Divider --}}
                <div class="h-12 w-px bg-gray-300"></div>

                {{-- Text --}}
                <div class="hidden sm:block">
                    <h1 class="text-base md:text-lg font-bold" style="color:#147C45">SIKOMANG</h1>
                    <p class="text-[10px] md:text-xs leading-tight text-muted">
                        Sistem Informasi dan Komunikasi<br>Mangrove DKI Jakarta
                    </p>
                </div>
            </a>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });
</script>
