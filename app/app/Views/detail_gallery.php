<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('gallery') ?>">Gallery</a></li>
                <li class="breadcrumb-item active"><?= $gallery['judul'] ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Gallery Detail -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <!-- Main Image & Gallery -->
            <div class="col-lg-7 mb-5 mb-lg-0">
                <div class="mb-4">
                    <img src="<?= base_url('uploads/gallery/' . $gallery['gambar']) ?>" 
                         alt="<?= $gallery['judul'] ?>" 
                         class="img-fluid rounded shadow-lg" 
                         id="mainGalleryImage">
                </div>
                
                <?php if (!empty($gallery['gambar_tambahan']) && is_array($gallery['gambar_tambahan'])): ?>
                <div class="row g-3">
                    <?php foreach ($gallery['gambar_tambahan'] as $image): ?>
                    <div class="col-4 col-md-3">
                        <img src="<?= base_url('uploads/gallery/tambahan/' . $image) ?>" 
                             alt="<?= $gallery['judul'] ?>" 
                             class="img-fluid rounded cursor-pointer gallery-thumb"
                             style="height: 100px; object-fit: cover;"
                             data-src="<?= base_url('uploads/gallery/tambahan/' . $image) ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Gallery Info -->
            <div class="col-lg-5">
                <div class="mb-4">
                    <span class="badge bg-light text-dark fs-6">
                        <?= $kategori_options[$gallery['kategori']] ?? ucfirst($gallery['kategori']) ?>
                    </span>
                    <?php if (!empty($gallery['style'])): ?>
                        <span class="badge bg-light text-dark fs-6 ms-2">
                            <?= $style_options[$gallery['style']] ?? $gallery['style'] ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if ($gallery['is_featured']): ?>
                        <span class="badge bg-warning fs-6 ms-2">
                            <i class="bi bi-star-fill"></i> Featured
                        </span>
                    <?php endif; ?>
                </div>
                
                <h1 class="mb-4"><?= $gallery['judul'] ?></h1>
                
                <?php if (!empty($gallery['deskripsi'])): ?>
                <div class="mb-4">
                    <p class="lead"><?= nl2br($gallery['deskripsi']) ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Gallery Details -->
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body">
                        <h5 class="mb-4">Detail Gallery</h5>
                        
                        <div class="row">
                            <?php if (!empty($gallery['tema_warna'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-palette text-primary me-2"></i> Tema Warna:</strong><br>
                                <span><?= $gallery['tema_warna'] ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($gallery['produk_digunakan'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-brush text-warning me-2"></i> Produk Digunakan:</strong><br>
                                <span><?= $gallery['produk_digunakan'] ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($gallery['lokasi_pemotretan'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-geo-alt text-success me-2"></i> Lokasi:</strong><br>
                                <span><?= $gallery['lokasi_pemotretan'] ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($gallery['makeup_artist'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-person-check text-info me-2"></i> Makeup Artist:</strong><br>
                                <span><?= $gallery['makeup_artist'] ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($gallery['model'])): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-person text-secondary me-2"></i> Model:</strong><br>
                                <span><?= $gallery['model'] ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="col-md-6 mb-3">
                                <strong><i class="bi bi-calendar text-danger me-2"></i> Tanggal:</strong><br>
                                <span><?= date('d F Y', strtotime($gallery['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Style Tips (for makeup) -->
                <?php if ($gallery['kategori'] == 'makeup' && !empty($gallery['style'])): ?>
                <div class="card border-0 mb-4" style="border-left: 4px solid var(--accent-dark) !important;">
                    <div class="card-body">
                        <h5><i class="bi bi-lightbulb text-warning me-2"></i> Tips Style <?= $style_options[$gallery['style']] ?? $gallery['style'] ?></h5>
                        <p class="mb-0">
                            <?php
                            $style_tips = [
                                'tradisional' => 'Cocok untuk pernikahan adat. Warna dominan merah dan emas. Eyeshadow smokey dengan eyeliner tegas.',
                                'modern' => 'Fokus pada kulit flawless dan highlight. Kontur natural dengan blush on soft.',
                                'natural' => 'Gunakan foundation ringan. Highlight mata bagian dalam. Lipstik warna nude atau soft pink.',
                                'glamour' => 'Bold eyeshadow dengan glitter. Contour kuat. Lipstik warna bold seperti merah atau plum.',
                                'kultural' => 'Sesuaikan dengan budaya pengantin. Untuk Jawa: sanggul dan paes. Untuk Bali: pusung dan gelungan.'
                            ];
                            echo $style_tips[$gallery['style']] ?? 'Konsultasikan dengan makeup artist untuk tips khusus style ini.';
                            ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="d-grid gap-3">
                    <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20tertarik%20dengan%20gallery%20<?= urlencode($gallery['judul']) ?>%20dan%20ingin%20membuat%20appointment%20untuk%20style%20ini." 
                       class="btn btn-success btn-lg" 
                       target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>Booking Style Ini
                    </a>
                    
                    <a href="<?= base_url('gallery') ?>" 
                       class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Lihat Gallery Lainnya
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Gallery -->
<?php if (!empty($related_gallery)): ?>
<section class="section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Gallery Terkait</h2>
        <div class="row g-4">
            <?php foreach ($related_gallery as $item): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-img-top" style="height: 200px; overflow: hidden;">
                        <img src="<?= base_url('uploads/gallery/' . $item['gambar']) ?>" 
                             class="img-fluid w-100 h-100" 
                             alt="<?= $item['judul'] ?>"
                             style="object-fit: cover;">
                    </div>
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-dark">
                                <?= $kategori_options[$item['kategori']] ?? ucfirst($item['kategori']) ?>
                            </span>
                            <?php if ($item['is_featured']): ?>
                                <span class="badge bg-warning">
                                    <i class="bi bi-star-fill"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="card-title"><?= character_limiter($item['judul'], 40) ?></h5>
                        <p class="card-text text-muted small"><?= character_limiter($item['deskripsi'] ?? '', 60) ?></p>
                        
                        <a href="<?= base_url('gallery/' . $item['id']) ?>" 
                           class="btn btn-sm btn-outline-primary w-100">
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

<!-- Style Inspiration Section -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="mb-4">Dapatkan Inspirasi Lebih Banyak</h2>
                <p class="lead mb-4">Ikuti kami di Instagram untuk melihat karya terbaru dan tips makeup langsung dari tim kami.</p>
                
                <div class="d-flex gap-3">
                    <a href="<?= $pengaturan['instagram'] ?? 'https://instagram.com/maulia' ?>" 
                       class="btn btn-instagram btn-lg" 
                       target="_blank">
                        <i class="bi bi-instagram me-2"></i>@maulia
                    </a>
                    
                    <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20konsultasi%20tentang%20style%20makeup%20untuk%20pernikahan." 
                       class="btn btn-success btn-lg" 
                       target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>Konsultasi
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3">Pertanyaan tentang Style?</h5>
                        <p class="text-muted mb-4">Jangan ragu untuk bertanya tentang style makeup yang sesuai dengan kepribadian dan tema pernikahan Anda.</p>
                        
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Konsultasi gratis via WhatsApp
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Trial makeup untuk mencoba style
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Rekomendasi berdasarkan tema pernikahan
                            </li>
                            <li>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Panduan produk yang sesuai kulit
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS -->
<style>
.gallery-thumb {
    transition: all 0.3s ease;
    cursor: pointer;
}

.gallery-thumb:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-instagram {
    background: linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D);
    color: white;
    border: none;
}

.btn-instagram:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.card-img-top {
    border-radius: 8px 8px 0 0;
}

.badge.bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6;
}
</style>

<!-- Additional Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail click to change main image
    const thumbnails = document.querySelectorAll('.gallery-thumb');
    const mainImage = document.getElementById('mainGalleryImage');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const newSrc = this.getAttribute('data-src');
            if (newSrc && mainImage) {
                mainImage.src = newSrc;
                
                // Add active class to clicked thumbnail
                thumbnails.forEach(t => t.classList.remove('border', 'border-primary'));
                this.classList.add('border', 'border-primary');
            }
        });
    });
    
    // Share functionality
    const shareButtons = document.querySelectorAll('.share-gallery');
    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const title = '<?= addslashes($gallery['judul']) ?>';
            const url = window.location.href;
            const text = 'Lihat gallery makeup ini dari Maulia Wedding: ' + title;
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: text,
                    url: url
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
                window.open(shareUrl, '_blank');
            }
        });
    });
});
</script>

<?= $this->include('template/footer') ?>