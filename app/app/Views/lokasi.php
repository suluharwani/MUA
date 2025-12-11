<?= $this->include('template/header') ?>
<?= $this->include('template/navbar') ?>

<!-- Hero Section -->
<section class="hero-section section-padding" style="background: linear-gradient(rgba(255, 255, 255, 0.92), rgba(249, 247, 244, 0.92)), url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="location-badge">
                    <i class="bi bi-geo-alt me-1"></i> Grobogan, Jawa Tengah
                </div>
                <h1>Lokasi & Area Layanan</h1>
                <p class="lead">Temukan lokasi studio kami dan cakupan area layanan makeup serta sewa kostum pernikahan di Jawa Tengah.</p>
            </div>
        </div>
    </div>
</section>

<!-- Location Info & Map -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="section-title mb-4">Studio Maulia</h2>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1">Alamat Studio</h5>
                                <p class="mb-0"><?= $pengaturan['alamat'] ?? 'Desa Klambu, Kecamatan Klambu, Kabupaten Grobogan, Jawa Tengah' ?></p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-clock-fill"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1">Jam Operasional</h5>
                                <p class="mb-0"><?= $pengaturan['jam_kerja'] ?? 'Senin - Minggu: 08:00 - 18:00 WIB' ?></p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1">Kontak</h5>
                                <p class="mb-1">
                                    <i class="bi bi-whatsapp text-success me-2"></i>
                                    <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>" class="text-decoration-none">
                                        <?= $pengaturan['whatsapp'] ?? '0877-3131-0979' ?> (WhatsApp)
                                    </a>
                                </p>
                                <?php if (!empty($pengaturan['telepon'])): ?>
                                <p class="mb-0">
                                    <i class="bi bi-telephone text-primary me-2"></i>
                                    <?= $pengaturan['telepon'] ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($pengaturan['email'])): ?>
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1">Email</h5>
                                <p class="mb-0">
                                    <i class="bi bi-envelope text-warning me-2"></i>
                                    <a href="mailto:<?= $pengaturan['email'] ?>" class="text-decoration-none">
                                        <?= $pengaturan['email'] ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="https://maps.app.goo.gl/KaDJLxzmVYHNynfs5" 
                       class="btn btn-primary btn-lg" 
                       target="_blank">
                        <i class="bi bi-google me-2"></i>Buka di Google Maps
                    </a>
                    
                    <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20bertanya%20arah%20ke%20studio%20Anda%20di%20<?= urlencode($pengaturan['alamat'] ?? 'Klambu, Grobogan') ?>" 
                       class="btn btn-success btn-lg" 
                       target="_blank">
                        <i class="bi bi-whatsapp me-2"></i>Tanya Arah via WhatsApp
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-0">
                        <div id="map" style="height: 500px; border-radius: 8px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Areas -->
<section class="section-padding bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Area Layanan Kami</h2>
        <p class="text-center mb-5">Kami melayani makeup dan sewa kostum pernikahan di berbagai daerah di Jawa Tengah. Biaya transportasi akan disesuaikan dengan jarak lokasi.</p>
        
        <?php if (empty($area_layanan)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle me-2"></i>
                Informasi area layanan sedang diperbarui. Silakan hubungi kami untuk informasi lebih lanjut.
            </div>
        <?php else: ?>
            <div class="row">
                <?php 
                $main_areas = array_filter($area_layanan, function($area) {
                    return $area['jenis_area'] === 'utama';
                });
                
                $secondary_areas = array_filter($area_layanan, function($area) {
                    return $area['jenis_area'] === 'sekunder';
                });
                ?>
                
                <!-- Area Utama -->
                <?php if (!empty($main_areas)): ?>
                <div class="col-lg-6 mb-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0">
                            <h4 class="mb-0 text-primary">
                                <i class="bi bi-house-heart me-2"></i>Area Utama
                            </h4>
                            <p class="text-muted mb-0">Layanan tanpa biaya transport tambahan</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($main_areas as $area): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="area-card main h-100">
                                        <div class="area-icon">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <h5><?= $area['nama_area'] ?></h5>
                                        <p class="small mb-2"><?= $area['keterangan'] ?? 'Seluruh wilayah' ?></p>
                                        <div class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Gratis Transport
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Area Sekunder -->
                <?php if (!empty($secondary_areas)): ?>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0">
                            <h4 class="mb-0 text-primary">
                                <i class="bi bi-truck me-2"></i>Area Sekunder
                            </h4>
                            <p class="text-muted mb-0">Layanan dengan biaya transport tambahan</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($secondary_areas as $area): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="area-card h-100">
                                        <div class="area-icon">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                        <h5><?= $area['nama_area'] ?></h5>
                                        <p class="small mb-2"><?= $area['keterangan'] ?? 'Biaya transport +' ?></p>
                                        <?php if (!empty($area['biaya_tambahan']) && $area['biaya_tambahan'] > 0): ?>
                                        <div class="badge bg-warning text-dark">
                                            <i class="bi bi-currency-exchange me-1"></i>
                                            +Rp <?= number_format($area['biaya_tambahan'], 0, ',', '.') ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="badge bg-info">
                                            <i class="bi bi-info-circle me-1"></i>Biaya menyesuaikan
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Transport Info -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-4 text-center">
                            <i class="bi bi-info-circle me-2 text-primary"></i>
                            Informasi Transportasi
                        </h4>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-3">Cara Penghitungan Biaya:</h6>
                                <ul class="mb-0">
                                    <li class="mb-2">Area utama: <strong>Gratis</strong> biaya transport</li>
                                    <li class="mb-2">Area sekunder: Biaya sesuai jarak</li>
                                    <li class="mb-2">Biaya dihitung per kilometer dari studio</li>
                                    <li class="mb-2">Include pulang-pergi</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-3">Prosedur Layanan:</h6>
                                <ul class="mb-0">
                                    <li class="mb-2">Konfirmasi lokasi via WhatsApp</li>
                                    <li class="mb-2">Perhitungan biaya transport</li>
                                    <li class="mb-2">Pembayaran DP 50%</li>
                                    <li class="mb-2">Tim berangkat H-1 untuk persiapan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Lokasi -->
<section class="section-padding">
    <div class="container">
        <h2 class="section-title text-center mb-5">Pertanyaan Umum Lokasi & Transport</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqLokasi">
                    <!-- FAQ 1 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqL1">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Berapa estimasi biaya transport untuk daerah di luar Grobogan?
                            </button>
                        </h3>
                        <div id="faqL1" class="accordion-collapse collapse" data-bs-parent="#faqLokasi">
                            <div class="accordion-body">
                                Biaya transport untuk area sekunder mulai dari Rp 50.000 - Rp 500.000 tergantung jarak. Contoh: Kudus ±Rp 150.000, Demak ±Rp 100.000, Semarang ±Rp 500.000. Biaya akan dikonfirmasi saat konsultasi berdasarkan alamat lengkap.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 2 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqL2">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Apakah bisa fitting kostum atau trial makeup di lokasi kami?
                            </button>
                        </h3>
                        <div id="faqL2" class="accordion-collapse collapse" data-bs-parent="#faqLokasi">
                            <div class="accordion-body">
                                Trial makeup dan fitting kostum sebaiknya dilakukan di studio kami di Klambu, Grobogan. Ini untuk memastikan fasilitas dan peralatan yang lengkap. Namun untuk kondisi khusus, bisa dibicarakan dengan biaya tambahan untuk mobilitas tim.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 3 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqL3">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Berapa lama perjalanan dari studio ke lokasi pernikahan?
                            </button>
                        </h3>
                        <div id="faqL3" class="accordion-collapse collapse" data-bs-parent="#faqLokasi">
                            <div class="accordion-body">
                                Perkiraan waktu tempuh dari studio di Klambu, Grobogan:
                                <ul class="mt-2">
                                    <li>Purwodadi: 30-45 menit</li>
                                    <li>Kudus: 1-1.5 jam</li>
                                    <li>Demak: 1-1.5 jam</li>
                                    <li>Semarang: 2-2.5 jam</li>
                                    <li>Pati: 1.5-2 jam</li>
                                </ul>
                                Tim akan berangkat lebih awal untuk mengantisipasi kondisi jalan.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 4 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqL4">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Apakah ada minimal order untuk layanan di luar kota?
                            </button>
                        </h3>
                        <div id="faqL4" class="accordion-collapse collapse" data-bs-parent="#faqLokasi">
                            <div class="accordion-body">
                                Tidak ada minimal order, namun untuk area di luar Grobogan dengan jarak >50km, kami menyarankan paket minimal Premium untuk makeup atau 2 set kostum untuk efisiensi biaya transport. Konsultasikan kebutuhan Anda untuk solusi terbaik.
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ 5 -->
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#faqL5">
                                <i class="bi bi-question-circle me-2 text-primary"></i>
                                Bagaimana jika lokasi pernikahan sangat terpencil/sulit dijangkau?
                            </button>
                        </h3>
                        <div id="faqL5" class="accordion-collapse collapse" data-bs-parent="#faqLokasi">
                            <div class="accordion-body">
                                Untuk lokasi yang sulit dijangkau, kami akan survey terlebih dahulu. Biaya transport mungkin lebih tinggi dari estimasi standar. Pastikan memberikan detail akses jalan, kondisi jalan, dan titik temu yang mudah dijangkau. Kami selalu berusaha melayani dengan baik meski di lokasi terpencil.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Check Location Form -->
<section class="section-padding" style="background-color: var(--accent-color);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="mb-3">Cek Biaya Layanan di Lokasi Anda</h3>
                <p class="mb-0">Ketik nama daerah/kecamatan/kabupaten Anda untuk mengetahui estimasi biaya transport dan ketersediaan layanan.</p>
            </div>
            <div class="col-lg-4">
                <form id="checkLocationForm" class="d-flex">
                    <input type="text" 
                           class="form-control form-control-lg me-2" 
                           id="lokasiInput" 
                           placeholder="Contoh: Purwodadi, Kudus">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-5 text-center">
                        <h2 class="mb-4">Masih Ada Pertanyaan tentang Lokasi?</h2>
                        <p class="lead mb-4">Hubungi kami untuk informasi lebih detail tentang arah ke studio, biaya transport ke lokasi Anda, atau konsultasi kebutuhan pernikahan.</p>
                        
                        <div class="row g-3 justify-content-center">
                            <div class="col-lg-4 col-md-6">
                                <a href="https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20ingin%20bertanya%20tentang%20lokasi%20dan%20biaya%20transport" 
                                   class="btn btn-success btn-lg w-100" 
                                   target="_blank">
                                    <i class="bi bi-whatsapp me-2"></i>Chat WhatsApp
                                </a>
                            </div>
                            
                            <div class="col-lg-4 col-md-6">
                                <a href="tel:<?= $pengaturan['telepon'] ?? $pengaturan['whatsapp'] ?? '6287731310979' ?>" 
                                   class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-telephone me-2"></i>Telepon Sekarang
                                </a>
                            </div>
                            
                            <?php if (!empty($pengaturan['email'])): ?>
                            <div class="col-lg-4 col-md-6">
                                <a href="mailto:<?= $pengaturan['email'] ?>" 
                                   class="btn btn-outline-primary btn-lg w-100">
                                    <i class="bi bi-envelope me-2"></i>Kirim Email
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Script -->
<script>
// Initialize Map
function initMap() {
    // Coordinates from settings
    const studioCoords = [<?= $latitude ?>, <?= $longitude ?>];
    
    // Create map
    const map = L.map('map').setView(studioCoords, 14);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Custom icon
    const weddingIcon = L.divIcon({
        html: '<div style="background-color: var(--accent-dark); color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 10px rgba(0,0,0,0.2);"><i class="bi bi-heart-fill"></i></div>',
        className: 'custom-div-icon',
        iconSize: [50, 50],
        iconAnchor: [25, 25],
        popupAnchor: [0, -25]
    });
    
    // Add marker
    L.marker(studioCoords, { icon: weddingIcon }).addTo(map)
        .bindPopup(`
            <div style="min-width: 200px;">
                <h5 class="mb-2"><i class="bi bi-heart-fill text-danger me-1"></i> <strong>Studio Maulia</strong></h5>
                <p class="mb-1"><i class="bi bi-geo-alt text-primary me-1"></i> <?= $pengaturan['alamat'] ?? 'Desa Klambu, Grobogan' ?></p>
                <p class="mb-2"><i class="bi bi-clock text-warning me-1"></i> <?= $pengaturan['jam_kerja'] ?? '08:00-18:00 WIB' ?></p>
                <a href="https://maps.google.com/?q=<?= urlencode($pengaturan['alamat'] ?? 'Klambu, Grobogan') ?>" 
                   target="_blank" 
                   class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-arrow-up-right-square me-1"></i> Buka di Google Maps
                </a>
            </div>
        `)
        .openPopup();
    
    // Add circle to show service area
    L.circle(studioCoords, {
        color: 'var(--accent-dark)',
        fillColor: 'var(--accent-color)',
        fillOpacity: 0.2,
        radius: 15000 // 15km radius for main area
    }).addTo(map).bindPopup("Area Layanan Utama (Gratis Transport)");
}
</script>

<!-- Additional Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    initMap();
    
    // Location check form
    const checkLocationForm = document.getElementById('checkLocationForm');
    const lokasiInput = document.getElementById('lokasiInput');
    
    checkLocationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const location = lokasiInput.value.trim();
        
        if (!location) {
            alert('Silakan ketik nama daerah terlebih dahulu.');
            return;
        }
        
        // Simple area matching (in a real app, this would be an AJAX call)
        const mainAreas = ['grobogan', 'klambu', 'purwodadi'];
        const secondaryAreas = {
            'kudus': 'Rp 150.000',
            'demak': 'Rp 100.000',
            'pati': 'Rp 200.000',
            'blora': 'Rp 250.000',
            'rembang': 'Rp 300.000',
            'semarang': 'Rp 500.000'
        };
        
        const locationLower = location.toLowerCase();
        let message = '';
        
        // Check if it's a main area
        if (mainAreas.some(area => locationLower.includes(area))) {
            message = `✅ Lokasi "${location}" termasuk dalam AREA UTAMA kami.\n\nBiaya transport: GRATIS\nLayanan: Makeup & Sewa Kostum\n\nHubungi kami untuk konsultasi lebih lanjut.`;
        }
        // Check if it's a secondary area
        else {
            let found = false;
            for (const [key, value] of Object.entries(secondaryAreas)) {
                if (locationLower.includes(key)) {
                    message = `📍 Lokasi "${location}" termasuk dalam AREA SEKUNDER kami.\n\nEstimasi biaya transport: ${value}\nLayanan: Makeup & Sewa Kostum\n*Biaya bisa berubah sesuai alamat detail\n\nHubungi kami untuk perhitungan biaya yang lebih akurat.`;
                    found = true;
                    break;
                }
            }
            
            if (!found) {
                message = `🌍 Lokasi "${location}" mungkin di luar area layanan standar kami.\n\nUntuk lokasi di luar Jawa Tengah atau daerah yang tidak tercantum, silakan hubungi kami via WhatsApp untuk konsultasi khusus.\n\nTim kami akan menghitung biaya transport berdasarkan jarak dari studio.`;
            }
        }
        
        // Show result with option to contact
        if (confirm(message + "\n\nIngin konsultasi via WhatsApp?")) {
            const whatsappURL = `https://wa.me/<?= $pengaturan['whatsapp'] ?? '6287731310979' ?>?text=Halo%20Maulia,%20saya%20berada%20di%20${encodeURIComponent(location)}%20dan%20ingin%20konsultasi%20tentang%20layanan%20pernikahan.`;
            window.open(whatsappURL, '_blank');
        }
        
        lokasiInput.value = '';
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>

<!-- Additional CSS -->
<style>
/* Custom map icon */
.custom-div-icon {
    background: transparent !important;
    border: none !important;
}

/* Area cards styling */
.area-card {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    height: 100%;
}

.area-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.area-card.main {
    border-color: var(--accent-dark);
    background-color: rgba(212, 184, 163, 0.1);
}

.area-icon {
    font-size: 2rem;
    color: var(--accent-dark);
    margin-bottom: 15px;
}

.area-card.main .area-icon {
    color: var(--accent-dark);
}

.area-card:not(.main) .area-icon {
    color: #6c757d;
}

/* Map container */
#map {
    border-radius: 8px;
    z-index: 1;
}

/* Location form */
#checkLocationForm input:focus {
    border-color: var(--accent-dark);
    box-shadow: 0 0 0 0.25rem rgba(212, 184, 163, 0.25);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .area-card {
        margin-bottom: 15px;
    }
    
    #checkLocationForm {
        flex-direction: column;
    }
    
    #checkLocationForm input {
        margin-bottom: 10px;
        margin-right: 0 !important;
    }
}
</style>

<?= $this->include('template/footer') ?>