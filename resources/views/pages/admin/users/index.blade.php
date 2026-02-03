@extends('layouts.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div id="toolbar">
                            <a href="{{ route('admin.users.update') }}" id="btn-add" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah User
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="grid-data">
                                <thead class="table-primary text-primary">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Dibuat</th>
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
    loadGridData();
});

function loadGridData() {
    $.ajax({
        url: '{{ route("admin.users.grid") }}',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            var tbody = $('#grid-data tbody');
            tbody.empty();

            if (data.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center">Tidak ada data</td></tr>');
                return;
            }

            $.each(data, function(index, item) {
                var row = '<tr>' +
                    '<td>' + item.no + '</td>' +
                    '<td>' + item.name + '</td>' +
                    '<td>' + item.username + '</td>' +
                    '<td>' + item.email + '</td>' +
                    '<td>' + item.role + '</td>' +
                    '<td>' + item.created_at + '</td>' +
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

$(document).on("click", ".remove_data", function(e) {
    e.preventDefault();
    var base_url = $(this).attr('data-href');

    Swal.fire({
        title: "Hapus User!",
        text: "Apa anda yakin ingin menghapus user ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Ya Hapus",
        cancelButtonText: "Tidak"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: base_url,
                type: "GET",
                success: function(response) {
                    if (response.success) {
                        alertify.success(response.message || "Berhasil Menghapus User");
                        loadGridData();
                    } else {
                        alertify.error("Gagal menghapus user");
                    }
                },
                error: function(xhr) {
                    alertify.error("Gagal menghapus user");
                }
            });
        }
    });
});
</script>
@endsection
