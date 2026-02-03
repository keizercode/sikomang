@extends('layouts.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="toolbar">
                            <a href="{{ route('admin.monitoring.create') }}" id="btn-add" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah Lokasi
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="grid-data">
                                <thead class="table-primary text-primary">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama Lokasi</th>
                                        <th>Wilayah</th>
                                        <th>Luas Area</th>
                                        <th>Kerapatan</th>
                                        <th>Kesehatan</th>
                                        <th>Tipe</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    // Load data using AJAX
    loadGridData();
});

function loadGridData() {
    $.ajax({
        url: '{{ route("admin.monitoring.grid") }}',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var tbody = $('#grid-data tbody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
                return;
            }

            $.each(data, function(index, item) {
                var row = '<tr>' +
                    '<td>' + item.no + '</td>' +
                    '<td>' + item.name + '</td>' +
                    '<td>' + item.region + '</td>' +
                    '<td>' + item.area + '</td>' +
                    '<td>' + item.density + '</td>' +
                    '<td>' + item.health + '</td>' +
                    '<td>' + item.type + '</td>' +
                    '<td>' + item.action + '</td>' +
                    '</tr>';
                tbody.append(row);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            alertify.error('Gagal memuat data');
        }
    });
}

// Delete handler
$(document).on("click", ".remove_data", function(e) {
    e.preventDefault();
    var base_url = $(this).attr('data-href');

    Swal.fire({
        title: "Hapus Data!",
        text: "Apa anda yakin ingin menghapus data ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Ya Hapus Sekarang",
        cancelButtonText: "Tidak"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url,
                type: "GET",
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message || "Berhasil Menghapus Data");
                        loadGridData(); // Reload data
                    } else {
                        alertify.error("Gagal menghapus data");
                    }
                },
                error: function(xhr) {
                    alertify.error("Gagal menghapus data");
                }
            });
        }
    });

    return false;
});
</script>
@endsection
