@extends('layouts.frontend.app')

@section('title', $location['name'] . ' - Detail Lokasi - SIKOMANG')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

@vite([
    'resources/css/detail-lokasi.css',
    'resources/css/accordion.css',
    'resources/js/detail-lokasi.js',
    'resources/js/image-modal.js',

])
@endpush

@section('content')
<div class="detail-container">
    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        @foreach([
            ['route' => 'home', 'label' => 'Beranda'],
            ['route' => 'monitoring', 'label' => 'Monitoring'],
            ['route' => 'hasil-pemantauan', 'label' => 'Hasil Pemantauan']
        ] as $crumb)
            <a href="{{ route($crumb['route']) }}">{{ $crumb['label'] }}</a>
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
            </svg>
        @endforeach
        <span>{{ $location['name'] }}</span>
    </nav>

    {{-- Header --}}
    <div class="detail-header">
        <div class="title-section">
            <h1>{{ $location['name'] }}</h1>
            <div class="badges">
                <span class="badge badge-{{ strtolower($location['type']) }}">{{ $location['type'] }}</span>
                <span class="badge badge-year">{{ $location['year'] }}</span>
            </div>
        </div>

        <div class="action-buttons">
            @foreach([
                ['onclick' => 'window.print()', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', 'label' => 'Data', 'class' => 'btn-primary'],
                ['onclick' => 'generateReport()', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Report', 'class' => 'btn-secondary']
            ] as $btn)
                <button class="btn {{ $btn['class'] }}" onclick="{{ $btn['onclick'] }}">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $btn['icon'] }}"/>
                    </svg>
                    <span>{{ $btn['label'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid">
        {{-- Main Content --}}
        <div class="main-content">
            {{-- Map Card --}}
            <div class="card">
                <h2 class="card-title">Peta Lokasi</h2>
                <div id="detailMap"></div>
            </div>

            {{-- Informasi Lokasi --}}
            <div class="card">
                <h2 class="card-title">Informasi Lokasi Kawasan</h2>
                <x-components.info-grid :items="[
                    'Koordinat' => $location['coords'],
                    'Luas Area' => $location['area'],
                    'Kerapatan' => $location['density'],
                    'Kesehatan Mangrove' => $location['health'],
                    'Serapan Karbon' => $location['carbon_data'],
                    'Pengelola' => $location['manager']
                ]" />

                @foreach([
                    ['label' => 'Lokasi', 'value' => $location['location']],
                    ['label' => 'Species', 'value' => $location['species']],
                    ['label' => 'Deskripsi', 'value' => $location['description'], 'isParagraph' => true]
                ] as $info)
                    <div style="margin-top: 1.5rem;">
                        <div class="info-item">
                            <span class="info-label">{{ $info['label'] }}</span>
                            @if(isset($info['isParagraph']))
                                <p style="color: #4b5563; line-height: 1.6; margin-top: 0.5rem;">{{ $info['value'] }}</p>
                            @else
                                <span class="info-value">{{ $info['value'] }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Kerusakan & Aksi --}}
            @if(isset($location['damages']) && count($location['damages']) > 0)
                @foreach([
                    ['title' => 'Kerusakan Teridentifikasi', 'items' => $location['damages'], 'class' => 'damage'],
                    ['title' => 'Aksi Penanganan', 'items' => $location['actions'] ?? [], 'class' => 'action']
                ] as $section)
                    @if(count($section['items']) > 0)
                    <div class="card">
                        <h2 class="card-title">{{ $section['title'] }}</h2>
                        <ul class="{{ $section['class'] }}-list">
                            @foreach($section['items'] as $index => $item)
                            <li class="{{ $section['class'] }}-item">
                                <span class="{{ $section['class'] }}-number">{{ $index + 1 }}.</span>
                                <span>{{ $item }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                @endforeach
            @endif

            {{-- Accordion Sections --}}
            @if(isset($location['species_detail']))
                <x-components.accordion-card id="species" title="Spesies (Vegetasi dan Fauna)">
                    @foreach(['vegetasi' => 'Vegetasi', 'fauna' => 'Fauna'] as $key => $label)
                        @if(isset($location['species_detail'][$key]))
                        <div class="species-section">
                            <h3 class="species-subtitle">{{ $label }}:</h3>
                            <ul class="species-list">
                                @foreach($location['species_detail'][$key] as $item)
                                <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    @endforeach
                </x-components.accordion-card>
            @endif

            @foreach([
                ['key' => 'activities', 'id' => 'activities', 'title' => 'Aktivitas Sekitar', 'listClass' => 'activity-list'],
                ['key' => 'forest_utilization', 'id' => 'utilization', 'title' => 'Pemanfaatan Hutan', 'listClass' => 'utilization-list'],
                ['key' => 'programs', 'id' => 'programs', 'title' => 'Program yang Dilaksanakan', 'listClass' => 'program-list'],
                ['key' => 'stakeholders', 'id' => 'stakeholders', 'title' => 'Pihak Terkait', 'listClass' => 'stakeholder-list']
            ] as $acc)
                @if(isset($location[$acc['key']]))
                <x-components.accordion-card :id="$acc['id']" :title="$acc['title']">
                    @if($acc['key'] === 'activities')
                        <p class="activity-description">{{ $location['activities']['description'] }}</p>
                        <ul class="{{ $acc['listClass'] }}">
                            @foreach($location['activities']['items'] as $item)
                            <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <ul class="{{ $acc['listClass'] }}">
                            @foreach($location[$acc['key']] as $item)
                            <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @endif
                </x-components.accordion-card>
                @endif
            @endforeach

            {{-- Galeri Foto --}}
            <div class="card">
                <h2 class="card-title">Galeri Foto</h2>
                <div class="gallery">
                    @foreach($location['images'] as $index => $image)
                    <div class="gallery-item" onclick="openModal({{ $index }})">
                        <img src="{{ $image }}" alt="{{ $location['name'] }} - Foto {{ $index + 1 }}" loading="lazy">
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="sidebar">
            <x-shared.stat-card
                label="Kesehatan Mangrove"
                :value="$location['health']"
                :subtitle="$location['health_score']"
            />

            <x-shared.stat-card
                label="Total Luas Area"
                :value="$location['area']"
            />

            @if($location['damage_count'] > 0)
            <div class="damage-alert">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <div class="damage-text" style="font-weight: 600; margin-bottom: 0.25rem;">
                        {{ $location['damage_count'] }} Kerusakan Teridentifikasi
                    </div>
                    <div class="damage-text">Diperlukan tindakan konservasi segera</div>
                </div>
            </div>
            @endif

            <a href="{{ route('monitoring.hasil-pemantauan') }}" class="btn btn-secondary" style="justify-content: center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>
</div>

{{-- Image Modal with Navigation & Thumbnails --}}

<x-components.gallery-modal />

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ===========================
    // LEAFLET MAP INITIALIZATION
    // ===========================
    document.addEventListener('DOMContentLoaded', function() {
        const coords = '{{ $location["coords"] }}'.split(",").map(c => parseFloat(c.trim()));
        const map = L.map("detailMap").setView([coords[0], coords[1]], 15);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(map);

        L.marker([coords[0], coords[1]]).addTo(map)
            .bindPopup('<strong>{{ $location["name"] }}</strong><br>{{ $location["location"] }}')
            .openPopup();

        L.circle([coords[0], coords[1]], {
            color: "#009966",
            fillColor: "#009966",
            fillOpacity: 0.2,
            radius: 500,
            weight: 2
        }).addTo(map);

        setTimeout(() => map.invalidateSize(), 100);
    });

    // Generate Report function
    function generateReport() {
        alert('Fitur generate report akan segera tersedia');
    }


</script>
@endpush
