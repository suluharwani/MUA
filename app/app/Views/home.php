<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Hero Section -->
<section id="home" class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="location-badge">
                    <i class="bi bi-geo-alt me-1"></i> Grobogan, Jawa Tengah
                </div>
                <h1>Maulia Makeup & Sewa Kostum Pernikahan</h1>
                <p class="lead">Profesional Wedding Make Up Artist & Penyewaan Kostum Pernikahan di Grobogan dan sekitarnya. Melayani makeup pengantin dan sewa kostum pernikahan lengkap untuk hari istimewa Anda.</p>
                <div class="mt-4">
                    <a href="#booking" class="btn btn-primary btn-lg me-2">Pesan Makeup</a>
                    <a href="<?= base_url('sewa-kostum') ?>" class="btn btn-costume btn-lg">Sewa Kostum</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section id="process" class="section-padding process-section">
    <div class="container">
        <h2 class="section-title">Proses Kerja Sama</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h4>Konsultasi Gratis</h4>
                    <p>Diskusi via WhatsApp untuk memahami kebutuhan makeup atau kostum untuk pernikahan Anda.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h4>Trial & Fitting</h4>
                    <p>Sesi trial makeup atau fitting kostum di studio untuk memastikan sesuai dengan keinginan.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h4>DP & Booking</h4>
                    <p>Konfirmasi paket dan pembayaran DP 50% untuk mengamankan tanggal booking.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h4>Eksekusi & Pengiriman</h4>
                    <p>Eksekusi makeup di lokasi atau pengantaran kostum sesuai jadwal yang disepakati.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content sections from your HTML... -->
<!-- Saya akan sisipkan bagian-bagian lain sesuai kebutuhan -->

<?= $this->include('template/footer') ?>