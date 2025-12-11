<?php
// File: app/Views/admin/kostum/tambah.php
?>

<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/kostum') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tambah Kostum Baru</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($validation)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($validation->getErrors() as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('admin/kostum/simpan') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nama_kostum" class="form-label required">
                                        Nama Kostum
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('nama_kostum')) ? 'is-invalid' : '' ?>" 
                                           id="nama_kostum" 
                                           name="nama_kostum" 
                                           value="<?= old('nama_kostum') ?>" 
                                           required>
                                    <?php if (isset($validation) && $validation->hasError('nama_kostum')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('nama_kostum') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" 
                                              id="deskripsi" 
                                              name="deskripsi" 
                                              rows="5"><?= old('deskripsi') ?></textarea>
                                    <small class="text-muted">Deskripsikan detail kostum, bahan, ukuran, dll.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar Kostum</label>
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control <?= (isset($validation) && $validation->hasError('gambar')) ? 'is-invalid' : '' ?>" 
                                               id="gambar" 
                                               name="gambar" 
                                               accept="image/*">
                                    </div>
                                    <small class="text-muted">
                                        Ukuran maksimal 2MB. Format: JPG, PNG, WebP
                                    </small>
                                    <?php if (isset($validation) && $validation->hasError('gambar')): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= $validation->getError('gambar') ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div id="imagePreview" class="mt-2 text-center"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label required">
                                        Kategori
                                    </label>
                                    <select class="form-select <?= (isset($validation) && $validation->hasError('kategori')) ? 'is-invalid' : '' ?>" 
                                            id="kategori" 
                                            name="kategori" 
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($kategori_options as $value => $label): ?>
                                            <option value="<?= $value ?>" 
                                                <?= old('kategori') == $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($validation) && $validation->hasError('kategori')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('kategori') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="durasi_sewa" class="form-label">Durasi Sewa</label>
                                    <select class="form-select" id="durasi_sewa" name="durasi_sewa">
                                        <option value="24 Jam" <?= old('durasi_sewa') == '24 Jam' ? 'selected' : '' ?>>24 Jam</option>
                                        <option value="12 Jam" <?= old('durasi_sewa') == '12 Jam' ? 'selected' : '' ?>>12 Jam</option>
                                        <option value="1 Minggu" <?= old('durasi_sewa') == '1 Minggu' ? 'selected' : '' ?>>1 Minggu</option>
                                        <option value="2 Minggu" <?= old('durasi_sewa') == '2 Minggu' ? 'selected' : '' ?>>2 Minggu</option>
                                        <option value="1 Bulan" <?= old('durasi_sewa') == '1 Bulan' ? 'selected' : '' ?>>1 Bulan</option>
                                        <option value="Kustom">Kustom (isi manual)</option>
                                    </select>
                                    
                                    <!-- Custom duration input (hidden by default) -->
                                    <input type="text" 
                                           class="form-control mt-2 d-none" 
                                           id="durasi_kustom" 
                                           name="durasi_kustom" 
                                           placeholder="Misal: 3 Hari, 48 Jam, dll.">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="harga_sewa" class="form-label required">
                                        Harga Sewa
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control <?= (isset($validation) && $validation->hasError('harga_sewa')) ? 'is-invalid' : '' ?>" 
                                               id="harga_sewa" 
                                               name="harga_sewa" 
                                               value="<?= old('harga_sewa') ?>" 
                                               required 
                                               min="0" 
                                               step="1000">
                                    </div>
                                    <?php if (isset($validation) && $validation->hasError('harga_sewa')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('harga_sewa') ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted">Harga dalam Rupiah per durasi sewa</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stok" class="form-label required">
                                        Stok Total
                                    </label>
                                    <input type="number" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('stok')) ? 'is-invalid' : '' ?>" 
                                           id="stok" 
                                           name="stok" 
                                           value="<?= old('stok', 1) ?>" 
                                           required 
                                           min="1">
                                    <?php if (isset($validation) && $validation->hasError('stok')): ?>
                                        <div class="invalid-feedback">
                                            <?= $validation->getError('stok') ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted">Jumlah total kostum yang tersedia</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="stok_tersedia" class="form-label">Stok Tersedia</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="stok_tersedia" 
                                           name="stok_tersedia" 
                                           value="<?= old('stok_tersedia', old('stok', 1)) ?>" 
                                           min="0" 
                                           max="<?= old('stok', 1) ?>">
                                    <small class="text-muted">
                                        Sisa kostum yang bisa disewa
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   value="1" 
                                                   <?= old('is_active', 1) ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold" for="is_active">
                                                <i class="bi bi-power me-1"></i> Status Aktif
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            Kostum tidak aktif tidak akan tampil di halaman depan
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_featured" 
                                                   name="is_featured" 
                                                   value="1" 
                                                   <?= old('is_featured') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold" for="is_featured">
                                                <i class="bi bi-star me-1"></i> Featured
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            Kostum featured akan ditampilkan di bagian khusus
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Isi semua field bertanda * (required)
                            </div>
                            
                            <div>
                                <button type="reset" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Kostum
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set max stok_tersedia based on stok
document.getElementById('stok').addEventListener('input', function() {
    const stok = parseInt(this.value) || 0;
    const stokTersediaInput = document.getElementById('stok_tersedia');
    stokTersediaInput.max = stok;
    
    if (parseInt(stokTersediaInput.value) > stok) {
        stokTersediaInput.value = stok;
    }
});

// Custom duration handler
const durasiSelect = document.getElementById('durasi_sewa');
const durasiKustomInput = document.getElementById('durasi_kustom');

durasiSelect.addEventListener('change', function() {
    if (this.value === 'Kustom') {
        durasiKustomInput.classList.remove('d-none');
        durasiKustomInput.setAttribute('name', 'durasi_sewa');
        durasiKustomInput.value = '';
        durasiKustomInput.focus();
    } else {
        durasiKustomInput.classList.add('d-none');
        durasiKustomInput.removeAttribute('name');
    }
});

// Initialize if custom duration is selected from old input
if ('<?= old("durasi_sewa") ?>' === 'Kustom') {
    durasiSelect.value = 'Kustom';
    durasiKustomInput.classList.remove('d-none');
    durasiKustomInput.setAttribute('name', 'durasi_sewa');
    durasiKustomInput.value = '<?= old("durasi_kustom") ?>';
}

// Preview image before upload
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.getElementById('imagePreview');
            previewDiv.innerHTML = `
                <label class="form-label">Preview Gambar:</label>
                <img src="${e.target.result}" 
                     class="img-thumbnail" 
                     style="max-height: 150px; max-width: 100%;">
            `;
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('imagePreview').innerHTML = '';
    }
});

// Form validation
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    let valid = true;
    
    // Check required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    // Check file size if image is uploaded
    const gambarInput = document.getElementById('gambar');
    if (gambarInput.files[0]) {
        const fileSize = gambarInput.files[0].size / 1024 / 1024; // in MB
        if (fileSize > 2) {
            alert('Ukuran gambar maksimal 2MB');
            valid = false;
        }
    }
    
    if (!valid) {
        e.preventDefault();
        alert('Harap isi semua field yang wajib diisi');
    }
});
const gambarInput = document.getElementById('gambar');
if (gambarInput) {
    gambarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Ukuran maksimal 2MB
            if (file.size > 2097152) {
                alert('Ukuran gambar maksimal 2MB');
                this.value = '';
                return;
            }
            
            // Tipe file
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format gambar tidak didukung. Gunakan JPG, PNG, atau WebP');
                this.value = '';
                return;
            }
        }
    });
}
</script>

<style>
/* Custom styles for tambah form */
.card-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
}

.form-label.required:after {
    content: " *";
    color: #dc3545;
}

.invalid-feedback {
    display: block !important;
}

/* Switch styling */
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-check-input:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>
<?= $this->endSection() ?>