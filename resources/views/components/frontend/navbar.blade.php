<nav class="bg-white border-b border-gray-100 sticky top-0 z-50"
     style="box-shadow: 0 1px 12px rgba(0,0,0,0.06);">

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 min-h-[64px] md:h-[100px] relative">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                <img
                    src="https://pencil-matter-70015947.figma.site/_assets/v11/e6fb723ab1735b08498a0a60cab1e81508afa7e6.png"
                    alt="SIKOMANG Logo"
                    class="h-8 w-8 sm:h-10 sm:w-10 md:h-12 md:w-12"
                >

                {{-- Vertical Divider --}}
                <div class="hidden sm:block h-10 md:h-13 w-px bg-gray-300"></div>

                {{-- Text --}}
                <div class="hidden sm:block">
                    <h1 class="text-sm md:text-lg font-bold" style="color:#147C45">SIKOMANG</h1>
                    <p class="text-[9px] md:text-xs leading-tight text-muted">
                        Sistem Informasi dan Komunikasi<br>Mangrove DKI Jakarta
                    </p>
                </div>

                {{-- Mobile: show text inline --}}
                <span class="block sm:hidden font-bold text-sm" style="color:#147C45">SIKOMANG</span>
            </a>

            {{-- Left slot (search) --}}
            <div class="flex items-center min-w-0 flex-1">
                @stack('navbar-left')
            </div>

            {{-- Right slot (actions) --}}
            <div class="flex items-center flex-shrink-0">
                @stack('navbar-right')
            </div>
        </div>
    </div>

    {{-- Mobile menu toggle support --}}
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
        });
    </script>
</nav>
