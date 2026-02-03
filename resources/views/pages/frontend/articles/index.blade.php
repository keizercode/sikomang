@extends('layouts.frontend.app')

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
            <form method="GET" class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="relative w-full md:w-auto">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari artikel..."
                        class="w-full md:w-64 px-4 py-2 pl-10 border border-gray-200 rounded-lg"
                    >
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <button type="submit" class="btn btn-primary">
                    Cari
                </button>
            </form>
        </div>
    </section>

    {{-- Articles List --}}
    <section class="py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach($articles as $article)
                        <x-shared.article-card
                            :image="$article->featured_image ? asset('storage/' . $article->featured_image) : 'https://via.placeholder.com/400x300'"
                            :date="$article->published_at ? $article->published_at->format('d M Y') : ''"
                            :author="$article->user->name ?? 'Admin'"
                            :title="$article->title"
                            :excerpt="$article->excerpt"
                            :link="route('articles.show', $article->slug)"
                        />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="flex justify-center mt-12">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-muted text-lg">
                        @if(request()->has('search'))
                            Tidak ada artikel yang sesuai dengan pencarian
                        @else
                            Belum ada artikel yang dipublikasikan
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
