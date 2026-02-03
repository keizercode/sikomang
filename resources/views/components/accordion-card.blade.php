@props(['id', 'title', 'icon' => null])

<div class="card">
    <button type="button" class="accordion-header" onclick="window.toggleAccordion('{{ $id }}')">
        <h2 class="card-title" style="margin: 0;">
            @if($icon) {!! $icon !!} @endif
            {{ $title }}
        </h2>
        <svg class="accordion-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div class="accordion-content" id="accordion-{{ $id }}">
        {{ $slot }}
    </div>
</div>
