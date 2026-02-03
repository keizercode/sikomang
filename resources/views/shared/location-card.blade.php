@props([
    'location'
])

<a href="{{ route('detail-lokasi', $location['slug']) }}"
   class="card location-card"
   data-group="{{ $location['group'] ?? 'all' }}"
   style="text-decoration: none; color: inherit; display: block;">
    <div class="card-image">
        <img src="{{ $location['images'][0] ?? 'https://via.placeholder.com/300x200' }}"
             alt="{{ $location['name'] }}"
             loading="lazy">
    </div>
    <div class="card-content">
        <h3 class="card-title">{{ $location['name'] }}</h3>
        <div class="card-badges">
            <span class="badge badge-{{ strtolower($location['type']) }}">{{ $location['type'] }}</span>
            <span class="badge badge-year">{{ $location['year'] }}</span>
        </div>
        <div class="card-info">
            <div class="info-row">
                <span class="icon">
                    <img src="https://res.cloudinary.com/dmcvht1vr/image/upload/v1769412092/605f77f2-e3f3-415e-8ae4-8846f9770cc7.png"
                         alt="Location"
                         style="width: 16px; height: 16px; object-fit: contain;">
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
                    <span class="value">{{ $location['density'] }}</span>
                </div>
            </div>
            <p class="description">{{ $location['description'] ?? 'Tidak ada deskripsi' }}</p>

            @if(isset($location['damage_count']) && $location['damage_count'] > 0)
            <div class="status damage-status">
                <span class="status-icon">⚠️</span>
                <span class="status-text">{{ $location['damage_count'] }} Kerusakan teridentifikasi</span>
            </div>
            @endif
        </div>
    </div>
</a>
