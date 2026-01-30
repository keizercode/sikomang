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

                        <table class="table w-100"
                        data-search="true"
                        data-toggle="table"
                        data-pagination="true"
                        data-toolbar="#toolbar"
                        data-show-refresh="false"
                        data-url="{{ route('admin.monitoring.grid') }}"
                        data-ajax-options='{"xhrFields": {"withCredentials": true}}'
                        data-sort-name="no"
                        data-sort-order="asc"
                        data-page-size="10"
                        data-id-field="id"
                        id="grid-data">
                            <thead class="table-primary text-primary">
                            <tr>
                                <th data-field="no" data-width="50">No</th>
                                <th data-field="name">Nama Lokasi</th>
                                <th data-field="region">Wilayah</th>
                                <th data-field="area">Luas Area</th>
                                <th data-field="density">Kerapatan</th>
                                <th data-field="health">Kesehatan</th>
                                <th data-field="type">Tipe</th>
                                <th data-field="action" data-width="120">#</th>
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
@endsection

@section('js')
<script type="text/javascript">
$("#grid-data").on("click", ".remove_data", function() {
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
                    alertify.success("Berhasil Menghapus Data");
                    $('#grid-data').bootstrapTable('refresh');
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
