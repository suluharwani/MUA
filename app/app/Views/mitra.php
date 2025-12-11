<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Hero Section -->
<section class="hero-section section-padding" style="background: linear-gradient(rgba(255, 255, 255, 0.92), rgba(249, 247, 244, 0.92)), url('https://images.unsplash.com/photo-1465495976277-4387d4b0e4a6?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="location-badge">
                    <i class="bi bi-people-fill me-1"></i> Mitra Pernikahan
                </div>
                <h1>Mitra Pernikahan Terpercaya</h1>
                <p class="lead">Temukan berbagai vendor pernikahan terbaik di Grobogan dan sekitarnya. Fotografer, WO, catering, dekorasi, dan masih banyak lagi.</p>
            </div>
        </div>
    </div>
</section>

<!-- Search & Filter -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
    
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Cari mitra pernikahan..." 
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
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                          </i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Kategori Menu -->
<section class="py-4" style="background-color: white;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="<?= base_url('mitra') ?>" 
                       class="btn btn-outline-secondary btn-sm rounded-pill mx-1 mb-2 <?= !$kategori_aktif ? 'active' : '' ?>">
                        Semua Mitra
                    </a>
                    <?php foreach ($kategori_options as $value => $label): ?>
                    <a href="<?= base_url('mitra?kategori=' . $value) ?>" 
                       class="btn btn-outline-secondary btn-sm rounded-pill mx-1 mb-2 <?= ($kategori_aktif == $value) ? 'active' : '' ?>">
                        <?= $label ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mitra List Section -->
<section class="section-padding">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="section-title"><?= $kategori_aktif ? $kategori_options[$kategori_aktif] : 'Semua Mitra' ?></h2>
                <p class="text-muted"><?= $kategori_aktif ? 'Mitra terpercaya untuk ' . strtolower($kategori_options[$kategori_aktif]) : 'Berbagai vendor pernikahan terbaik untuk hari istimewa Anda' ?></p>
            </div>
        </div>
        
        <?php if (empty($mitra)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                <?php if ($search_term): ?>
                    Tidak ditemukan mitra dengan kata kunci "<?= $search_term ?>".
                <?php elseif ($kategori_aktif): ?>
                    Belum ada mitra dalam kategori <?= $kategori_options[$kategori_aktif] ?>.
                <?php else: ?>
                    Belum ada mitra yang terdaftar.
                <?php endif; ?>
                <a href="<?= base_url('mitra') ?>" class="alert-link ms-2">Lihat semua mitra</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($mitra as $item): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="mitra-card h-100">
                        <?php if ($item['is_featured']): ?>
                            <div class="featured-badge">REKOMENDASI</div>
                        <?php endif; ?>
                        
                        <div class="mitra-image mb-4" style="height: 200px; overflow: hidden; border-radius: 8px;">
                            <?php if (!empty($item['gambar'])): ?>
                                <img src="<?= base_url('uploads/mitra/' . $item['gambar']) ?>" 
                                     alt="<?= $item['nama_mitra'] ?>" 
                                     class="img-fluid w-100 h-100" 
                                     style="object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-building fs-1 text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mitra-badge mb-2">
                            <?= $kategori_options[$item['kategori']] ?? ucfirst($item['kategori']) ?>
                        </div>
                        
                        <h4 class="mb-2"><?= $item['nama_mitra'] ?></h4>
                        
                        <!-- Rating -->
                        <div class="mb-3">
                            <?php
                            $rating = $item['rating'] ?? 5.0;
                            $fullStars = floor($rating);
                            $halfStar = ($rating - $fullStars) >= 0.5;
                            ?>
                            <div class="d-flex align-items-center">
                                <div class="text-warning me-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $fullStars): ?>
                                            <i class="bi bi-star-fill"></i>
                                        <?php elseif ($i == $fullStars + 1 && $halfStar): ?>
                                            <i class="bi bi-star-half"></i>
                                        <?php else: ?>
                                            <i class="bi bi-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span class="text-muted">(<?= number_format($rating, 1) ?>)</span>
                                <span class="ms-2 small text-muted">
                                    <i class="bi bi-clock-history me-1"></i>
                                    <?= $item['pengalaman'] ?>
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-muted mb-3"><?= character_limiter($item['deskripsi'], 100) ?></p>
                        
                        <?php if (!empty($item['layanan']) && is_array($item['layanan'])): ?>
                            <div class="mb-3">
                                <?php foreach (array_slice($item['layanan'], 0, 3) as $service): ?>
                                    <span class="badge bg-light text-dark me-1 mb-1"><?= $service ?></span>
                                <?php endforeach; ?>
                                <?php if (count($item['layanan']) > 3): ?>
                                    <span class="badge bg-light text-muted">+<?= count($item['layanan']) - 3 ?> lainnya</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['harga_mulai'])): ?>
                            <div class="mb-3">
                                <strong class="text-primary">Mulai Rp <?= number_format($item['harga_mulai'], 0, ',', '.') ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid gap-2 mt-4">
                            <a href="<?= base_url('mitra/' . $item['slug']) ?>" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Why Choose Our Partners -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="mb-4">Mengapa Memilih Mitra Kami?</h2>
                <p class="lead mb-4">Semua mitra telah melalui proses seleksi ketat untuk memastikan kualitas dan profesionalisme.</p>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                            </div>
                            <div>
                                <h5>Terpercaya</h5>
                                <p class="mb-0">Mitra telah diverifikasi dan memiliki reputasi baik</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-star"></i>
                                </div>
                            </div>
                            <div>
                                <h5>Berkualitas</h5>
                                <p class="mb-0">Standar kualitas tinggi untuk hasil terbaik</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                            </div>
                            <div>
                                <h5>Harga Kompetitif</h5>
                                <p class="mb-0">Harga transparan sesuai kualitas</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-headset"></i>
                                </div>
                            </div>
                            <div>
                                <h5>Support Penuh</h5>
                                <p class="mb-0">Kami membantu komunikasi dengan mitra</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h3 class="mb-4">Ingin Menjadi Mitra?</h3>
                        <p class="mb-4">Apakah Anda penyedia jasa pernikahan di Grobogan dan sekitarnya? Bergabunglah dengan jaringan mitra kami untuk mendapatkan lebih banyak pelanggan.</p>
                        
                        <div class="mb-4">
                            <h6><i class="bi bi-check-circle text-success me-2"></i>Keuntungan Jadi Mitra:</h6>
                            <ul class="mb-0">
                                <li class="mb-2">Promosi gratis di website kami</li>
                                <li class="mb-2">Rekomendasi ke pelanggan Maulia</li>
                                <li class="mb-2">Networking dengan vendor lain</li>
                                <li class="mb-2">Support sistem booking terintegrasi</li>
                            </ul>
                        </div>
                        
                        <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20bergabung%20sebagai%20mitra%20pernikahan." 
                           class="btn btn-primary btn-lg w-100" 
                           target="_blank">
                            <i class="bi bi-whatsapp me-2"></i>Daftar Jadi Mitra
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--accent-dark) 0%, var(--accent-color) 100%); color: white;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Butuh Rekomendasi Mitra?</h3>
                <p class="mb-0">Konsultasikan kebutuhan pernikahan Anda dan dapatkan rekomendasi mitra terbaik sesuai budget dan tema.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20konsultasi%20tentang%20pemilihan%20mitra%20pernikahan." 
                   class="btn btn-light btn-lg" 
                   target="_blank">
                    <i class="bi bi-whatsapp me-2"></i>Konsultasi Gratis
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS -->
<style>
.mitra-card {
    background-color: white;
    border-radius: 10px;
    padding: 25px;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    position: relative;
    overflow: hidden;
}

.mitra-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    border-color: var(--accent-color);
}

.mitra-badge {
    background-color: var(--accent-color);
    color: var(--heading-color);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 10px;
}

.mitra-image {
    position: relative;
}

.mitra-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 70%, rgba(0,0,0,0.1));
    border-radius: 8px;
}

.featured-badge {
    position: absolute;
    top: 20px;
    right: -35px;
    background-color: var(--success-color);
    color: white;
    padding: 8px 40px;
    transform: rotate(45deg);
    font-size: 0.85rem;
    font-weight: 600;
    z-index: 1;
}

.btn-outline-secondary.active {
    background-color: var(--accent-dark);
    color: white;
    border-color: var(--accent-dark);
}
</style>

<?= $this->include('template/footer') ?>