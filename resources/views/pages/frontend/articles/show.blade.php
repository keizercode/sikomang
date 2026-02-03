@extends('layouts.app')

@section('title', 'Peran Vital Mangrove dalam Pertahanan Pesisir Jakarta - SIKOMANG')

@section('content')
    {{-- Article Header --}}
    <section class="py-8 md:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center space-x-2 text-sm text-muted mb-6">
                <a href="{{ route('home') }}" class="hover:text-primary">Beranda</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('articles.index') }}" class="hover:text-primary">Artikel</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-secondary">Peran Vital Mangrove</span>
            </nav>

            {{-- Category Badge --}}
            <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                Konservasi
            </span>

            {{-- Title --}}
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-secondary leading-tight mb-6">
                Peran Vital Mangrove dalam Pertahanan Pesisir Jakarta
            </h1>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-4 text-sm text-muted mb-8">
                <div class="flex items-center space-x-2">
                    <img
                        src="https://ui-avatars.com/api/?name=Admin+DLH&background=418276&color=fff&size=32"
                        alt="Admin"
                        class="w-8 h-8 rounded-full"
                    >
                    <span class="font-medium text-secondary">Admin, DLH Jakarta</span>
                </div>
                <span class="text-gray-300">|</span>
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>15 November 2024</span>
                </div>
                <span class="text-gray-300">|</span>
                <div class="flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>1.2K dibaca</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Image --}}
    <section class="mb-8 md:mb-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl overflow-hidden">
                <img
                    src="https://ext.same-assets.com/1105228812/2436896300.png"
                    alt="Peran Vital Mangrove"
                    class="w-full h-64 md:h-96 object-cover"
                >
            </div>
        </div>
    </section>

    {{-- Article Content --}}
    <section class="pb-12 md:pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="prose prose-lg max-w-none">
                <p class="lead text-xl text-muted leading-relaxed mb-6">
                    Hutan mangrove di Jakarta memainkan peran yang sangat penting dalam menjaga keberlanjutan ekosistem pesisir. Sebagai benteng alami, mangrove melindungi garis pantai dari abrasi, gelombang pasang, dan intrusi air laut.
                </p>

                <h2 class="text-2xl font-bold text-secondary mt-8 mb-4">Fungsi Ekologis Mangrove</h2>
                <p class="text-muted leading-relaxed mb-4">
                    Ekosistem mangrove menyediakan berbagai layanan ekosistem yang tidak ternilai harganya. Akar-akar mangrove yang kompleks berfungsi sebagai tempat berkembang biak bagi berbagai spesies ikan, udang, dan kepiting yang menjadi sumber protein bagi masyarakat pesisir.
                </p>
                <p class="text-muted leading-relaxed mb-4">
                    Selain itu, hutan mangrove juga berperan sebagai penyerap karbon dioksida yang sangat efektif. Penelitian menunjukkan bahwa mangrove dapat menyerap karbon hingga 4-5 kali lebih banyak dibandingkan hutan daratan pada luas yang sama.
                </p>

                <h2 class="text-2xl font-bold text-secondary mt-8 mb-4">Perlindungan Pesisir</h2>
                <p class="text-muted leading-relaxed mb-4">
                    Struktur akar mangrove yang unik mampu meredam energi gelombang hingga 75%, memberikan perlindungan signifikan terhadap erosi pantai. Di Jakarta, dimana ancaman banjir rob semakin meningkat, keberadaan mangrove menjadi sangat krusial.
                </p>

                <blockquote class="border-l-4 border-primary pl-6 my-8 italic text-lg text-secondary">
                    "Mangrove adalah garda terdepan dalam melindungi wilayah pesisir Jakarta dari dampak perubahan iklim dan kenaikan permukaan air laut."
                    <footer class="text-sm text-muted mt-2">- Kepala DLH DKI Jakarta</footer>
                </blockquote>

                <h2 class="text-2xl font-bold text-secondary mt-8 mb-4">Manfaat Ekonomi</h2>
                <p class="text-muted leading-relaxed mb-4">
                    Masyarakat pesisir Jakarta telah lama memanfaatkan ekosistem mangrove untuk berbagai keperluan. Dari hasil tangkapan ikan dan udang, hingga pengolahan buah mangrove menjadi produk makanan dan minuman yang bernilai ekonomi tinggi.
                </p>
                <p class="text-muted leading-relaxed mb-4">
                    Program pemberdayaan masyarakat yang diinisiasi oleh Dinas Lingkungan Hidup DKI Jakarta telah berhasil meningkatkan pendapatan masyarakat pesisir melalui pengembangan ekowisata mangrove dan produk olahan berbasis mangrove.
                </p>

                <h2 class="text-2xl font-bold text-secondary mt-8 mb-4">Tantangan dan Solusi</h2>
                <p class="text-muted leading-relaxed mb-4">
                    Meskipun memiliki banyak manfaat, ekosistem mangrove di Jakarta menghadapi berbagai ancaman, termasuk reklamasi, pencemaran, dan konversi lahan. Upaya konservasi dan rehabilitasi terus dilakukan melalui berbagai program penanaman dan pemantauan.
                </p>
                <p class="text-muted leading-relaxed mb-4">
                    SIKOMANG hadir sebagai platform untuk memfasilitasi pemantauan kondisi mangrove secara real-time dan mengkoordinasikan upaya pelestarian antara pemerintah, masyarakat, dan pemangku kepentingan lainnya.
                </p>
            </article>

            {{-- Tags --}}
            <div class="flex flex-wrap gap-2 mt-8 pt-8 border-t border-gray-100">
                <span class="text-sm text-muted mr-2">Tags:</span>
                <a href="#" class="px-3 py-1 bg-gray-100 text-sm text-secondary rounded-full hover:bg-primary hover:text-white transition-all">Mangrove</a>
                <a href="#" class="px-3 py-1 bg-gray-100 text-sm text-secondary rounded-full hover:bg-primary hover:text-white transition-all">Konservasi</a>
                <a href="#" class="px-3 py-1 bg-gray-100 text-sm text-secondary rounded-full hover:bg-primary hover:text-white transition-all">Jakarta</a>
                <a href="#" class="px-3 py-1 bg-gray-100 text-sm text-secondary rounded-full hover:bg-primary hover:text-white transition-all">Pesisir</a>
            </div>

            {{-- Share --}}
            <div class="flex items-center justify-between mt-8 pt-8 border-t border-gray-100">
                <span class="text-sm text-muted">Bagikan artikel ini:</span>
                <div class="flex items-center space-x-3">
                    <a href="#" class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center hover:bg-blue-600 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-sky-500 text-white rounded-full flex items-center justify-center hover:bg-sky-600 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <button class="w-10 h-10 bg-gray-200 text-secondary rounded-full flex items-center justify-center hover:bg-gray-300 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Related Articles --}}
    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-secondary">Artikel Terkait</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-article-card
                    image="https://ext.same-assets.com/1105228812/2230853859.png"
                    date="20 November 2024"
                    author="Admin, DLH Jakarta"
                    title="Revolusi Energi Terbarukan: Solusi untuk Masa Depan"
                    excerpt="Jelajahi bagaimana energi terbarukan dapat mengubah lanskap energi global dan mengurangi ketergantungan pada bahan bakar fosil."
                    link="#"
                />

                <x-article-card
                    image="https://ext.same-assets.com/1105228812/3696522300.png"
                    date="28 November 2024"
                    author="Admin, DLH Jakarta"
                    title="Program Penanaman 10.000 Bibit Mangrove di Jakarta Utara"
                    excerpt="DLH DKI Jakarta bersama komunitas berhasil menanam 10.000 bibit mangrove di kawasan pesisir Jakarta Utara."
                    link="#"
                />

                <x-article-card
                    image="https://ext.same-assets.com/1105228812/3198143648.jpeg"
                    date="1 Desember 2024"
                    author="Admin, DLH Jakarta"
                    title="Wisata Edukasi Mangrove: Destinasi Baru di Jakarta"
                    excerpt="Kawasan konservasi mangrove kini dibuka untuk wisata edukasi, memberikan pengalaman belajar langsung."
                    link="#"
                />
            </div>
        </div>
    </section>
@endsection
