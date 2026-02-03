@extends('layouts.admin.master')

@section('title', $title ?? 'Coming Soon')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">{{ $title ?? 'Halaman' }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $title ?? 'Halaman' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center" style="padding: 80px 20px;">
                    <div class="mb-4">
                        <i class="bx bx-time-five display-1 text-warning"></i>
                    </div>
                    <h3 class="mb-3">Fitur Segera Hadir</h3>
                    <p class="text-muted mb-4">
                        Halaman <strong>{{ $title ?? 'ini' }}</strong> sedang dalam tahap pengembangan.<br>
                        Fitur ini akan segera tersedia.
                    </p>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                        <i class="bx bx-arrow-back"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
