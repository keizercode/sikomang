@extends('layouts.frontend.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/article-frontend.css') }}">
<style>
    .article-header {
        background: linear-gradient(135deg, #009966 0%, #00664d 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }

    .article-meta {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.9);
        margin-top: 20px;
    }

    .article-meta i {
        margin-right: 5px;
    }

    .article-featured-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .article-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .share-buttons {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e5e7eb;
    }

    .share-button {
        display: inline-block;
        padding: 10px 20px;
        margin-right: 10px;
        border-radius: 6px;
        text-decoration: none;
        color: white;
        font-weight: 500;
        transition: all 0.3s;
    }

    .share-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .share-facebook { background-color: #1877f2; }
    .share-twitter { background-color: #1da1f2; }
    .share-whatsapp { background-color: #25d366; }
    .share-linkedin { background-color: #0077b5; }

    @media (max-width: 768px) {
        .article-container {
            padding: 20px;
        }

        .article-header {
            padding: 40px 0;
        }
    }
</style>
@endsection

@section('content')
<div class="article-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h1 class="display-4 font-weight-bold mb-0">{{ $article->title }}</h1>
                <div class="article-meta">
                    <span><i class="far fa-calendar"></i> {{ $article->published_at ? $article->published_at->format('d F Y') : $article->created_at->format('d F Y') }}</span>
                    <span class="mx-3">•</span>
                    <span><i class="far fa-user"></i> {{ $article->author->name ?? 'Admin' }}</span>
                    @if($article->category)
                    <span class="mx-3">•</span>
                    <span><i class="far fa-folder"></i> {{ $article->category->name }}</span>
                    @endif
                    <span class="mx-3">•</span>
                    <span><i class="far fa-clock"></i> {{ $article->read_time ?? '5' }} menit baca</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            @if($article->featured_image)
            <img src="{{ asset('storage/' . $article->featured_image) }}"
                 alt="{{ $article->title }}"
                 class="article-featured-image">
            @endif

            <div class="article-container">
                @if($article->excerpt)
                <div class="lead mb-4" style="font-size: 1.25rem; color: #4b5563; line-height: 1.7;">
                    {{ $article->excerpt }}
                </div>
                @endif

                <div class="article-content">
                    {!! $article->content !!}
                </div>

                <!-- Share Buttons -->
                <div class="share-buttons">
                    <h5 class="mb-3">Bagikan Artikel Ini:</h5>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank"
                       class="share-button share-facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}"
                       target="_blank"
                       class="share-button share-twitter">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
                       target="_blank"
                       class="share-button share-whatsapp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}"
                       target="_blank"
                       class="share-button share-linkedin">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                </div>
            </div>

            <!-- Related Articles (Optional) -->
            @if(isset($relatedArticles) && $relatedArticles->count() > 0)
            <div class="mt-5">
                <h3 class="mb-4">Artikel Terkait</h3>
                <div class="row">
                    @foreach($relatedArticles as $related)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            @if($related->featured_image)
                            <img src="{{ asset('storage/' . $related->featured_image) }}"
                                 class="card-img-top"
                                 alt="{{ $related->title }}"
                                 style="height: 200px; object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('articles.show', $related->slug) }}"
                                       class="text-dark text-decoration-none">
                                        {{ $related->title }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($related->excerpt, 100) }}
                                </p>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <small class="text-muted">
                                    <i class="far fa-calendar"></i>
                                    {{ $related->published_at ? $related->published_at->format('d M Y') : $related->created_at->format('d M Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Smooth scroll for anchor links
    $('.article-content a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });

    // Add copy button to code blocks
    $('.article-content pre').each(function() {
        var code = $(this);
        var copyButton = $('<button class="btn btn-sm btn-secondary copy-code" style="position: absolute; top: 10px; right: 10px;">Copy</button>');
        code.css('position', 'relative');
        code.append(copyButton);

        copyButton.on('click', function() {
            var text = code.text().replace('Copy', '');
            navigator.clipboard.writeText(text).then(function() {
                copyButton.text('Copied!');
                setTimeout(function() {
                    copyButton.text('Copy');
                }, 2000);
            });
        });
    });
});
</script>
@endsection
