@extends('layouts.frontend.app')

@section('title', $article->title . ' - SIKOMANG')

@push('styles')

<style>
    /* Additional inline styles for article page */
    .prose {
        max-width: 65ch;
    }

    .prose p {
        margin-bottom: 1.5em;
    }

    .prose h1, .prose h2, .prose h3, .prose h4 {
        margin-top: 2em;
        margin-bottom: 1em;
    }

    .prose ul, .prose ol {
        margin: 1.5em 0;
        padding-left: 1.5em;
    }

    .prose li {
        margin: 0.5em 0;
    }

    .prose img {
        margin: 2em 0;
        border-radius: 0.75rem;
    }
</style>
@endpush

@section('content')
    <!-- Article Header -->
    <section class="py-8 md:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Title -->
            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-secondary leading-tight mb-6">
                {{ $article->title }}
            </h1>

            <!-- Meta -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-muted mb-8">
                <div class="flex items-center space-x-2">
                    <span class="font-medium text-secondary">{{ $article->user->name ?? 'Admin' }}</span>
                </div>
                <span class="text-gray-300">|</span>
                <div class="flex items-center space-x-1">
                    <span>{{ $article->published_at ? $article->published_at->format('d F Y') : '' }}</span>
                </div>
                <span class="text-gray-300">|</span>
                <div class="flex items-center space-x-1">
                    <span>{{ number_format($article->views) }} dibaca</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Image -->
    @if($article->featured_image)
    <section class="mb-8 md:mb-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl overflow-hidden">
                <img
                    src="{{ asset('storage/' . $article->featured_image) }}"
                    alt="{{ $article->title }}"
                    class="w-full h-64 md:h-96 object-cover"
                >
            </div>
        </div>
    </section>
    @endif

    <!-- Article Content -->
    <section class="pb-12 md:pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="article-content prose prose-lg max-w-none">
                {!! $article->formatted_content !!}
            </article>
        </div>
    </section>

    <!-- Related Articles -->
    @if($relatedArticles->count() > 0)
    <section class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl md:text-3xl font-bold text-secondary mb-8">Artikel Terkait</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedArticles as $related)
                    <x-shared.article-card
                        :image="$related->featured_image ? asset('storage/' . $related->featured_image) : 'https://via.placeholder.com/400x300'"
                        :date="$related->published_at ? $related->published_at->format('d F Y') : ''"
                        :author="$related->user->name ?? 'Admin'"
                        :title="$related->title"
                        :excerpt="$related->excerpt"
                        :link="route('articles.show', $related->slug)"
                    />
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
