@extends('frontend.layouts.master')

@section('title', 'Hubungi Kami - SIKOMANG')
@section('meta_description', 'Hubungi tim SIKOMANG untuk pertanyaan, saran, atau kerjasama terkait konservasi mangrove')

@section('styles')
<style>
    .contact-hero {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 80px 0 40px;
    }

    .contact-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        height: 100%;
        transition: transform 0.3s;
    }

    .contact-card:hover {
        transform: translateY(-5px);
    }

    .contact-card .icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
    }

    .form-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(45, 122, 94, 0.25);
    }

    #map-contact {
        height: 400px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .info-item {
        display: flex;
        align-items-start;
        margin-bottom: 25px;
    }

    .info-item .icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 20px;
        flex-shrink: 0;
    }

    .social-links a {
        display: inline-block;
        width: 50px;
        height: 50px;
        line-height: 50px;
        text-align: center;
        border-radius: 50%;
        margin-right: 15px;
        transition: all 0.3s;
        font-size: 1.2rem;
    }

    .social-links a.facebook {
        background: #1877f2;
        color: white;
    }

    .social-links a.twitter {
        background: #1da1f2;
        color: white;
    }

    .social-links a.instagram {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
        color: white;
    }

    .social-links a.youtube {
        background: #ff0000;
        color: white;
    }

    .social-links a:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('content')
<!-- Hero -->
<section class="contact-hero">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h1 class="mb-3">Hubungi Kami</h1>
            <p class="lead mb-0">Kami siap membantu Anda. Jangan ragu untuk menghubungi kami!</p>
        </div>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-card text-center">
                    <div class="icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h5>Alamat</h5>
                    <p class="text-muted mb-0">Jl. Mangrove No. 123<br>Jakarta Selatan, DKI Jakarta<br>Indonesia 12345</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-card text-center">
                    <div class="icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h5>Telepon</h5>
                    <p class="text-muted mb-0">+62 21 1234 5678<br>+62 812 3456 7890<br>Senin - Jumat: 08:00 - 17:00</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="contact-card text-center">
                    <div class="icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h5>Email</h5>
                    <p class="text-muted mb-0">info@sikomang.id<br>support@sikomang.id<br>partnership@sikomang.id</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form & Map -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="form-card" data-aos="fade-right">
                    <h3 class="mb-4">Kirim Pesan</h3>
                    <form id="contactForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control" name="phone">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subjek <span class="text-danger">*</span></label>
                                <select class="form-select" name="subject" required>
                                    <option value="">Pilih Subjek</option>
                                    <option value="general">Pertanyaan Umum</option>
                                    <option value="report">Laporan Kerusakan</option>
                                    <option value="partnership">Kerjasama</option>
                                    <option value="data">Permintaan Data</option>
                                    <option value="feedback">Saran & Masukan</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Pesan <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="message" rows="6" required></textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="agreement" required>
                                    <label class="form-check-label" for="agreement">
                                        Saya setuju dengan kebijakan privasi dan syarat & ketentuan
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info & Map -->
            <div class="col-lg-5">
                <div class="form-card mb-4" data-aos="fade-left">
                    <h5 class="mb-4">Informasi Kontak</h5>

                    <div class="info-item">
                        <div class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6>Kunjungi Kami</h6>
                            <p class="text-muted mb-0">Jl. Mangrove No. 123, Jakarta Selatan, DKI Jakarta 12345</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h6>Telepon</h6>
                            <p class="text-muted mb-0">+62 21 1234 5678<br>+62 812 3456 7890</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6>Email</h6>
                            <p class="text-muted mb-0">info@sikomang.id<br>support@sikomang.id</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6>Jam Operasional</h6>
                            <p class="text-muted mb-0">Senin - Jumat: 08:00 - 17:00<br>Sabtu - Minggu: Tutup</p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="form-card" data-aos="fade-left" data-aos-delay="100">
                    <h5 class="mb-4">Ikuti Kami</h5>
                    <div class="social-links">
                        <a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="youtube"><i class="fab fa-youtube"></i></a>
                    </div>
                    <p class="text-muted mt-3 mb-0">Dapatkan update terbaru tentang konservasi mangrove di media sosial kami!</p>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="row mt-5">
            <div class="col-12" data-aos="fade-up">
                <h3 class="mb-4 text-center">Lokasi Kantor</h3>
                <div id="map-contact"></div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Pertanyaan yang Sering Diajukan</h2>
            <p>Temukan jawaban atas pertanyaan umum tentang SIKOMANG</p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion" data-aos="fade-up" data-aos-delay="100">
                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Apa itu SIKOMANG?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                SIKOMANG adalah Sistem Informasi Kawasan Mangrove, platform digital untuk monitoring dan pengelolaan ekosistem mangrove di Indonesia.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Bagaimana cara melaporkan kerusakan mangrove?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Anda dapat melaporkan kerusakan melalui form kontak di halaman ini dengan memilih subjek "Laporan Kerusakan" atau langsung menghubungi kami via email/telepon.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Apakah data di SIKOMANG dapat diakses publik?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, sebagian besar data dapat diakses publik untuk tujuan edukasi dan penelitian. Untuk permintaan data khusus, silakan hubungi kami.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Bagaimana cara berkontribusi atau bermitra dengan SIKOMANG?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Kami terbuka untuk berbagai bentuk kerjasama. Silakan hubungi kami melalui email partnership@sikomang.id atau form kontak dengan subjek "Kerjasama".
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Initialize map
    var map = L.map('map-contact').setView([-6.2088, 106.8456], 15); // Jakarta coordinates

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([-6.2088, 106.8456]).addTo(map);
    marker.bindPopup("<b>Kantor SIKOMANG</b><br>Jl. Mangrove No. 123, Jakarta Selatan").openPopup();

    // Form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Show success message (in production, this would send to backend)
        alert('Terima kasih! Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
        this.reset();
    });
</script>
@endsection
