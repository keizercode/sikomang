@props([
    'location'
])

<a href="{{ route('monitoring.detail-lokasi', $location['slug']) }}"
   class="card location-card"
   data-group="{{ $location['group'] ?? 'all' }}"
   style="text-decoration: none; color: inherit; display: block;">

    {{-- Floating Rounded Image --}}
    <div class="card-image-float">
        <img
            src="{{ $location['images'][0] ?? 'https://via.placeholder.com/400x200?text=No+Image' }}"
            alt="{{ $location['name'] }}"
            loading="lazy"
            onerror="this.src='https://via.placeholder.com/400x200?text=No+Image'"
        >
        @if(isset($location['damage_count']) && $location['damage_count'] > 0)
        <div class="card-damage-badge">
            <span class="damage-pulse-dot"></span>
            {{ $location['damage_count'] }} Kerusakan
        </div>
        @endif
    </div>

    <div class="card-content">
        <h3 class="card-title">{{ $location['name'] }}</h3>

        @php
            $typeLabels = [
                'dilindungi'              => 'Dilindungi',
                'pengkayaan'              => 'Pengkayaan',
                'rehabilitasi'            => 'Rehabilitasi',
                'pengkayaan_rehabilitasi' => 'Pengkayaan/Rehabilitasi',
            ];
            $typeKey   = strtolower($location['type']);
            $typeLabel = $typeLabels[$typeKey] ?? $location['type'];
        @endphp
        <div class="card-badges">
            <span class="badge badge-{{ $typeKey }}">{{ $typeLabel }}</span>
            <span class="badge badge-year">{{ $location['year'] }}</span>
        </div>

        <div class="info-row">
            <span class="icon">
                <img src="https://res.cloudinary.com/dmcvht1vr/image/upload/v1769412092/605f77f2-e3f3-415e-8ae4-8846f9770cc7.png"
                     alt="Location"
                     style="width: 14px; height: 14px; object-fit: contain;">
            </span>
            <span class="text">{{ $location['coords'] }}</span>
        </div>

        <div class="info-row details">
            <div class="detail-item">
                <span class="label">Luas Area</span>
                <span class="value">{{ $location['area'] }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Kerapatan</span>
                <span class="value">{{ \Illuminate\Support\Str::title($location['density']) }}</span>
            </div>
        </div>

        <p class="description">
            <span style="font-size: 0.78rem; color: var(--text-light);">Species</span><br>
            @php
                $speciesRaw = $location['species'] ?? null;
                if ($speciesRaw && $speciesRaw !== 'Belum diidentifikasi') {
                    $speciesList = array_map('trim', explode(',', $speciesRaw));
                    $show  = array_slice($speciesList, 0, 1);
                    $extra = count($speciesList) - count($show);
                    $formatted = array_map(fn($s) => "<span class='species-name'>$s</span>", $show);
                    echo implode(', ', $formatted) . ($extra > 0 ? ', ...' : '');
                } else {
                    echo 'Spesies belum diidentifikasi';
                }
            @endphp
        </p>
    </div>
</a>
