@extends('layouts.blank')

@section('content')
<div class="min-vh-100">
    <div class="bg-overlay bg-light"></div>
    <div class="container">
        <div class="d-flex flex-column min-vh-100 px-3 pt-4">
            <div class="row justify-content-center my-auto">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <div class="mb-4 pb-2">
                                    <a href="{{ url('/') }}" class="d-block auth-logo">
                                        <img src="https://pencil-matter-70015947.figma.site/_assets/v11/e6fb723ab1735b08498a0a60cab1e81508afa7e6.png" alt="" height="80" class="auth-logo-dark me-start">
                                    </a>
                                </div>
                                <h5>Selamat Datang!</h5>
                                <p class="text-muted">Login ke Admin Panel SIKOMANG</p>
                            </div>
                            <div class="p-2 mt-4">
                                <form action="{{ route('post_login') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label" for="username">Username<span class="text-danger"> *</span></label>
                                        <div class="position-relative input-custom-icon">
                                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                                   name="username" value="{{ old('username') }}" id="username"
                                                   placeholder="Masukan Username" required>
                                            <span class="bx bx-user"></span>
                                        </div>
                                        @error('username')
                                            <span class="text-danger"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="password-input">Password<span class="text-danger"> *</span></label>
                                        <div class="position-relative auth-pass-inputgroup input-custom-icon">
                                            <span class="bx bx-lock-alt"></span>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                   id="password" name="password" placeholder="Masukan Password" required>
                                            <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-3" id="togglePassword">
                                                <i class="mdi mdi-eye-outline font-size-18 text-muted"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <span class="text-danger"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>

                                    <div class="form-check py-1">
                                        <input type="checkbox" class="form-check-input" id="auth-remember-check">
                                        <label class="form-check-label" for="auth-remember-check">Ingatkan Saya</label>
                                    </div>

                                    <div class="mt-3">
                                        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Masuk</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <p>© <script>document.write(new Date().getFullYear())</script> SIKOMANG. DLH Provinsi DKI Jakarta</p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
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
});
</script>
@endsection
