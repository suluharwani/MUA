<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Hero Section -->
<section class="hero-section section-padding" style="background: linear-gradient(rgba(255, 255, 255, 0.92), rgba(249, 247, 244, 0.92)), url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="location-badge">
                    <i class="bi bi-geo-alt me-1"></i> Grobogan, Jawa Tengah
                </div>
                <h1>Paket Makeup Pernikahan</h1>
                <p class="lead">Temukan paket makeup pernikahan terbaik untuk hari istimewa Anda. Kami menawarkan berbagai pilihan dari paket sederhana hingga lengkap dengan harga terjangkau.</p>
            </div>
        </div>
    </div>
</section>

<!-- Paket Makeup Section -->
<section class="section-padding">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title">Pilihan Paket Makeup</h2>
                <p class="text-muted">Semua paket sudah termasuk konsultasi gratis dan menggunakan produk berkualitas tinggi yang aman untuk kulit.</p>
            </div>
        </div>
        
        <?php if (empty($paket_makeup)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Data paket makeup sedang dalam perbaikan. Silakan hubungi kami untuk informasi lebih lanjut.
            </div>
        <?php else: ?>
            <div class="row justify-content-center">
                <?php foreach ($paket_makeup as $index => $paket): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card <?= $paket['is_featured'] ? 'featured' : '' ?>">
                        <?php if ($paket['is_featured']): ?>
                            <div class="featured-badge">FAVORIT</div>
                        <?php endif; ?>
                        
                        <h3><?= $paket['nama_paket'] ?></h3>
                        <p class="text-muted"><?= $paket['deskripsi'] ?></p>
                        
                        <div class="price">
                            Rp <?= number_format($paket['harga'], 0, ',', '.') ?>
                            <?php if (!empty($paket['durasi'])): ?>
                                <span class="price-period"> / <?= $paket['durasi'] ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4">
                            <?php if (!empty($paket['features']) && is_array($paket['features'])): ?>
                                <?php foreach ($paket['features'] as $feature): ?>
                                    <?php 
                                    // Cek apakah fitur tersedia atau tidak
                                    $isDisabled = strpos($feature, '[x]') !== false || strpos($feature, '(tidak termasuk)') !== false;
                                    $cleanFeature = str_replace(['[x]', '[✓]', '(tidak termasuk)', '(termasuk)'], '', $feature);
                                    ?>
                                    <div class="price-feature <?= $isDisabled ? 'disabled' : '' ?>">
                                        <?php if ($isDisabled): ?>
                                            <i class="bi bi-x-circle"></i>
                                        <?php else: ?>
                                            <i class="bi bi-check-circle-fill"></i>
                                        <?php endif; ?>
                                        <span><?= trim($cleanFeature) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <a href="#booking" 
                           class="btn <?= $paket['is_featured'] ? 'btn-primary' : 'btn-outline-primary' ?> w-100 mt-4"
                           data-package="<?= $paket['nama_paket'] ?> - Rp <?= number_format($paket['harga'], 0, ',', '.') ?>">
                            Pilih Paket
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Process Section -->
<section class="section-padding process-section">
    <div class="container">
        <h2 class="section-title text-center mb-5">Proses Makeup Pernikahan</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <h4>Konsultasi</h4>
                    <p>Diskusi tema, warna, dan konsep makeup yang diinginkan via WhatsApp atau langsung di studio.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">2</div>
                    <h4>Trial Makeup</h4>
                    <p>Sesi trial untuk mencoba makeup dan memastikan sesuai dengan keinginan sebelum hari-H.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">3</div>
                    <h4>Booking</h4>
                    <p>Konfirmasi paket dan tanggal, pembayaran DP 50% untuk mengamankan booking.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step">
                    <div class="step-number">4</div>
                    <h4>Hari-H</h4>
                    <p>Eksekusi makeup di lokasi pernikahan sesuai jadwal yang disepakati.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section-padding">
    <div class="container">
        <h2 class="section-title text-center mb-5">Pertanyaan Umum (FAQ)</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ 1 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Berapa lama sebelum pernikahan sebaiknya melakukan trial makeup?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Kami menyarankan trial makeup dilakukan 2-4 minggu sebelum hari pernikahan. Ini memberi waktu cukup untuk melakukan penyesuaian jika diperlukan dan memastikan kulit dalam kondisi terbaik.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 2 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Apakah produk makeup yang digunakan aman untuk kulit sensitif?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, kami menggunakan produk berkualitas tinggi yang aman untuk semua jenis kulit termasuk kulit sensitif. Silakan informasikan kondisi kulit Anda saat konsultasi agar kami bisa menyesuaikan produk yang digunakan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 3 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Berapa lama durasi makeup untuk pengantin?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Durasi makeup pengantin biasanya 1.5 - 2 jam. Untuk keluarga dan bridesmaid sekitar 45 menit - 1 jam per orang. Pastikan jadwal makeup disesuaikan dengan timeline acara pernikahan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 4 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Apakah ada biaya tambahan untuk lokasi di luar Grobogan?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ya, untuk lokasi di luar Grobogan akan dikenakan biaya transportasi tambahan sesuai jarak. Silakan konsultasikan lokasi pernikahan Anda untuk mendapatkan informasi biaya transportasi yang akurat.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 5 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Bagaimana jika terjadi perubahan jadwal pernikahan?
                            </button>
                        </h3>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Perubahan jadwal dapat dilakukan dengan pemberitahuan minimal 14 hari sebelum tanggal awal. Perubahan kurang dari 14 hari akan dikenakan biaya administrasi. DP dapat dialihkan ke tanggal baru dengan ketersediaan yang terbatas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Testimoni Pelanggan</h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">Sari & Rudi</h5>
                                <small class="text-muted">Pernikahan, 15 Maret 2024</small>
                            </div>
                        </div>
                        <p class="mb-0">"Makeup dari Maulia sangat natural dan tahan lama. Dari pagi sampai malam masih tetap fresh. Recomended banget!"</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">Dewi & Ahmad</h5>
                                <small class="text-muted">Pernikahan, 8 Februari 2024</small>
                            </div>
                        </div>
                        <p class="mb-0">"Paket premiumnya worth it banget! Trial makeup-nya membantu banget buat nentuin look yang pas. Thanks Maulia!"</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0">Maya & Dito</h5>
                                <small class="text-muted">Pernikahan, 22 Januari 2024</small>
                            </div>
                        </div>
                        <p class="mb-0">"Pelayanannya ramah dan profesional. Makeup ibu dan bridesmaid juga bagus semua. Puas banget!"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Form -->
<section id="booking" class="section-padding">
    <div class="container">
        <div class="booking-form-container">
            <h2 class="text-center mb-5">Booking Paket Makeup</h2>
            <p class="text-center mb-4">Isi form di bawah untuk booking atau konsultasi paket makeup. Kami akan menghubungi Anda via WhatsApp dalam 1x24 jam.</p>
            
            <form id="bookingFormMakeup">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="fullName" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="fullName" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="phone" class="form-label">Nomor WhatsApp *</label>
                            <input type="tel" class="form-control" id="phone" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="package" class="form-label">Paket yang Diminati *</label>
                            <select class="form-select" id="package" required>
                                <option value="" selected disabled>Pilih paket...</option>
                                <?php foreach ($paket_makeup as $paket): ?>
                                <option value="<?= $paket['nama_paket'] ?> - Rp <?= number_format($paket['harga'], 0, ',', '.') ?>">
                                    <?= $paket['nama_paket'] ?> - Rp <?= number_format($paket['harga'], 0, ',', '.') ?>
                                </option>
                                <?php endforeach; ?>
                                <option value="lainnya">Paket Lainnya / Konsultasi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="eventDate" class="form-label">Tanggal Pernikahan *</label>
                            <input type="date" class="form-control" id="eventDate" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="location" class="form-label">Lokasi Pernikahan *</label>
                            <input type="text" class="form-control" id="location" placeholder="Alamat lengkap lokasi pernikahan" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="bridalName" class="form-label">Nama Pengantin Wanita</label>
                            <input type="text" class="form-control" id="bridalName" placeholder="Nama calon pengantin">
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="additionalInfo" class="form-label">Informasi Tambahan</label>
                    <textarea class="form-control" id="additionalInfo" rows="4" placeholder="Tema pernikahan, warna dominan, jumlah orang yang perlu makeup (ibu, bridesmaid, dll), riwayat alergi kulit, dll."></textarea>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                    <label class="form-check-label" for="agreeTerms">Saya setuju dengan <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a> yang berlaku *</label>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-whatsapp me-2"></i>Kirim Booking via WhatsApp
                    </button>
                    <p class="text-muted mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Anda akan dihubungi via WhatsApp dalam 1x24 jam untuk konfirmasi dan detail lebih lanjut.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Masih ragu memilih paket?</h3>
                <p class="mb-0">Konsultasikan kebutuhan makeup pernikahan Anda secara gratis via WhatsApp. Kami akan membantu Anda memilih paket yang paling sesuai.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20konsultasi%20tentang%20paket%20makeup%20pernikahan" 
                   class="btn btn-light btn-lg" target="_blank">
                    <i class="bi bi-whatsapp me-2"></i>Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Additional Script for this page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for event date (tomorrow)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('eventDate').min = minDate;
    
    // Form submission for makeup booking
    const bookingFormMakeup = document.getElementById('bookingFormMakeup');
    bookingFormMakeup.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const fullName = document.getElementById('fullName').value;
        const phone = document.getElementById('phone').value;
        const package = document.getElementById('package').value;
        const eventDate = document.getElementById('eventDate').value;
        const location = document.getElementById('location').value;
        const bridalName = document.getElementById('bridalName').value;
        const additionalInfo = document.getElementById('additionalInfo').value;
        
        // Format date
        const formattedDate = new Date(eventDate).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Create WhatsApp message
        const message = `Halo Maulia, saya ${fullName} ingin booking paket makeup pernikahan. Detail:
📋 Paket: ${package}
👰 Nama Pengantin: ${bridalName || '-'}
📅 Tanggal Pernikahan: ${formattedDate}
📍 Lokasi: ${location}
📞 WhatsApp: ${phone}
📝 Info Tambahan: ${additionalInfo || '-'}

Saya sudah membaca syarat dan ketentuan.`;
        
        // Encode message for URL
        const encodedMessage = encodeURIComponent(message);
        
        // Create WhatsApp URL
        const whatsappURL = `https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=${encodedMessage}`;
        
        // Open WhatsApp
        window.open(whatsappURL, '_blank');
        
        // Show success message
        alert('Terima kasih! Form booking Anda telah berhasil dikirim. Anda akan diarahkan ke WhatsApp untuk konfirmasi lebih lanjut.');
        
        // Reset form
        bookingFormMakeup.reset();
    });
    
    // Auto-select package when clicking package buttons
    document.querySelectorAll('[data-package]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const packageValue = this.getAttribute('data-package');
            document.getElementById('package').value = packageValue;
            
            // Scroll to booking form smoothly
            const bookingSection = document.querySelector('#booking');
            if (bookingSection) {
                window.scrollTo({
                    top: bookingSection.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>

<?= $this->include('template/footer') ?>