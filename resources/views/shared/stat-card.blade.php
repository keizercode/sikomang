@props(['label', 'value', 'subtitle' => null])

<div class="stat-card">
    <div class="stat-label">{{ $label }}</div>
    <div class="stat-value">{{ $value }}</div>
    @if($subtitle)
    <div class="stat-label" style="margin-top: 0.5rem;">{{ $subtitle }}</div>
    @endif
</div>
