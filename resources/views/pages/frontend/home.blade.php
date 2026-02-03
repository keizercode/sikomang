@extends('layouts.frontend.app')

@section('title', 'SIKOMANG - Sistem Informasi dan Komunikasi Mangrove DKI Jakarta')

@section('content')
    {{-- Hero Section --}}
    <section class="py-12 md:py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">
                {{-- Left Content --}}
                <div>
                    <p class="text-sm md:text-base mb-2 text-muted">Dinas Lingkungan Hidup DKI Jakarta</p>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6 text-secondary">Sistem Informasi dan Komunikasi Mangrove DKI Jakarta</h1>
                </div>

                {{-- Right Content --}}
                <div class="lg:text-left flex flex-col items-start lg:items-end">
                    <p class="text-sm md:text-base mb-6 max-w-md" style="color: #4c5250;">
                        Sikomang hadir sebagai platform pemantauan dan strategi pengendalian kerusakan ekosistem mangrove di DKI Jakarta.
                    </p>

                    {{-- Button Group --}}
                    <div class="flex flex-wrap gap-3">
                        <a
                            href="{{ route('monitoring.index') }}"
                            class="inline-flex items-center space-x-2 text-white px-6 py-3 rounded-lg font-semibold transition-all"
                            style="background-color: #009966;"
                            onmouseover="this.style.backgroundColor='#2d5c54'"
                            onmouseout="this.style.backgroundColor='#009966'"
                        >
                            <span>SIKOMANG</span>
                            <x-icons.globe-home />
                        </a>

                        <button
                            type="button"
                            onclick="openModal()"
                            class="cursor-pointer inline-flex items-center space-x-2 border border-gray-300 text-secondary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all"
                        >
                            <span>Pelajari lebih lanjut</span>
                            <x-icons.info-home />
                        </button>
                    </div>
                </div>
            </div>

            {{-- Hero Images Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-10">
                <div class="rounded-2xl overflow-hidden h-64 md:h-80">
                    <img
                        src="https://ext.same-assets.com/1105228812/3696522300.png"
                        alt="Akar Mangrove"
                        class="w-full h-full object-cover"
                    >
                </div>

                <div class="rounded-2xl overflow-hidden h-64 md:h-80">
                    <img
                        src="https://ext.same-assets.com/1105228812/3198143648.jpeg"
                        alt="Hutan Mangrove"
                        class="w-full h-full object-cover"
                    >
                </div>

                <div class="hero-gradient relative rounded-2xl p-6 md:p-8 h-64 md:h-80 flex flex-col justify-between">
                    <div class="absolute top-6 left-8 w-6 h-4">
                        <x-icons.quote-home />
                    </div>

                    <div class="pt-6">
                        <p class="text-white text-sm md:text-base leading-relaxed">
                            Meningkatkan Ketahanan Masyarakat dan Keberlanjutan Lingkungan Pesisir melalui Pengendalian Kerusakan Mangrove
                        </p>
                    </div>

                    <div class="flex justify-start">
                        <x-icons.mangrove-home />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Statistics Section --}}
    @if(isset($stats))
    <section class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                    <div class="text-3xl font-bold text-primary mb-2">{{ $stats['total_locations'] }}</div>
                    <div class="text-sm text-muted">Lokasi Pemantauan</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                    <div class="text-3xl font-bold text-primary mb-2">{{ number_format($stats['total_area'], 2) }} ha</div>
                    <div class="text-sm text-muted">Total Luas Area</div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                    <div class="text-3xl font-bold text-primary mb-2">{{ $stats['total_articles'] }}</div>
                    <div class="text-sm text-muted">Artikel Terpublikasi</div>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Articles Section --}}
    <section class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Section Header --}}
            <div class="mb-8 md:mb-12">
                <p class="text-sm mb-1" style="color: #4c5250;">Postingan</p>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold" style="color: #242621;">Artikel Terkini</h2>
            </div>

            {{-- Articles Grid --}}
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($articles as $article)
                        <x-shared.article-card :article="$article"
                            :image="$article->featured_image ? asset('storage/' . $article->featured_image) : 'https://ext.same-assets.com/1105228812/2230853859.png'"
                            :date="$article->published_at ? $article->published_at->format('d F Y') : ''"
                            :author="$article->user->name ?? 'Admin, DLH Jakarta'"
                            :title="$article->title"
                            :excerpt="$article->excerpt"
                            :link="route('articles.show', $article->slug)"
                        />
                    @endforeach
                </div>
            @else
                {{-- Fallback jika belum ada artikel --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    <x-shared.article-card
                        image="https://ext.same-assets.com/1105228812/2230853859.png"
                        date="6 Januari 2026"
                        author="Admin, DLH Jakarta"
                        title="Peran Vital Mangrove dalam Pertahanan Pesisir Jakarta"
                        excerpt="Temukan bagaimana hutan mangrove di Jakarta menjaga wilayah pesisir dari erosi dan gelombang besar."
                        link="#"
                    />
                    <x-shared.article-card
                        image="https://ext.same-assets.com/1105228812/2230853859.png"
                        date="5 Januari 2026"
                        author="Admin, DLH Jakarta"
                        title="Revolusi Energi Terbarukan: Solusi untuk Masa Depan"
                        excerpt="Jelajahi bagaimana energi terbarukan dapat mengubah lanskap energi global."
                        link="#"
                    />
                    <x-shared.article-card
                        image="https://ext.same-assets.com/1105228812/2708995451.jpeg"
                        date="5 Januari 2026"
                        author="Admin, DLH Jakarta"
                        title="Menghidupkan Tradisi: Festival Budaya Betawi"
                        excerpt="Saksikan keindahan dan kekayaan budaya Betawi melalui festival tahunan."
                        link="#"
                    />
                </div>
            @endif

            {{-- View All Button --}}
            <div class="text-center mt-10">
                <a
                    href="{{ route('articles.index') }}"
                    class="inline-flex items-center space-x-2 border-2 px-6 py-3 rounded-lg font-semibold transition-all"
                    style="border-color: #009966; color: #009966;"
                    onmouseover="this.style.backgroundColor='#009966'; this.style.color='white';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#009966';"
                >
                    <span>Lihat Semua Artikel</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- Modal Tentang Sikomang --}}
    <div id="aboutModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto relative animate-modal">
                <div class="flex items-center justify-between p-6 pb-4">
                    <h2 class="text-2xl md:text-3xl font-bold" style="color: #242621;">Tentang Sikomang</h2>
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="cursor-pointer w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 hover:bg-gray-100 transition-all"
                    >
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="px-6 pb-6">
                    <p class="text-gray-700 leading-relaxed mb-6">
                        Sikomang (Sistem Informasi dan Komunikasi Mangrove DKI Jakarta) adalah platform resmi dari Dinas Lingkungan Hidup DKI Jakarta yang dikembangkan untuk:
                    </p>

                    <ul class="space-y-4 mb-6">
                        <li class="flex items-start space-x-3">
                            <span class="text-xl">📊</span>
                            <span class="text-gray-700">Memantau kondisi ekosistem mangrove di wilayah pesisir DKI Jakarta.</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <span class="text-xl">🌱</span>
                            <span class="text-gray-700">Mendukung konservasi dan rehabilitasi hutan mangrove.</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <span class="text-xl">👥</span>
                            <span class="text-gray-700">Menghubungkan masyarakat, komunitas, dan pemerintah dalam menjaga keberlanjutan lingkungan pesisir.</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <span class="text-xl">🎯</span>
                            <span class="text-gray-700">Menyediakan edukasi dan informasi terkini mengenai mangrove serta dampaknya terhadap ketahanan lingkungan.</span>
                        </li>
                    </ul>

                    <p class="text-gray-700 leading-relaxed">
                        Dengan adanya Sikomang, diharapkan masyarakat dapat lebih mudah berpartisipasi dalam perlindungan dan pengendalian kerusakan ekosistem mangrove.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-modal {
        animation: modalFadeIn 0.2s ease-out forwards;
    }
</style>
@endpush

@push('scripts')
<script>
    function openModal() {
        const modal = document.getElementById('aboutModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('aboutModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush
