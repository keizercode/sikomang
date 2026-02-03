@extends('layouts.app')

@section('title', 'Galeri - SIKOMANG')

@section('content')
    {{-- Page Header --}}
    <section class="py-12 md:py-16 bg-gradient-to-br from-primary/5 to-primary/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-secondary mb-4">
                    Galeri Mangrove
                </h1>
                <p class="text-muted text-lg max-w-2xl mx-auto">
                    Dokumentasi visual ekosistem mangrove DKI Jakarta
                </p>
            </div>
        </div>
    </section>

    {{-- Filter Section --}}
    <section class="py-8 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="GET" class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <select name="category" class="px-4 py-2 border border-gray-200 rounded-lg" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="location_id" class="px-4 py-2 border border-gray-200 rounded-lg" onchange="this.form.submit()">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="relative w-full md:w-auto">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari foto..."
                        class="w-full md:w-64 px-4 py-2 pl-10 border border-gray-200 rounded-lg"
                    >
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </form>
        </div>
    </section>

    {{-- Gallery Grid --}}
    <section class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($galleries->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($galleries as $gallery)
                        <a href="{{ route('gallery.show', $gallery) }}"
                           class="group block rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
                            <div class="aspect-square overflow-hidden bg-gray-100">
                                <img
                                    src="{{ asset('storage/' . $gallery->image_path) }}"
                                    alt="{{ $gallery->title }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    loading="lazy"
                                >
                            </div>
                            <div class="p-4 bg-white">
                                <h3 class="font-semibold text-secondary group-hover:text-primary transition-colors mb-1">
                                    {{ Str::limit($gallery->title, 40) }}
                                </h3>
                                @if($gallery->location)
                                    <p class="text-sm text-muted">
                                        📍 {{ Str::limit($gallery->location->name, 30) }}
                                    </p>
                                @endif
                                <span class="inline-block mt-2 text-xs px-2 py-1 bg-primary/10 text-primary rounded">
                                    {{ $gallery->category_label }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $galleries->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-muted text-lg">Tidak ada foto yang ditemukan</p>
                </div>
            @endif
        </div>
    </section>
@endsection
