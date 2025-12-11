<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('sewa-kostum') ?>">Sewa Kostum</a></li>
                <li class="breadcrumb-item active"><?= $kostum['nama_kostum'] ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Kostum Detail -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <!-- Kostum Images -->
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="mb-4">
                    <?php if (!empty($kostum['gambar'])): ?>
                        <img src="<?= base_url('uploads/kostum/' . $kostum['gambar']) ?>" 
                             alt="<?= $kostum['nama_kostum'] ?>" 
                             class="img-fluid rounded shadow" 
                             id="mainImage">
                    <?php else: ?>
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                            <i class="bi bi-image fs-1 text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($kostum['gambar_tambahan']) && is_array($kostum['gambar_tambahan'])): ?>
                <div class="row g-2">
                    <?php foreach ($kostum['gambar_tambahan'] as $image): ?>
                    <div class="col-3">
                        <img src="<?= base_url('uploads/kostum/tambahan/' . $image) ?>" 
                             alt="<?= $kostum['nama_kostum'] ?>" 
                             class="img-fluid rounded cursor-pointer"
                             style="height: 80px; object-fit: cover;"
                             onclick="document.getElementById('mainImage').src = this.src">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Kostum Info -->
            <div class="col-lg-6">
                <div class="costume-badge mb-3">
                    <?= ucfirst($kostum['kategori']) ?>
                </div>
                
                <h1 class="mb-3"><?= $kostum['nama_kostum'] ?></h1>
                
                <div class="price display-5 mb-4">
                    Rp <?= number_format($kostum['harga_sewa'], 0, ',', '.') ?>
                    <small class="text-muted fs-6">/ <?= $kostum['durasi_sewa'] ?></small>
                </div>
                
                <div class="mb-4">
                    <span class="badge <?= $kostum['stok_tersedia'] > 0 ? 'bg-success' : 'bg-danger' ?> fs-6">
                        <i class="bi bi-<?= $kostum['stok_tersedia'] > 0 ? 'check' : 'x' ?>-circle me-1"></i>
                        <?= $kostum['stok_tersedia'] > 0 ? 'Tersedia' : 'Habis' ?>
                    </span>
                    
                    <?php if ($kostum['stok_tersedia'] > 0): ?>
                        <span class="ms-2 text-muted">
                            <i class="bi bi-box-seam me-1"></i>
                            Stok: <?= $kostum['stok_tersedia'] ?> dari <?= $kostum['stok'] ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <h5 class="mb-3">Deskripsi</h5>
                    <p><?= nl2br($kostum['deskripsi']) ?></p>
                </div>
                
                <?php if (!empty($kostum['spesifikasi']) && is_array($kostum['spesifikasi'])): ?>
                <div class="mb-4">
                    <h5 class="mb-3">Spesifikasi</h5>
                    <ul class="list-unstyled">
                        <?php foreach ($kostum['spesifikasi'] as $spec): ?>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <?= $spec ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($kostum['ukuran']) || !empty($kostum['warna']) || !empty($kostum['bahan'])): ?>
                <div class="mb-4">
                    <h5 class="mb-3">Detail Kostum</h5>
                    <div class="row">
                        <?php if (!empty($kostum['ukuran'])): ?>
                        <div class="col-md-4 mb-2">
                            <strong>Ukuran:</strong><br>
                            <?= $kostum['ukuran'] ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($kostum['warna'])): ?>
                        <div class="col-md-4 mb-2">
                            <strong>Warna:</strong><br>
                            <?= $kostum['warna'] ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($kostum['bahan'])): ?>
                        <div class="col-md-4 mb-2">
                            <strong>Bahan:</strong><br>
                            <?= $kostum['bahan'] ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <h5 class="mb-3">Kondisi</h5>
                    <span class="badge bg-light text-dark fs-6">
                        <i class="bi bi-star-fill text-warning me-1"></i>
                        <?= ucfirst($kostum['kondisi'] ?? 'baik') ?>
                    </span>
                </div>
                
                <div class="d-grid gap-3">
                    <?php if ($kostum['stok_tersedia'] > 0): ?>
                        <a href="#bookingKostum" class="btn btn-costume btn-lg">
                            <i class="bi bi-calendar-check me-2"></i>Sewa Kostum Ini
                        </a>
                        <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20tertarik%20dengan%20kostum%20<?= urlencode($kostum['nama_kostum']) ?>%20<?= urlencode(base_url('sewa-kostum/' . $kostum['slug'])) ?>" 
                           class="btn btn-outline-success btn-lg" target="_blank">
                            <i class="bi bi-whatsapp me-2"></i>Tanya via WhatsApp
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="bi bi-x-circle me-2"></i>Kostum Sedang Tidak Tersedia
                        </button>
                        <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20bertanya%20tentang%20ketersediaan%20kostum%20<?= urlencode($kostum['nama_kostum']) ?>" 
                           class="btn btn-outline-primary btn-lg" target="_blank">
                            <i class="bi bi-question-circle me-2"></i>Tanya Ketersediaan
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Additional Info -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 bg-light">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Penting</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li class="mb-2">Masa sewa: <?= $kostum['durasi_sewa'] ?></li>
                                    <li class="mb-2">DP 50% untuk booking</li>
                                    <li class="mb-2">Boleh dicoba di studio sebelum sewa</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="mb-0">
                                    <li class="mb-2">Denda keterlambatan: Rp 50.000/hari</li>
                                    <li class="mb-2">Biaya cleaning sudah termasuk</li>
                                    <li class="mb-2">Bisa diantar (biaya tambah)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Kostum -->
<?php if (!empty($related_kostum)): ?>
<section class="section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Kostum Lainnya</h2>
        <div class="row">
            <?php foreach ($related_kostum as $item): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <?php if (!empty($item['gambar'])): ?>
                        <img src="<?= base_url('uploads/kostum/' . $item['gambar']) ?>" 
                             class="card-img-top" 
                             alt="<?= $item['nama_kostum'] ?>"
                             style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted fs-1"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <span class="badge bg-light text-dark mb-2"><?= ucfirst($item['kategori']) ?></span>
                        <h5 class="card-title"><?= character_limiter($item['nama_kostum'], 40) ?></h5>
                        <p class="card-text text-muted small"><?= character_limiter($item['deskripsi'], 60) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-primary">Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?></strong>
                            <a href="<?= base_url('sewa-kostum/' . $item['slug']) ?>" class="btn btn-sm btn-outline-primary">
                                Lihat <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Booking Form for This Kostum -->
<section id="bookingKostum" class="section-padding">
    <div class="container">
        <div class="booking-form-container">
            <h2 class="text-center mb-5">Sewa <?= $kostum['nama_kostum'] ?></h2>
            <p class="text-center mb-4">Isi form di bawah untuk booking kostum ini. Pastikan tanggal dan informasi yang Anda berikan sesuai.</p>
            
            <form id="bookingFormThisKostum">
                <input type="hidden" id="kostumName" value="<?= $kostum['nama_kostum'] ?> - Rp <?= number_format($kostum['harga_sewa'], 0, ',', '.') ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="fullNameDetail" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="fullNameDetail" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="phoneDetail" class="form-label">Nomor WhatsApp *</label>
                            <input type="tel" class="form-control" id="phoneDetail" required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="eventDateDetail" class="form-label">Tanggal Penggunaan *</label>
                            <input type="date" class="form-control" id="eventDateDetail" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="lokasiDetail" class="form-label">Lokasi Pengambilan *</label>
                            <select class="form-select" id="lokasiDetail" required>
                                <option value="" selected disabled>Pilih lokasi...</option>
                                <option value="ambil_di_studio">Ambil di Studio (Klambu, Grobogan)</option>
                                <option value="diantar_ke_lokasi">Diantar ke Lokasi (biaya tambah)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="ukuranDetail" class="form-label">Ukuran *</label>
                            <select class="form-select" id="ukuranDetail" required>
                                <option value="" selected disabled>Pilih ukuran...</option>
                                <option value="XS">XS (Extra Small)</option>
                                <option value="S">S (Small)</option>
                                <option value="M">M (Medium)</option>
                                <option value="L">L (Large)</option>
                                <option value="XL">XL (Extra Large)</option>
                                <option value="XXL">XXL (Double Extra Large)</option>
                                <option value="custom">Custom Size (akan diukur)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="jumlah" class="form-label">Jumlah Set</label>
                            <input type="number" class="form-control" id="jumlah" value="1" min="1" max="<?= $kostum['stok_tersedia'] ?>">
                            <small class="text-muted">Maksimal <?= $kostum['stok_tersedia'] ?> set tersedia</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="alamatDetail" class="form-label">Alamat Lengkap (Jika diantar) *</label>
                    <textarea class="form-control" id="alamatDetail" rows="3" placeholder="Isi alamat lengkap jika memilih diantar ke lokasi" required></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="additionalInfoDetail" class="form-label">Informasi Tambahan</label>
                    <textarea class="form-control" id="additionalInfoDetail" rows="3" placeholder="Catatan khusus, warna preferensi, dll."></textarea>
                </div>
                
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="agreeTermsDetail" required>
                    <label class="form-check-label" for="agreeTermsDetail">
                        Saya setuju dengan <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a> sewa kostum *
                        dan memahami bahwa DP 50% dibayarkan saat booking.
                    </label>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-costume btn-lg px-5">
                        <i class="bi bi-whatsapp me-2"></i>Booking via WhatsApp
                    </button>
                    <p class="text-muted mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        DP 50% (Rp <?= number_format($kostum['harga_sewa'] * 0.5, 0, ',', '.') ?>) dibayarkan saat booking
                    </p>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Additional Script for detail page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date for event date (tomorrow)
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('eventDateDetail').min = minDate;
    
    // Form submission for specific kostum booking
    const bookingFormThisKostum = document.getElementById('bookingFormThisKostum');
    bookingFormThisKostum.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const fullName = document.getElementById('fullNameDetail').value;
        const phone = document.getElementById('phoneDetail').value;
        const kostumName = document.getElementById('kostumName').value;
        const eventDate = document.getElementById('eventDateDetail').value;
        const lokasi = document.getElementById('lokasiDetail').value;
        const ukuran = document.getElementById('ukuranDetail').value;
        const jumlah = document.getElementById('jumlah').value;
        const alamat = document.getElementById('alamatDetail').value;
        const additionalInfo = document.getElementById('additionalInfoDetail').value;
        
        // Format date
        const formattedDate = new Date(eventDate).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        // Calculate DP
        const harga = <?= $kostum['harga_sewa'] ?>;
        const totalHarga = harga * jumlah;
        const dp = totalHarga * 0.5;
        
        // Create WhatsApp message
        const message = `Halo Maulia, saya ${fullName} ingin booking sewa kostum:
👗 Kostum: ${kostumName}
📏 Ukuran: ${ukuran}
🔢 Jumlah: ${jumlah} set
💰 Total: Rp ${totalHarga.toLocaleString('id-ID')} (DP 50%: Rp ${dp.toLocaleString('id-ID')})
📅 Tanggal Penggunaan: ${formattedDate}
📍 Lokasi: ${lokasi}
🏠 Alamat: ${alamat}
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
        alert('Terima kasih! Form booking kostum telah berhasil dikirim. Anda akan diarahkan ke WhatsApp untuk konfirmasi pembayaran DP.');
        
        // Reset form
        bookingFormThisKostum.reset();
    });
    
    // Show/hide alamat field based on lokasi selection
    const lokasiSelect = document.getElementById('lokasiDetail');
    const alamatTextarea = document.getElementById('alamatDetail');
    
    lokasiSelect.addEventListener('change', function() {
        if (this.value === 'diantar_ke_lokasi') {
            alamatTextarea.required = true;
            alamatTextarea.placeholder = "Isi alamat lengkap untuk pengantaran";
        } else {
            alamatTextarea.required = false;
            alamatTextarea.placeholder = "Isi alamat lengkap jika memilih diantar ke lokasi";
        }
    });
});
</script>

<?= $this->include('template/footer') ?>