@extends('frontend.layouts.master')

@section('title', 'Monitoring Kerusakan - SIKOMANG')
@section('meta_description', 'Pantau laporan kerusakan dan upaya penanganan kawasan mangrove di Indonesia')

@section('styles')
<style>
    .damage-timeline {
        position: relative;
        padding-left: 40px;
    }

    .damage-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
    }

    .timeline-item {
        position: relative;
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        border-left: 4px solid;
    }

    .timeline-item.priority-low {
        border-left-color: #4caf50;
    }

    .timeline-item.priority-medium {
        border-left-color: #ffa726;
    }

    .timeline-item.priority-high {
        border-left-color: #f44336;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -44px;
        top: 30px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: white;
        border: 4px solid var(--primary-color);
        box-shadow: 0 0 0 4px white;
    }

    .timeline-item.status-resolved::before {
        background: #4caf50;
        border-color: #4caf50;
    }

    .timeline-item.status-in_progress::before {
        background: #ffa726;
        border-color: #ffa726;
    }

    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-card .icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .stats-card h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .action-badge {
        background: #e8f5e9;
        border-left: 3px solid #4caf50;
        padding: 12px 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        font-size: 0.9rem;
    }

    .filter-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="text-center text-white" data-aos="fade-up">
            <h1 class="mb-3">Monitoring Kerusakan Mangrove</h1>
            <p class="lead mb-0">Pantau laporan kerusakan dan upaya penanganan di berbagai kawasan</p>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up">
                <div class="stats-card">
                    <div class="icon text-primary">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>{{ $stats['total_damages'] }}</h3>
                    <p class="text-muted mb-0">Total Laporan</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card">
                    <div class="icon text-secondary">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>{{ $stats['pending'] }}</h3>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-card">
                    <div class="icon text-warning">
                        <i class="fas fa-spinner"></i>
                    </div>
                    <h3>{{ $stats['in_progress'] }}</h3>
                    <p class="text-muted mb-0">Dalam Proses</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-card">
                    <div class="icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>{{ $stats['resolved'] }}</h3>
                    <p class="text-muted mb-0">Selesai</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Damage Reports -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Filters -->
            <div class="col-lg-3">
                <div class="filter-card" data-aos="fade-up">
                    <h5 class="mb-4"><i class="fas fa-filter me-2"></i> Filter</h5>

                    <div class="mb-3">
                        <label class="form-label">Status:</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">Dalam Proses</option>
                            <option value="resolved">Selesai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Prioritas:</label>
                        <select class="form-select" id="priorityFilter">
                            <option value="">Semua Prioritas</option>
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i> Reset Filter
                    </button>
                </div>

                <!-- Legend -->
                <div class="filter-card mt-4" data-aos="fade-up" data-aos-delay="100">
                    <h6 class="mb-3">Keterangan:</h6>
                    <div class="mb-2">
                        <span style="width: 20px; height: 20px; background: #4caf50; border-radius: 50%; display: inline-block;"></span>
                        <span class="ms-2">Prioritas Rendah</span>
                    </div>
                    <div class="mb-2">
                        <span style="width: 20px; height: 20px; background: #ffa726; border-radius: 50%; display: inline-block;"></span>
                        <span class="ms-2">Prioritas Sedang</span>
                    </div>
                    <div class="mb-2">
                        <span style="width: 20px; height: 20px; background: #f44336; border-radius: 50%; display: inline-block;"></span>
                        <span class="ms-2">Prioritas Tinggi</span>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="col-lg-9">
                @if($damages->count() > 0)
                <div class="damage-timeline">
                    @foreach($damages as $damage)
                    <div class="timeline-item priority-{{ $damage->priority }} status-{{ $damage->status }}"
                         data-status="{{ $damage->status }}"
                         data-priority="{{ $damage->priority }}"
                         data-aos="fade-up"
                         data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-2">{{ $damage->title }}</h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    <a href="{{ route('frontend.detail', encode_id($damage->location_id)) }}" class="text-decoration-none">
                                        {{ $damage->location->name }}
                                    </a>
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    {{ \Carbon\Carbon::parse($damage->created_at)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="text-end">
                                @if($damage->priority == 'low')
                                    <span class="badge bg-success mb-2">Prioritas Rendah</span>
                                @elseif($damage->priority == 'medium')
                                    <span class="badge bg-warning mb-2">Prioritas Sedang</span>
                                @else
                                    <span class="badge bg-danger mb-2">Prioritas Tinggi</span>
                                @endif
                                <br>
                                @if($damage->status == 'pending')
                                    <span class="badge bg-secondary">Pending</span>
                                @elseif($damage->status == 'in_progress')
                                    <span class="badge bg-info">Dalam Proses</span>
                                @else
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            </div>
                        </div>

                        <p class="text-muted">{{ $damage->description }}</p>

                        @if($damage->actions->count() > 0)
                        <div class="mt-4">
                            <h6 class="mb-3">
                                <i class="fas fa-tools me-2"></i>
                                Aksi Penanganan ({{ $damage->actions->count() }})
                            </h6>
                            @foreach($damage->actions as $action)
                            <div class="action-badge">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ $action->action_description }}
                                    </div>
                                    @if($action->action_date)
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($action->action_date)->format('d M Y') }}
                                    </small>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="alert alert-light border mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Belum ada aksi penanganan yang tercatat.
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $damages->links() }}
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4 class="text-muted">Tidak ada laporan kerusakan</h4>
                    <p class="text-muted">Saat ini tidak ada laporan kerusakan yang tercatat.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8" data-aos="fade-right">
                <h3 class="text-white mb-3">Temukan Kerusakan di Kawasan Anda?</h3>
                <p class="text-white mb-0">Laporkan segera untuk mendapatkan penanganan yang cepat dan tepat.</p>
            </div>
            <div class="col-lg-4 text-lg-end text-start mt-3 mt-lg-0" data-aos="fade-left">
                <a href="{{ route('frontend.contact') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-exclamation-triangle me-2"></i> Laporkan Kerusakan
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Filter functionality
    document.getElementById('statusFilter').addEventListener('change', filterDamages);
    document.getElementById('priorityFilter').addEventListener('change', filterDamages);

    function filterDamages() {
        const statusFilter = document.getElementById('statusFilter').value;
        const priorityFilter = document.getElementById('priorityFilter').value;
        const items = document.querySelectorAll('.timeline-item');

        items.forEach(item => {
            const itemStatus = item.getAttribute('data-status');
            const itemPriority = item.getAttribute('data-priority');

            const statusMatch = !statusFilter || itemStatus === statusFilter;
            const priorityMatch = !priorityFilter || itemPriority === priorityFilter;

            if (statusMatch && priorityMatch) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function resetFilters() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('priorityFilter').value = '';
        filterDamages();
    }
</script>
@endsection
