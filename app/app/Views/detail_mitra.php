<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('mitra') ?>">Mitra</a></li>
                <li class="breadcrumb-item active"><?= $mitra['nama_mitra'] ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Mitra Detail -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <!-- Mitra Images -->
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="mb-4">
                    <?php if (!empty($mitra['gambar'])): ?>
                        <img src="<?= base_url('uploads/mitra/' . $mitra['gambar']) ?>" 
                             alt="<?= $mitra['nama_mitra'] ?>" 
                             class="img-fluid rounded shadow-lg" 
                             id="mainMitraImage">
                    <?php else: ?>
                        <div class="bg-light rounded shadow-lg d-flex align-items-center justify-content-center" style="height: 400px;">
                            <i class="bi bi-building fs-1 text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($mitra['gambar_tambahan']) && is_array($mitra['gambar_tambahan'])): ?>
                <div class="row g-2">
                    <?php foreach ($mitra['gambar_tambahan'] as $image): ?>
                    <div class="col-4">
                        <img src="<?= base_url('uploads/mitra/tambahan/' . $image) ?>" 
                             alt="<?= $mitra['nama_mitra'] ?>" 
                             class="img-fluid rounded cursor-pointer"
                             style="height: 80px; object-fit: cover;"
                             onclick="document.getElementById('mainMitraImage').src = this.src">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Mitra Info -->
            <div class="col-lg-7">
                <div class="mitra-badge mb-3">
                    <?= $kategori_options[$mitra['kategori']] ?? ucfirst($mitra['kategori']) ?>
                </div>
                
                <h1 class="mb-3"><?= $mitra['nama_mitra'] ?></h1>
                
                <!-- Rating & Experience -->
                <div class="d-flex align-items-center mb-4">
                    <div class="text-warning me-2">
                        <?php
                        $rating = $mitra['rating'] ?? 5.0;
                        $fullStars = floor($rating);
                        $halfStar = ($rating - $fullStars) >= 0.5;
                        ?>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= $fullStars): ?>
                                <i class="bi bi-star-fill fs-5"></i>
                            <?php elseif ($i == $fullStars + 1 && $halfStar): ?>
                                <i class="bi bi-star-half fs-5"></i>
                            <?php else: ?>
                                <i class="bi bi-star fs-5"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    <span class="fs-5 fw-bold me-3"><?= number_format($rating, 1) ?></span>
                    <span class="text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        Pengalaman: <?= $mitra['pengalaman'] ?>
                    </span>
                </div>
                
                <?php if (!empty($mitra['harga_mulai'])): ?>
                <div class="price display-5 mb-4 text-primary">
                    Mulai Rp <?= number_format($mitra['harga_mulai'], 0, ',', '.') ?>
                </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <h5 class="mb-3">Deskripsi</h5>
                    <p><?= nl2br($mitra['deskripsi']) ?></p>
                </div>
                
                <?php if (!empty($mitra['layanan']) && is_array($mitra['layanan'])): ?>
                <div class="mb-4">
                    <h5 class="mb-3">Layanan yang Ditawarkan</h5>
                    <div class="row g-2">
                        <?php foreach ($mitra['layanan'] as $service): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="border rounded p-3 text-center h-100">
                                <i class="bi bi-check-circle-fill text-success mb-2"></i>
                                <p class="mb-0"><?= $service ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Contact Info -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Kontak Mitra</h5>
                        <div class="row">
                            <?php if (!empty($mitra['alamat'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-geo-alt text-primary me-2"></i> Alamat:</strong><br>
                                <span><?= $mitra['alamat'] ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($mitra['whatsapp'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-whatsapp text-success me-2"></i> WhatsApp:</strong><br>
                                <a href="https://wa.me/<?= $mitra['whatsapp'] ?>" 
                                   class="text-decoration-none" 
                                   target="_blank">
                                    <?= $mitra['whatsapp'] ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($mitra['telepon'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-telephone text-primary me-2"></i> Telepon:</strong><br>
                                <a href="tel:<?= $mitra['telepon'] ?>" 
                                   class="text-decoration-none">
                                    <?= $mitra['telepon'] ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($mitra['email'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-envelope text-warning me-2"></i> Email:</strong><br>
                                <a href="mailto:<?= $mitra['email'] ?>" 
                                   class="text-decoration-none">
                                    <?= $mitra['email'] ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Social Media -->
                        <?php if (!empty($mitra['instagram']) || !empty($mitra['facebook']) || !empty($mitra['tiktok']) || !empty($mitra['website'])): ?>
                        <div class="mt-3">
                            <strong class="mb-2 d-block">Media Sosial:</strong>
                            <div class="d-flex gap-2">
                                <?php if (!empty($mitra['instagram'])): ?>
                                <a href="<?= $mitra['instagram'] ?>" 
                                   class="btn btn-outline-danger btn-sm" 
                                   target="_blank">
                                    <i class="bi bi-instagram"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($mitra['facebook'])): ?>
                                <a href="<?= $mitra['facebook'] ?>" 
                                   class="btn btn-outline-primary btn-sm" 
                                   target="_blank">
                                    <i class="bi bi-facebook"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($mitra['tiktok'])): ?>
                                <a href="<?= $mitra['tiktok'] ?>" 
                                   class="btn btn-outline-dark btn-sm" 
                                   target="_blank">
                                    <i class="bi bi-tiktok"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($mitra['website'])): ?>
                                <a href="<?= $mitra['website'] ?>" 
                                   class="btn btn-outline-success btn-sm" 
                                   target="_blank">
                                    <i class="bi bi-globe"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-3">
                    <a href="https://wa.me/<?= $mitra['whatsapp'] ?>?text=Halo%20<?= urlencode($mitra['nama_mitra']) ?>%2C%20saya%20dapat%20rekomendasi%20dari%20Maulia%20Wedding.%20Saya%20ingin%20konsultasi%20tentang%20<?= urlencode($mitra['nama_mitra']) ?>" 
                       class="btn btn-success btn-lg" 
                       target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>Hubungi via WhatsApp
                    </a>
                    
                    <?php if (!empty($mitra['telepon'])): ?>
                    <a href="tel:<?= $mitra['telepon'] ?>" 
                       class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-telephone me-2"></i>Telepon Sekarang
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Form -->
<section id="bookingMitra" class="section-padding bg-light">
    <div class="container">
        <div class="booking-form-container">
            <h2 class="text-center mb-5">Konsultasi dengan <?= $mitra['nama_mitra'] ?></h2>
            <p class="text-center mb-4">Isi form di bawah untuk konsultasi langsung dengan mitra. Data Anda akan dikirimkan ke mitra terkait.</p>
            
            <form id="bookingFormMitra">
                <input type="hidden" id="mitraName" value="<?= $mitra['nama_mitra'] ?>">
                <input type="hidden" id="mitraWhatsApp" value="<?= $mitra['whatsapp'] ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="fullNameMitra" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="fullNameMitra" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="phoneMitra" class="form-label">Nomor WhatsApp *</label>
                            <input type="tel" class="form-control" id="phoneMitra" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="weddingDate" class="form-label">Tanggal Pernikahan</label>
                            <input type="date" class="form-control" id="weddingDate">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="budget" class="form-label">Estimasi Budget</label>
                            <select class="form-select" id="budget">
                                <option value="">Pilih range budget</option>
                                <option value="< 5 juta">< 5 juta</option>
                                <option value="5 - 10 juta">5 - 10 juta</option>
                                <option value="10 - 20 juta">10 - 20 juta</option>
                                <option value="20 - 50 juta">20 - 50 juta</option>
                                <option value="> 50 juta">> 50 juta</option>
                                <option value="lainnya">Belum pasti / konsultasi</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="serviceNeeded" class="form-label">Layanan yang Dibutuhkan *</label>
                    <textarea class="form-control" id="serviceNeeded" rows="3" placeholder="Jelaskan kebutuhan Anda secara detail..." required></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="additionalInfoMitra" class="form-label">Informasi Tambahan</label>
                    <textarea class="form-control" id="additionalInfoMitra" rows="3" placeholder="Tema pernikahan, lokasi, jumlah tamu, preferensi khusus, dll."></textarea>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="agreePrivacy" required>
                    <label class="form-check-label" for="agreePrivacy">
                        Saya setuju data saya dibagikan ke <?= $mitra['nama_mitra'] ?> untuk keperluan konsultasi *
                    </label>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-send me-2"></i>Kirim Permintaan
                    </button>
                    <p class="text-muted mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Permintaan akan dikirim ke <?= $mitra['nama_mitra'] ?>. Anda akan dihubungi dalam 1x24 jam.
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Related Mitra -->
<?php if (!empty($related_mitra)): ?>
<section class="section-padding">
    <div class="container">
        <h2 class="section-title text-center mb-5">Mitra Lainnya</h2>
        <div class="row">
            <?php foreach ($related_mitra as $item): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-img-top" style="height: 150px; overflow: hidden;">
                        <?php if (!empty($item['gambar'])): ?>
                            <img src="<?= base_url('uploads/mitra/' . $item['gambar']) ?>" 
                                 class="img-fluid w-100 h-100" 
                                 alt="<?= $item['nama_mitra'] ?>"
                                 style="object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-building text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <span class="badge bg-light text-dark mb-2"><?= $kategori_options[$item['kategori']] ?? ucfirst($item['kategori']) ?></span>
                        <h5 class="card-title"><?= character_limiter($item['nama_mitra'], 30) ?></h5>
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning small">
                                <?php
                                $rating = $item['rating'] ?? 5.0;
                                $fullStars = floor($rating);
                                ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $fullStars): ?>
                                        <i class="bi bi-star-fill"></i>
                                    <?php else: ?>
                                        <i class="bi bi-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted ms-2"><?= number_format($rating, 1) ?></small>
                        </div>
                        <a href="<?= base_url('mitra/' . $item['slug']) ?>" class="btn btn-sm btn-outline-primary w-100">
                            Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Additional Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for wedding date (tomorrow)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('weddingDate').min = minDate;
    
    // Form submission for mitra consultation
    const bookingFormMitra = document.getElementById('bookingFormMitra');
    bookingFormMitra.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const fullName = document.getElementById('fullNameMitra').value;
        const phone = document.getElementById('phoneMitra').value;
        const mitraName = document.getElementById('mitraName').value;
        const mitraWhatsApp = document.getElementById('mitraWhatsApp').value;
        const weddingDate = document.getElementById('weddingDate').value;
        const budget = document.getElementById('budget').value;
        const serviceNeeded = document.getElementById('serviceNeeded').value;
        const additionalInfo = document.getElementById('additionalInfoMitra').value;
        
        // Format date if exists
        let formattedDate = '-';
        if (weddingDate) {
            formattedDate = new Date(weddingDate).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        // Create message for mitra
        const messageToMitra = `Halo ${mitraName}, saya ${fullName} dapat rekomendasi dari Maulia Wedding. Saya tertarik dengan layanan Anda.

📋 Kebutuhan: ${serviceNeeded}
📅 Tanggal Pernikahan: ${formattedDate}
💰 Budget: ${budget || '-'}
📍 Info Tambahan: ${additionalInfo || '-'}
📞 Kontak Saya: ${phone}

Mohon info lebih lanjut tentang layanan dan harga. Terima kasih.`;
        
        // Create message for user copy
        const messageForUser = `Permintaan konsultasi untuk ${mitraName} berhasil dibuat. Berikut detail yang akan dikirim:

Nama: ${fullName}
Telepon: ${phone}
Tanggal Pernikahan: ${formattedDate}
Budget: ${budget || '-'}
Kebutuhan: ${serviceNeeded}
Info Tambahan: ${additionalInfo || '-'}

Klik OK untuk membuka WhatsApp dan mengirim pesan ke ${mitraName}.`;
        
        // Show confirmation
        if (confirm(messageForUser)) {
            // Encode message for URL
            const encodedMessage = encodeURIComponent(messageToMitra);
            
            // Create WhatsApp URL
            const whatsappURL = `https://wa.me/${mitraWhatsApp}?text=${encodedMessage}`;
            
            // Open WhatsApp
            window.open(whatsappURL, '_blank');
            
            // Clear form
            bookingFormMitra.reset();
        }
    });
});
</script>

<!-- Additional CSS -->
<style>
.mitra-badge {
    background-color: var(--accent-color);
    color: var(--heading-color);
    padding: 8px 20px;
    border-radius: 30px;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 10px;
}

.cursor-pointer {
    cursor: pointer;
}

.card-img-top {
    border-radius: 8px 8px 0 0;
}
</style>

<?= $this->include('template/footer') ?>