@extends('layouts.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="bx bx-info-circle text-primary" style="font-size: 72px;"></i>
                        </div>
                        <h3 class="mb-3">Fitur Dalam Pengembangan</h3>
                        <p class="text-muted mb-4">
                            Halaman ini sedang dalam tahap pengembangan dan akan segera tersedia.
                        </p>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="bx bx-arrow-back"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
