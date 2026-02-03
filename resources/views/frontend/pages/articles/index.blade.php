@extends('layouts.app')

@section('title', 'Artikel - SIKOMANG')

@section('content')
    {{-- Page Header --}}
    <section class="py-12 md:py-16 bg-gradient-to-br from-primary/5 to-primary/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-secondary mb-4">
                    Artikel
                </h1>
                <p class="text-muted text-lg max-w-2xl mx-auto">
                    Berita dan informasi terkini seputar ekosistem mangrove DKI Jakarta.
                </p>
            </div>
        </div>
    </section>

    {{-- Search & Filter --}}
    <section class="py-8 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center space-x-4">
                    <select class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option>Semua Kategori</option>
                        <option>Konservasi</option>
                        <option>Edukasi</option>
                        <option>Kegiatan</option>
                        <option>Berita</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option>Urutkan: Terbaru</option>
                        <option>Urutkan: Terlama</option>
                        <option>Paling Populer</option>
                    </select>
                </div>
                <div class="relative w-full md:w-auto">
                    <input
                        type="text"
                        placeholder="Cari artikel..."
                        class="w-full md:w-64 px-4 py-2 pl-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    {{-- Articles List --}}
    <section class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                @foreach([
                    [
                        'image' => 'https://ext.same-assets.com/1105228812/2436896300.png',
                        'date' => '15 November 2024',
                        'title' => 'Peran Vital Mangrove dalam Pertahanan Pesisir Jakarta',
                        'excerpt' => 'Temukan bagaimana hutan mangrove di Jakarta menjaga wilayah pesisir dari erosi dan gelombang besar, mendukung keanekaragaman hayati, dan menopang kehidupan masyarakat lokal.'
                    ],
                    [
                        'image' => 'https://ext.same-assets.com/1105228812/2230853859.png',
                        'date' => '20 November 2024',
                        'title' => 'Revolusi Energi Terbarukan: Solusi untuk Masa Depan',
                        'excerpt' => 'Jelajahi bagaimana energi terbarukan dapat mengubah lanskap energi global dan mengurangi ketergantungan pada bahan bakar fosil, serta dampaknya terhadap perubahan iklim.'
                    ],
                    [
                        'image' => 'https://ext.same-assets.com/1105228812/2708995451.jpeg',
                        'date' => '25 November 2024',
                        'title' => 'Menghidupkan Tradisi: Festival Budaya Betawi',
                        'excerpt' => 'Saksikan keindahan dan kekayaan budaya Betawi melalui festival tahunan yang menampilkan seni, kuliner, dan tradisi yang telah diwariskan dari generasi ke generasi.'
                    ],
                    [
                        'image' => 'https://ext.same-assets.com/1105228812/3696522300.png',
                        'date' => '28 November 2024',
                        'title' => 'Program Penanaman 10.000 Bibit Mangrove di Jakarta Utara',
                        'excerpt' => 'DLH DKI Jakarta bersama komunitas berhasil menanam 10.000 bibit mangrove di kawasan pesisir Jakarta Utara sebagai upaya rehabilitasi ekosistem.'
                    ],
                    [
                        'image' => 'https://ext.same-assets.com/1105228812/3198143648.jpeg',
                        'date' => '1 Desember 2024',
                        'title' => 'Wisata Edukasi Mangrove: Destinasi Baru di Jakarta',
                        'excerpt' => 'Kawasan konservasi mangrove kini dibuka untuk wisata edukasi, memberikan pengalaman belajar langsung tentang pentingnya ekosistem pesisir.'
                    ],
                    [
                        'image' => 'https://ext.same-assets.com/1105228812/2436896300.png',
                        'date' => '5 Desember 2024',
                        'title' => 'Workshop Pengolahan Produk Mangrove untuk UMKM',
                        'excerpt' => 'Pelatihan pengolahan buah dan daun mangrove menjadi produk bernilai ekonomi tinggi bagi pelaku UMKM di wilayah pesisir Jakarta.'
                    ],
                ] as $article)
                <x-article-card
                    :image="$article['image']"
                    :date="$article['date']"
                    author="Admin, DLH Jakarta"
                    :title="$article['title']"
                    :excerpt="$article['excerpt']"
                    link="#"
                />
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="flex justify-center mt-12">
                <nav class="flex items-center space-x-2">
                    <button class="px-3 py-2 rounded-lg border border-gray-200 text-muted hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button class="px-4 py-2 rounded-lg bg-primary text-white font-medium">1</button>
                    <button class="px-4 py-2 rounded-lg border border-gray-200 text-muted hover:bg-gray-50">2</button>
                    <button class="px-4 py-2 rounded-lg border border-gray-200 text-muted hover:bg-gray-50">3</button>
                    <span class="px-2 text-muted">...</span>
                    <button class="px-4 py-2 rounded-lg border border-gray-200 text-muted hover:bg-gray-50">10</button>
                    <button class="px-3 py-2 rounded-lg border border-gray-200 text-muted hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </section>
@endsection
