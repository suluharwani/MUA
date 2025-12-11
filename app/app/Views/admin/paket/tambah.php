<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="<?= base_url('admin/paket') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <?php 
    // Debug session data
    // echo '<pre>'; print_r(session()->get()); echo '</pre>'; 
    ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan Validasi</h5>
            <ul class="mb-0">
                <?php 
                $errors = session()->getFlashdata('errors');
                if (is_array($errors)): 
                    foreach ($errors as $error): 
                        if (is_array($error)): 
                            foreach ($error as $err): ?>
                                <li><?= $err ?></li>
                            <?php endforeach; 
                        else: ?>
                            <li><?= $error ?></li>
                        <?php endif;
                    endforeach; 
                endif; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Tambah Paket</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/paket/simpan') ?>" method="POST" id="paketForm">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="nama_paket" class="form-label">Nama Paket <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= (session('errors.nama_paket') || (isset($validation) && $validation->hasError('nama_paket'))) ? 'is-invalid' : '' ?>" 
                                   id="nama_paket" name="nama_paket" 
                                   value="<?= old('nama_paket', session()->getFlashdata('nama_paket') ?? '') ?>" 
                                   required maxlength="100">
                            <div class="invalid-feedback">
                                <?= session('errors.nama_paket') ?? (isset($validation) ? $validation->getError('nama_paket') : '') ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= (session('errors.deskripsi') || (isset($validation) && $validation->hasError('deskripsi'))) ? 'is-invalid' : '' ?>" 
                                      id="deskripsi" name="deskripsi" rows="4" 
                                      required minlength="10"><?= old('deskripsi', session()->getFlashdata('deskripsi') ?? '') ?></textarea>
                            <div class="invalid-feedback">
                                <?= session('errors.deskripsi') ?? (isset($validation) ? $validation->getError('deskripsi') : '') ?>
                            </div>
                            <small class="text-muted">Minimal 10 karakter.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control <?= (session('errors.harga') || (isset($validation) && $validation->hasError('harga'))) ? 'is-invalid' : '' ?>" 
                                           id="harga" name="harga" 
                                           value="<?= old('harga', session()->getFlashdata('harga') ?? '') ?>" 
                                           required>
                                    <div class="invalid-feedback">
                                        <?= session('errors.harga') ?? (isset($validation) ? $validation->getError('harga') : '') ?>
                                    </div>
                                </div>
                                <small class="text-muted">Contoh: 1.500.000 atau 1500000</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="durasi" class="form-label">Durasi</label>
                                <input type="text" class="form-control" 
                                       id="durasi" name="durasi" 
                                       value="<?= old('durasi', session()->getFlashdata('durasi') ?? '') ?>" 
                                       placeholder="Contoh: 3 jam, 1 hari" maxlength="50">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="features" class="form-label">Fitur & Layanan</label>
                            <textarea class="form-control" id="features" name="features" rows="5" 
                                      placeholder="Masukkan setiap fitur di baris baru"><?= old('features', session()->getFlashdata('features') ?? '') ?></textarea>
                            <small class="text-muted">Satu fitur per baris.</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="urutan" class="form-label">Urutan Tampil</label>
                                <input type="number" class="form-control" id="urutan" name="urutan" 
                                       value="<?= old('urutan', session()->getFlashdata('urutan') ?? 0) ?>" 
                                       min="0" max="999">
                                <small class="text-muted">Angka lebih kecil akan ditampilkan lebih awal (0-999).</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Status</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                            <?= old('is_active', session()->getFlashdata('is_active') ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                            <?= old('is_featured', session()->getFlashdata('is_featured') ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Featured
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="reset" class="btn btn-secondary" id="resetBtn">
                                <i class="bi bi-x-circle"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Simpan Paket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Paket:</label>
                        <div id="previewNama" class="fw-bold text-primary">-</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Harga:</label>
                        <div id="previewHarga" class="fw-bold fs-5 text-success">-</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Durasi:</label>
                        <div id="previewDurasi" class="text-muted">-</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status:</label>
                        <div id="previewStatus" class="d-flex gap-2">
                            <span id="statusAktif" class="badge bg-success d-none">Aktif</span>
                            <span id="statusFeatured" class="badge bg-warning d-none">Featured</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Fitur:</label>
                        <div id="previewFeatures" class="small">
                            <div class="text-muted">Belum ada fitur</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Contoh Data</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-outline-primary mb-2" onclick="fillExample('basic')">
                        Paket Basic
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success mb-2" onclick="fillExample('premium')">
                        Paket Premium
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning mb-2" onclick="fillExample('wedding')">
                        Paket Wedding
                    </button>
                    
                    <hr>
                    
                    <ul class="mb-0 small">
                        <li><strong>Nama:</strong> Jelas dan deskriptif</li>
                        <li><strong>Harga:</strong> Format angka tanpa titik/koma</li>
                        <li><strong>Durasi:</strong> Jangka waktu layanan</li>
                        <li><strong>Fitur:</strong> Satu per baris</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const form = document.getElementById('paketForm');
    const namaInput = document.getElementById('nama_paket');
    const hargaInput = document.getElementById('harga');
    const durasiInput = document.getElementById('durasi');
    const featuresInput = document.getElementById('features');
    const isActiveCheckbox = document.getElementById('is_active');
    const isFeaturedCheckbox = document.getElementById('is_featured');
    const urutanInput = document.getElementById('urutan');
    
    // Preview elements
    const previewNama = document.getElementById('previewNama');
    const previewHarga = document.getElementById('previewHarga');
    const previewDurasi = document.getElementById('previewDurasi');
    const previewFeatures = document.getElementById('previewFeatures');
    const statusAktif = document.getElementById('statusAktif');
    const statusFeatured = document.getElementById('statusFeatured');
    
    // Format number to currency (Rp)
    function formatCurrency(number) {
        if (!number || isNaN(number)) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }
    
    // Parse currency to number
    function parseCurrency(value) {
        if (!value) return 0;
        // Remove all non-digit characters except minus
        const cleaned = value.toString().replace(/[^\d]/g, '');
        return parseInt(cleaned) || 0;
    }
    
    // Format input value on blur
    function formatHargaInput() {
        if (hargaInput.value) {
            const number = parseCurrency(hargaInput.value);
            hargaInput.value = number.toLocaleString('id-ID');
            updatePreview();
        }
    }
    
    // Clean input value on focus
    function cleanHargaInput() {
        if (hargaInput.value) {
            const number = parseCurrency(hargaInput.value);
            hargaInput.value = number;
        }
    }
    
    // Update preview function
    function updatePreview() {
        // Nama
        previewNama.textContent = namaInput.value || '-';
        
        // Harga
        const hargaNumber = parseCurrency(hargaInput.value);
        previewHarga.textContent = formatCurrency(hargaNumber);
        
        // Durasi
        previewDurasi.textContent = durasiInput.value || '-';
        
        // Status
        if (isActiveCheckbox.checked) {
            statusAktif.classList.remove('d-none');
        } else {
            statusAktif.classList.add('d-none');
        }
        
        if (isFeaturedCheckbox.checked) {
            statusFeatured.classList.remove('d-none');
        } else {
            statusFeatured.classList.add('d-none');
        }
        
        // Features
        const features = featuresInput.value.split('\n')
            .map(line => line.trim())
            .filter(line => line !== '');
            
        if (features.length > 0) {
            previewFeatures.innerHTML = features.map(feature => 
                `<div class="mb-1"><i class="bi bi-check-circle text-success me-1"></i>${feature}</div>`
            ).join('');
        } else {
            previewFeatures.innerHTML = '<div class="text-muted">Belum ada fitur</div>';
        }
    }
    
    // Attach event listeners
    namaInput.addEventListener('input', updatePreview);
    hargaInput.addEventListener('input', updatePreview);
    durasiInput.addEventListener('input', updatePreview);
    featuresInput.addEventListener('input', updatePreview);
    isActiveCheckbox.addEventListener('change', updatePreview);
    isFeaturedCheckbox.addEventListener('change', updatePreview);
    urutanInput.addEventListener('input', updatePreview);
    
    // Harga input formatting
    hargaInput.addEventListener('focus', cleanHargaInput);
    hargaInput.addEventListener('blur', formatHargaInput);
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation
        let isValid = true;
        const errors = [];
        
        // Nama validation
        if (!namaInput.value.trim()) {
            errors.push('Nama paket harus diisi');
            namaInput.classList.add('is-invalid');
            isValid = false;
        } else {
            namaInput.classList.remove('is-invalid');
        }
        
        // Harga validation
        const hargaValue = parseCurrency(hargaInput.value);
        if (!hargaValue || hargaValue < 1000) {
            errors.push('Harga minimal Rp 1.000');
            hargaInput.classList.add('is-invalid');
            isValid = false;
        } else {
            hargaInput.classList.remove('is-invalid');
        }
        
        // Deskripsi validation
        const deskripsi = document.getElementById('deskripsi').value.trim();
        if (!deskripsi || deskripsi.length < 10) {
            errors.push('Deskripsi minimal 10 karakter');
            document.getElementById('deskripsi').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('deskripsi').classList.remove('is-invalid');
        }
        
        // Clean harga value before submit
        cleanHargaInput();
        
        if (!isValid) {
            // Show errors
            alert('Perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
            return false;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        submitBtn.disabled = true;
        
        // Submit form
        this.submit();
    });
    
    // Reset form handler
    document.getElementById('resetBtn').addEventListener('click', function() {
        if (confirm('Reset semua data form?')) {
            form.reset();
            updatePreview();
        }
    });
    
    // Auto fill test data for debugging
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('test')) {
        fillExample('premium');
    }
    
    // Initial preview update
    updatePreview();
    
    // Format initial harga if exists
    if (hargaInput.value) {
        formatHargaInput();
    }
});

// Example data fillers
function fillExample(type) {
    const examples = {
        'basic': {
            nama_paket: 'Paket Makeup Basic',
            deskripsi: 'Paket makeup untuk acara casual seperti gathering, foto studio, atau pertemuan penting. Cocok untuk yang menginginkan tampilan fresh dan natural.',
            harga: '500000',
            durasi: '2 jam',
            features: 'Makeup wajah lengkap\nHairdo sederhana\nKonsultasi warna dasar\nTouch up ringan',
            urutan: 1,
            is_active: true,
            is_featured: false
        },
        'premium': {
            nama_paket: 'Paket Makeup Premium',
            deskripsi: 'Paket makeup lengkap untuk acara spesial seperti wisuda, lamaran, atau anniversary. Dengan teknik makeup yang lebih detail dan hasil yang tahan lama.',
            harga: '1200000',
            durasi: '3 jam',
            features: 'Makeup lengkap professional\nHairdo sesuai tema\nKonsultasi detail\nFree trial sebelum acara\nTouch up selama 2 jam',
            urutan: 2,
            is_active: true,
            is_featured: true
        },
        'wedding': {
            nama_paket: 'Paket Makeup Wedding',
            deskripsi: 'Paket makeup khusus pengantin dengan layanan lengkap dari persiapan hingga acara selesai. Didukung oleh makeup artist berpengalaman lebih dari 5 tahun.',
            harga: '2500000',
            durasi: '5 jam',
            features: 'Makeup pengantin lengkap\nMakeup orang tua (2 orang)\nMakeup saudara (3 orang)\nFree trial 2x sebelum hari-H\nKonsultasi tema dan warna\nTouch up sepanjang acara\nBantuan setting hijab/jilbab\nGratis produk touch up kit',
            urutan: 3,
            is_active: true,
            is_featured: true
        }
    };
    
    const example = examples[type];
    if (!example) return;
    
    // Fill the form
    document.getElementById('nama_paket').value = example.nama_paket;
    document.getElementById('deskripsi').value = example.deskripsi;
    document.getElementById('harga').value = example.harga;
    document.getElementById('durasi').value = example.durasi;
    document.getElementById('features').value = example.features;
    document.getElementById('urutan').value = example.urutan;
    document.getElementById('is_active').checked = example.is_active;
    document.getElementById('is_featured').checked = example.is_featured;
    
    // Update preview
    const event = new Event('input');
    document.getElementById('harga').dispatchEvent(event);
    document.getElementById('nama_paket').dispatchEvent(event);
    document.getElementById('durasi').dispatchEvent(event);
    document.getElementById('features').dispatchEvent(event);
}
</script>

<style>
.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(.375em + .1875rem) center;
    background-size: calc(.75em + .375rem) calc(.75em + .375rem);
}

.invalid-feedback {
    display: block;
}

#previewFeatures div {
    padding: 2px 0;
}

#previewFeatures i {
    font-size: 0.8em;
}
</style>

<?= $this->endSection() ?>