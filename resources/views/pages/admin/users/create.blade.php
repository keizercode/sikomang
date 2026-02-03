@extends('layouts.admin.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <i class="mdi mdi-account"></i> {{ $title }}
                    </div>
                    <form action="{{ route($route.'.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="secure_id" value="{{ @$keyId }}">

                                <!-- Informasi User -->
                                <div class="col-12 mb-4">
                                    <h5 class="text-primary">Informasi User</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', @$item->name) }}" required>
                                    @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', @$item->email) }}" required>
                                    @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                                           value="{{ old('username', @$item->username) }}" required>
                                    @error('username')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Role/Group <span class="text-danger">*</span></label>
                                    <select name="group" class="form-control @error('group') is-invalid @enderror" required>
                                        <option value="">-Pilih Role-</option>
                                        @foreach($group as $g)
                                        <option value="{{ encode_id($g->MsGroupId) }}" {{ old('group', @$item->ms_group_id) == $g->MsGroupId ? 'selected' : '' }}>
                                            {{ $g->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('group')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Password Section -->
                                <div class="col-12 mb-4 mt-3">
                                    <h5 class="text-primary">Password</h5>
                                    <hr>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Password
                                        @if(@$item)
                                        <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small>
                                        @else
                                        <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               {{ @$item ? '' : 'required' }}>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        Min. 8 karakter, harus mengandung huruf besar, huruf kecil, angka, dan karakter spesial
                                    </small>
                                    @error('password')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Konfirmasi Password
                                        @if(!@$item)
                                        <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                               class="form-control" {{ @$item ? '' : 'required' }}>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Masukkan ulang password untuk konfirmasi</small>
                                </div>

                                @if(@$item)
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information"></i>
                                        <strong>Info:</strong> Anda sedang mengedit user <strong>{{ $item->name }}</strong>.
                                        Password hanya diubah jika Anda mengisi field password.
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <a href="{{ route($route.'.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-cancel"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="mdi mdi-content-save-outline"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').on('click', function() {
        let passwordField = $('#password');
        let icon = $(this).find('i');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
        }
    });

    $('#togglePasswordConfirm').on('click', function() {
        let passwordField = $('#password_confirmation');
        let icon = $(this).find('i');

        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
        }
    });

    // Password confirmation validation
    $('form').on('submit', function(e) {
        let password = $('#password').val();
        let passwordConfirm = $('#password_confirmation').val();

        if (password || passwordConfirm) {
            if (password !== passwordConfirm) {
                e.preventDefault();
                alertify.error('Password dan Konfirmasi Password tidak sama!');
                return false;
            }
        }
    });
});
</script>
@endsection
