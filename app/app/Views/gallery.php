<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Hero Section -->
<section class="hero-section section-padding" style="background: linear-gradient(rgba(255, 255, 255, 0.92), rgba(249, 247, 244, 0.92)), url('https://images.unsplash.com/photo-1519741497674-611481863552?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="location-badge">
                    <i class="bi bi-images me-1"></i> Gallery
                </div>
                <h1>Gallery Makeup & Kostum</h1>
                <p class="lead">Lihat koleksi makeup pernikahan, kostum, dan portfolio kami. Temukan inspirasi untuk gaya pernikahan impian Anda.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Gallery -->
<?php if (!empty($featured_gallery)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Gallery Unggulan</h2>
        <div class="row g-4">
            <?php foreach ($featured_gallery as $item): ?>
            <div class="col-lg-3 col-md-6">
                <a href="<?= base_url('gallery/' . $item['id']) ?>" class="gallery-featured-card text-decoration-none">
                    <div class="position-relative overflow-hidden rounded" style="height: 250px;">
                        <img src="<?= base_url('uploads/gallery/' . $item['gambar']) ?>" 
                             alt="<?= $item['judul'] ?>" 
                             class="img-fluid w-100 h-100" 
                             style="object-fit: cover;">
                        <div class="gallery-overlay">
                            <div class="gallery-info">
                                <h6 class="mb-1"><?= character_limiter($item['judul'], 30) ?></h6>
                                <small class="text-white-50"><?= $kategori_options[$item['kategori']] ?? ucfirst($item['kategori']) ?></small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Search & Filter -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <form method="get" class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari gallery..." 
                                   value="<?= $search_term ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategori_options as $value => $label): ?>
                            <option value="<?= $value ?>" <?= ($kategori_aktif == $value) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                    </div>
                </form>
                
                <!-- Style Filters (for makeup) -->
                <div class="mt-4">
                    <h6 class="mb-3">Filter Style Makeup:</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= base_url('gallery') . ($kategori_aktif ? '?kategori=' . $kategori_aktif : '') ?>" 
                           class="btn btn-sm btn-outline-secondary rounded-pill <?= !$style_aktif ? 'active' : '' ?>">
                            Semua Style
                        </a>
                        <?php foreach ($style_options as $value => $label): ?>
                        <a href="<?= base_url('gallery?style=' . $value) . ($kategori_aktif ? '&kategori=' . $kategori_aktif : '') ?>" 
                           class="btn btn-sm btn-outline-secondary rounded-pill <?= ($style_aktif == $value) ? 'active' : '' ?>">
                            <?= $label ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="section-padding">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title"><?= $kategori_aktif ? $kategori_options[$kategori_aktif] : 'Semua Gallery' ?></h2>
                <p class="text-muted"><?= count($gallery) ?> hasil ditemukan</p>
            </div>
        </div>
        
        <?php if (empty($gallery)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                <?php if ($search_term): ?>
                    Tidak ditemukan gallery dengan kata kunci "<?= $search_term ?>".
                <?php else: ?>
                    Belum ada gallery yang tersedia.
                <?php endif; ?>
                <a href="<?= base_url('gallery') ?>" class="alert-link ms-2">Lihat semua gallery</a>
            </div>
        <?php else: ?>
            <div class="row g-4" id="galleryGrid">
                <?php foreach ($gallery as $item): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="gallery-card">
                        <a href="<?= base_url('gallery/' . $item['id']) ?>">
                            <div class="gallery-image" style="height: 300px; overflow: hidden; border-radius: 10px;">
                                <img src="<?= base_url('uploads/gallery/' . $item['gambar']) ?>" 
                                     alt="<?= $item['judul'] ?>" 
                                     class="img-fluid w-100 h-100" 
                                     style="object-fit: cover;">
                            </div>
                        </a>
                        
                        <div class="gallery-content mt-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-light text-dark">
                                        <?= $kategori_options[$item['kategori']] ?? ucfirst($item['kategori']) ?>
                                    </span>
                                    <?php if (!empty($item['style'])): ?>
                                        <span class="badge bg-light text-dark ms-1">
                                            <?= $style_options[$item['style']] ?? $item['style'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($item['is_featured']): ?>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-star-fill"></i> Featured
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <h5 class="mb-2"><?= $item['judul'] ?></h5>
                            
                            <?php if (!empty($item['deskripsi'])): ?>
                                <p class="text-muted small mb-3"><?= character_limiter($item['deskripsi'], 80) ?></p>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= date('d M Y', strtotime($item['created_at'])) ?>
                                </small>
                                <a href="<?= base_url('gallery/' . $item['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-zoom-in me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Style Guide Section -->
<section class="section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Panduan Style Makeup</h2>
        <div class="row g-4">
            <?php
            $style_guide = [
                'tradisional' => [
                    'icon' => 'bi-flower3',
                    'color' => '#d4b8a3',
                    'desc' => 'Makeup klasik dengan sentuhan tradisional Indonesia, cocok untuk pernikahan adat.'
                ],
                'modern' => [
                    'icon' => 'bi-stars',
                    'color' => '#b8a7c8',
                    'desc' => 'Makeup kontemporer dengan teknik terbaru, untuk tampilan fresh dan fashionable.'
                ],
                'natural' => [
                    'icon' => 'bi-tree',
                    'color' => '#a8c8b8',
                    'desc' => 'Tampilan natural namun tetap elegan, menonjolkan keindahan alami wajah.'
                ],
                'glamour' => [
                    'icon' => 'bi-gem',
                    'color' => '#ffd700',
                    'desc' => 'Makeup dramatis dengan highlight dan contouring untuk pesta malam.'
                ],
                'kultural' => [
                    'icon' => 'bi-house-heart',
                    'color' => '#dc3545',
                    'desc' => 'Makeup khusus budaya seperti Jawa, Sunda, Bali dengan ciri khas masing-masing.'
                ]
            ];
            
            foreach ($style_guide as $key => $guide):
                if (isset($style_options[$key])):
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center" 
                                 style="width: 80px; height: 80px; background-color: <?= $guide['color'] ?>;">
                                <i class="bi <?= $guide['icon'] ?> fs-3 text-white"></i>
                            </div>
                        </div>
                        <h5 class="mb-3"><?= $style_options[$key] ?></h5>
                        <p class="text-muted mb-0"><?= $guide['desc'] ?></p>
                        <a href="<?= base_url('gallery?style=' . $key) ?>" class="btn btn-outline-primary btn-sm mt-3">
                            Lihat Contoh <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; endforeach; ?>
        </div>
    </div>
</section>

<!-- Consultation CTA -->
<section class="py-5" style="background: linear-gradient(135deg, var(--accent-dark) 0%, var(--accent-color) 100%); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Inspirasi dari Gallery Kami?</h3>
                <p class="mb-0">Konsultasikan style makeup dan tema pernikahan yang Anda inginkan. Tim kami siap membantu mewujudkannya.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20terinspirasi%20dari%20gallery%20Anda%20dan%20ingin%20konsultasi%20style%20makeup." 
                   class="btn btn-light btn-lg" 
                   target="_blank">
                    <i class="bi bi-whatsapp me-2"></i>Konsultasi Style
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS -->
<style>
.gallery-card {
    transition: all 0.3s ease;
}

.gallery-card:hover {
    transform: translateY(-5px);
}

.gallery-card:hover .gallery-image img {
    transform: scale(1.05);
}

.gallery-image {
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.gallery-image img {
    transition: transform 0.5s ease;
}

.gallery-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    padding: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-featured-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info {
    color: white;
}

.cursor-pointer {
    cursor: pointer;
}

/* Style guide cards */
.card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}
</style>

<?= $this->include('template/footer') ?>