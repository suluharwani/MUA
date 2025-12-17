<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Hero Section -->
<section class="hero-section section-padding" style="background: linear-gradient(rgba(255, 255, 255, 0.92), rgba(249, 247, 244, 0.92)), url('https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3Dhttps://images.unsplash.com/photo-1515934751635-c81c6bc9a2d8?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="location-badge">
                    <i class="bi bi-geo-alt me-1"></i> Grobogan, Jawa Tengah
                </div>
                <h1>Sewa Kostum Pernikahan</h1>
                <p class="lead">Temukan koleksi kostum pernikahan terlengkap untuk pengantin dan keluarga. Semua kostum dalam kondisi terawat dan siap pakai untuk hari istimewa Anda.</p>
            </div>
        </div>
    </div>
</section>

<!-- Kategori Filter -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="text-center mb-4">
                    <h3 class="mb-3">Pilih Kategori Kostum</h3>
                    <p class="text-muted">Temukan kostum yang sesuai dengan kebutuhan pernikahan Anda</p>
                </div>
                
                <div class="row justify-content-center">
                    <?php
                    $kategori_options = [
                        'pengantin_wanita' => ['icon' => 'bi-person-hearts', 'label' => 'Pengantin Wanita', 'count' => 0],
                        'pengantin_pria' => ['icon' => 'bi-person', 'label' => 'Pengantin Pria', 'count' => 0],
                        'keluarga' => ['icon' => 'bi-people-fill', 'label' => 'Keluarga', 'count' => 0],
                        'lainnya' => ['icon' => 'bi-tags-fill', 'label' => 'Lainnya', 'count' => 0]
                    ];
                    
                    // Hitung jumlah kostum per kategori
                    foreach ($all_kostum as $k) {
                        if (isset($kategori_options[$k['kategori']])) {
                            $kategori_options[$k['kategori']]['count']++;
                        }
                    }
                    
                    foreach ($kategori_options as $key => $kategori):
                        if ($kategori['count'] > 0 || $key == 'lainnya'):
                    ?>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="<?= base_url('sewa-kostum' . ($key ? '?kategori=' . $key : '')) ?>" 
                           class="card kategori-card text-center text-decoration-none <?= ($kategori_aktif == $key || (!$kategori_aktif && $key == 'pengantin_wanita')) ? 'active' : '' ?>">
                            <div class="card-body py-4">
                                <div class="kategori-icon mb-3">
                                    <i class="bi <?= $kategori['icon'] ?> fs-1"></i>
                                </div>
                                <h5 class="card-title mb-2"><?= $kategori['label'] ?></h5>
                                <p class="text-muted mb-0"><?= $kategori['count'] ?> Kostum</p>
                            </div>
                        </a>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Kostum List Section -->
<section class="section-padding">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title costume">Koleksi Kostum Kami</h2>
                <p class="text-muted"><?= $kategori_aktif ? 'Kostum ' . ($kategori_options[$kategori_aktif]['label'] ?? $kategori_aktif) : 'Semua kostum tersedia untuk disewa' ?></p>
            </div>
        </div>
        
        <?php if (empty($kostum)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Kostum dalam kategori ini sedang tidak tersedia. Silakan pilih kategori lain atau hubungi kami untuk informasi lebih lanjut.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($kostum as $item): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="costume-card h-100">
                        <?php if (!empty($item['gambar'])): ?>
                            <div class="costume-image mb-4" style="height: 250px; overflow: hidden; border-radius: 8px;">
                                <img src="<?= base_url('uploads/kostum/' . $item['gambar']) ?>" 
                                     alt="<?= $item['nama_kostum'] ?>" 
                                     class="img-fluid w-100 h-100" 
                                     style="object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="costume-image mb-4" style="height: 250px; background-color: var(--costume-color); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image fs-1 text-white"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="costume-badge mb-2">
                            <?= $kategori_options[$item['kategori']]['label'] ?? ucfirst($item['kategori']) ?>
                        </div>
                        
                        <h4 class="mb-3"><?= $item['nama_kostum'] ?></h4>
                        <?php if (!empty($item['deskripsi'])): ?>
    <?= character_limiter($item['deskripsi'], 100) ?>
<?php else: ?>
    <span class="text-muted">Tidak ada deskripsi</span>
<?php endif; ?>
                        
                        <?php if (!empty($item['spesifikasi']) && is_array($item['spesifikasi'])): ?>
                            <ul class="mb-3">
                                <?php foreach (array_slice($item['spesifikasi'], 0, 3) as $spec): ?>
                                    <li><?= $spec ?></li>
                                <?php endforeach; ?>
                                <?php if (count($item['spesifikasi']) > 3): ?>
                                    <li class="text-muted">+<?= count($item['spesifikasi']) - 3 ?> lainnya</li>
                                <?php endif; ?>
                            </ul>
                        <?php endif; ?>
                        
                        <div class="row align-items-center mt-4">
                            <div class="col-6">
                                <div class="price">Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?></div>
                                <small class="text-muted"><?= $item['durasi_sewa'] ?></small>
                            </div>
                            <div class="col-6 text-end">
                                <span class="badge <?= $item['stok_tersedia'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $item['stok_tersedia'] > 0 ? 'Tersedia' : 'Habis' ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="<?= base_url('sewa-kostum/' . $item['slug']) ?>" class="btn btn-costume">
                                <i class="bi bi-eye me-2"></i>Detail & Sewa
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Pricing Info -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4">Informasi Sewa Kostum</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="bi bi-check-circle text-success me-2"></i>Yang Termasuk:</h5>
                                <ul class="mb-4">
                                    <li class="mb-2">Kostum dalam kondisi bersih dan terawat</li>
                                    <li class="mb-2">Aksesoris standar (jika ada)</li>
                                    <li class="mb-2">Biaya cleaning setelah pemakaian</li>
                                    <li class="mb-2">Konsultasi pemilihan kostum gratis</li>
                                    <li class="mb-2">Fitting di studio sebelum sewa</li>
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Ketentuan Sewa:</h5>
                                <ul class="mb-4">
                                    <li class="mb-2">Masa sewa standar <?= $kostum[0]['durasi_sewa'] ?? '3 hari' ?></li>
                                    <li class="mb-2">DP 50% saat booking</li>
                                    <li class="mb-2">Pelunasan saat pengambilan kostum</li>
                                    <li class="mb-2">Denda keterlambatan: Rp 50.000/hari</li>
                                    <li class="mb-2">Biaya perbaikan untuk kerusakan</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Tips:</strong> Booking kostum minimal 1 bulan sebelum tanggal pernikahan untuk memastikan ketersediaan. Kostum dapat dicoba di studio kami sebelum disewa.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Sewa Kostum -->
<section class="section-padding">
    <div class="container">
        <h2 class="section-title text-center mb-5">Pertanyaan Umum Sewa Kostum</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqKostum">
                    <!-- FAQ 1 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqK1">
                                <i class="bi bi-question-circle me-2" style="color: var(--costume-color);"></i>
                                Berapa lama sebelum pernikahan sebaiknya booking kostum?
                            </button>
                        </h3>
                        <div id="faqK1" class="accordion-collapse collapse" data-bs-parent="#faqKostum">
                            <div class="accordion-body">
                                Kami menyarankan booking kostum minimal 1-2 bulan sebelum tanggal pernikahan, terutama untuk musim pernikahan ramai (Juli-Desember). Untuk kostum popular dan ukuran khusus, booking lebih awal sangat dianjurkan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 2 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqK2">
                                <i class="bi bi-question-circle me-2" style="color: var(--costume-color);"></i>
                                Apakah boleh mencoba kostum sebelum menyewa?
                            </button>
                        </h3>
                        <div id="faqK2" class="accordion-collapse collapse" data-bs-parent="#faqKostum">
                            <div class="accordion-body">
                                Ya, Anda boleh datang ke studio kami untuk mencoba kostum sebelum memutuskan menyewa. Silakan buat janji terlebih dahulu via WhatsApp untuk memastikan kostum tersedia dan ada staf yang membantu fitting.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 3 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqK3">
                                <i class="bi bi-question-circle me-2" style="color: var(--costume-color);"></i>
                                Bagaimana jika ukuran kostum tidak pas?
                            </button>
                        </h3>
                        <div id="faqK3" class="accordion-collapse collapse" data-bs-parent="#faqKostum">
                            <div class="accordion-body">
                                Kami menyediakan jasa penyesuaian ukuran dengan biaya tambahan. Untuk perubahan minor seperti pinggang atau lengan, biaya penyesuaian mulai dari Rp 50.000. Untuk perubahan besar, biaya akan disesuaikan dengan tingkat kesulitan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 4 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqK4">
                                <i class="bi bi-question-circle me-2" style="color: var(--costume-color);"></i>
                                Apakah kostum bisa diantar ke lokasi?
                            </button>
                        </h3>
                        <div id="faqK4" class="accordion-collapse collapse" data-bs-parent="#faqKostum">
                            <div class="accordion-body">
                                Ya, kami menyediakan layanan pengantaran kostum dengan biaya tambahan sesuai jarak. Untuk area Grobogan gratis ongkir. Area lain dikenakan biaya transport. Pengantaran dilakukan H-1 pernikahan dan pengambilan H+1.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 5 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqK5">
                                <i class="bi bi-question-circle me-2" style="color: var(--costume-color);"></i>
                                Bagaimana jika kostum rusak atau kotor saat disewa?
                            </button>
                        </h3>
                        <div id="faqK5" class="accordion-collapse collapse" data-bs-parent="#faqKostum">
                            <div class="accordion-body">
                                Biaya cleaning sudah termasuk dalam harga sewa. Untuk kerusakan ringan seperti kancing lepas atau jahitan terbuka, akan kami perbaiki tanpa biaya. Kerusakan berat seperti sobek atau noda permanen akan dikenakan biaya perbaikan sesuai tingkat kerusakan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background-color: var(--costume-color); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Ingin konsultasi pemilihan kostum?</h3>
                <p class="mb-0">Tim kami siap membantu Anda memilih kostum yang paling sesuai dengan tema pernikahan dan budget.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20konsultasi%20tentang%20sewa%20kostum%20pernikahan" 
                   class="btn btn-light btn-lg" target="_blank">
                    <i class="bi bi-whatsapp me-2"></i>Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Booking Form -->
<section id="booking" class="section-padding">
    <div class="container">
        <div class="booking-form-container">
            <h2 class="text-center mb-5">Form Pemesanan Kostum</h2>
            <p class="text-center mb-4">Isi form di bawah untuk booking atau konsultasi sewa kostum. Kami akan menghubungi Anda via WhatsApp dalam 1x24 jam.</p>
            
            <form id="bookingFormKostum">
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
                            <label for="kostum" class="form-label">Kostum yang Diminati *</label>
                            <select class="form-select" id="kostum" required>
                                <option value="" selected disabled>Pilih kostum...</option>
                                <?php foreach ($all_kostum as $item): ?>
                                <option value="<?= $item['nama_kostum'] ?> - Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?>">
                                    <?= $item['nama_kostum'] ?> - Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?>
                                </option>
                                <?php endforeach; ?>
                                <option value="lainnya">Kostum Lainnya / Konsultasi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="eventDate" class="form-label">Tanggal Penggunaan *</label>
                            <input type="date" class="form-control" id="eventDate" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="lokasi" class="form-label">Lokasi Pengambilan/Pengiriman *</label>
                            <input type="text" class="form-control" id="lokasi" placeholder="Alamat lengkap" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="ukuran" class="form-label">Ukuran (Jika sudah tahu)</label>
                            <select class="form-select" id="ukuran">
                                <option value="">Pilih ukuran...</option>
                                <option value="XS">XS (Extra Small)</option>
                                <option value="S">S (Small)</option>
                                <option value="M">M (Medium)</option>
                                <option value="L">L (Large)</option>
                                <option value="XL">XL (Extra Large)</option>
                                <option value="XXL">XXL (Double Extra Large)</option>
                                <option value="custom">Custom Size</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="additionalInfo" class="form-label">Informasi Tambahan</label>
                    <textarea class="form-control" id="additionalInfo" rows="4" placeholder="Detail kostum yang diinginkan, tema pernikahan, warna preferensi, jumlah set yang dibutuhkan, dll."></textarea>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="agreeTermsKostum" required>
                    <label class="form-check-label" for="agreeTermsKostum">Saya setuju dengan <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a> sewa kostum *</label>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-costume btn-lg px-5">
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

<!-- Additional CSS -->
<style>
.kategori-card {
    border: 2px solid transparent;
    transition: all 0.3s ease;
    background: white;
}

.kategori-card:hover,
.kategori-card.active {
    border-color: var(--costume-color);
    transform: translateY(-5px);
}

.kategori-card.active .kategori-icon {
    color: var(--costume-color);
}

.kategori-icon {
    color: var(--text-color);
    transition: color 0.3s ease;
}

.costume-image {
    position: relative;
}

.costume-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 70%, rgba(0,0,0,0.1));
    border-radius: 8px;
}
</style>

<!-- Additional Script for this page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for event date (tomorrow)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('eventDate').min = minDate;
    
    // Form submission for kostum booking
    const bookingFormKostum = document.getElementById('bookingFormKostum');
    bookingFormKostum.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const fullName = document.getElementById('fullName').value;
        const phone = document.getElementById('phone').value;
        const kostum = document.getElementById('kostum').value;
        const eventDate = document.getElementById('eventDate').value;
        const lokasi = document.getElementById('lokasi').value;
        const ukuran = document.getElementById('ukuran').value;
        const additionalInfo = document.getElementById('additionalInfo').value;
        
        // Format date
        const formattedDate = new Date(eventDate).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Create WhatsApp message
        const message = `Halo Maulia, saya ${fullName} ingin booking sewa kostum pernikahan. Detail:
👗 Kostum: ${kostum}
📏 Ukuran: ${ukuran || 'Belum ditentukan'}
📅 Tanggal Penggunaan: ${formattedDate}
📍 Lokasi: ${lokasi}
📞 WhatsApp: ${phone}
📝 Info Tambahan: ${additionalInfo || '-'}

Saya sudah membaca syarat dan ketentuan sewa kostum.`;
        
        // Encode message for URL
        const encodedMessage = encodeURIComponent(message);
        
        // Create WhatsApp URL
        const whatsappURL = `https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=${encodedMessage}`;
        
        // Open WhatsApp
        window.open(whatsappURL, '_blank');
        
        // Show success message
        alert('Terima kasih! Form booking kostum telah berhasil dikirim. Anda akan diarahkan ke WhatsApp untuk konfirmasi lebih lanjut.');
        
        // Reset form
        bookingFormKostum.reset();
    });
});
</script>

<?= $this->include('template/footer') ?>