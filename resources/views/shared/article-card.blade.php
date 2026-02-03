@props([
    'image' => '',
    'date' => '',
    'author' => 'Admin, DLH Jakarta',
    'title' => '',
    'excerpt' => '',
    'link' => '#'
])

<article class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300">
    <a href="{{ $link }}" class="block">
        <div class="aspect-[4/3] overflow-hidden bg-gray-100">
            <img
                src="{{ $image }}"
                alt="{{ $title }}"
                loading="lazy"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'"
            >
        </div>

        <div class="p-5">
            <div class="flex items-center gap-2 text-sm text-muted mb-3">
                <time datetime="{{ $date }}">{{ $date }}</time>
                <span class="text-gray-300">|</span>
                <div class="flex items-center gap-1">
                    <x-icons.user class="text-secondary" />
                    <span class="font-medium text-secondary">{{ $author }}</span>
                </div>
            </div>

            <h3 class="text-lg font-bold text-secondary mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                {{ $title }}
            </h3>

            <p class="text-sm text-muted leading-relaxed mb-4 line-clamp-4">
                {{ $excerpt }}
            </p>

            <span class="inline-flex items-center text-primary font-semibold text-sm group-hover:gap-2 transition-all">
                Selengkapnya
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </div>
    </a>
</article>
