<?php
// File: app/Views/admin/kostum/edit.php
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
                    <h5 class="mb-0">Edit Data Kostum</h5>
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
                    
                    <form method="post" action="<?= base_url('admin/kostum/update/' . $kostum['id']) ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <input type="hidden" name="_method" value="POST">
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nama_kostum" class="form-label">
                                        Nama Kostum <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('nama_kostum')) ? 'is-invalid' : '' ?>" 
                                           id="nama_kostum" 
                                           name="nama_kostum" 
                                           value="<?= old('nama_kostum', esc($kostum['nama_kostum'])) ?>" 
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
                                              rows="5"><?= old('deskripsi', esc($kostum['deskripsi'])) ?></textarea>
                                    <small class="text-muted">Deskripsikan detail kostum, bahan, ukuran, dll.</small>
                                </div>
                            <div class="mb-3">
    <label for="spesifikasi" class="form-label">Spesifikasi</label>
    <textarea class="form-control" 
              id="spesifikasi" 
              name="spesifikasi" 
              rows="4"
              placeholder="Masukkan spesifikasi, satu per baris"><?= old('spesifikasi', $kostum['spesifikasi_text'] ?? '') ?></textarea>
    <small class="text-muted">Masukkan spesifikasi, satu per baris</small>
</div>

<!-- Tambahkan kondisi -->
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="kondisi" class="form-label">Kondisi</label>
            <select class="form-select" id="kondisi" name="kondisi">
                <?php foreach ($kondisi_options as $value => $label): ?>
                    <option value="<?= $value ?>" 
                        <?= old('kondisi', $kostum['kondisi'] ?? 'baik') == $value ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>

<!-- Tambahkan di bagian additional fields -->
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="ukuran" class="form-label">Ukuran</label>
            <input type="text" 
                   class="form-control" 
                   id="ukuran" 
                   name="ukuran" 
                   value="<?= old('ukuran', $kostum['ukuran'] ?? '') ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="warna" class="form-label">Warna</label>
            <input type="text" 
                   class="form-control" 
                   id="warna" 
                   name="warna" 
                   value="<?= old('warna', $kostum['warna'] ?? '') ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="bahan" class="form-label">Bahan</label>
            <input type="text" 
                   class="form-control" 
                   id="bahan" 
                   name="bahan" 
                   value="<?= old('bahan', $kostum['bahan'] ?? '') ?>">
        </div>
    </div>
</div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar Kostum</label>
                                    
                                    <!-- Preview Current Image -->
                                    <?php if (!empty($kostum['gambar'])): ?>
                                        <div class="mb-3 text-center">
                                            <img src="<?= base_url('uploads/kostum/' . $kostum['gambar']) ?>" 
                                                 alt="<?= esc($kostum['nama_kostum']) ?>" 
                                                 class="img-thumbnail mb-2" 
                                                 style="max-height: 150px; max-width: 100%;">
                                            <div class="form-text">
                                                Gambar saat ini
                                                <a href="<?= base_url('uploads/kostum/' . $kostum['gambar']) ?>" 
                                                   target="_blank" 
                                                   class="ms-2">
                                                    <i class="bi bi-box-arrow-up-right"></i> Lihat
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-3">
                                            <i class="bi bi-info-circle"></i> Belum ada gambar
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Upload New Image -->
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control <?= (isset($validation) && $validation->hasError('gambar')) ? 'is-invalid' : '' ?>" 
                                               id="gambar" 
                                               name="gambar" 
                                               accept="image/*">
                                    </div>
                                    <small class="text-muted">
                                        Kosongkan jika tidak ingin mengubah gambar. 
                                        Ukuran maksimal 2MB. Format: JPG, PNG, WebP
                                    </small>
                                    <?php if (isset($validation) && $validation->hasError('gambar')): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= $validation->getError('gambar') ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Option to delete existing image -->
                                    <?php if (!empty($kostum['gambar'])): ?>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="hapus_gambar" 
                                                   name="hapus_gambar" 
                                                   value="1">
                                            <label class="form-check-label text-danger" for="hapus_gambar">
                                                <i class="bi bi-trash"></i> Hapus gambar saat ini
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kategori" class="form-label">
                                        Kategori <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?= (isset($validation) && $validation->hasError('kategori')) ? 'is-invalid' : '' ?>" 
                                            id="kategori" 
                                            name="kategori" 
                                            required>
                                        <option value="">Pilih Kategori</option>
                                        <?php foreach ($kategori_options as $value => $label): ?>
                                            <option value="<?= $value ?>" 
                                                <?= old('kategori', $kostum['kategori']) == $value ? 'selected' : '' ?>>
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
                                        <option value="24 Jam" <?= old('durasi_sewa', $kostum['durasi_sewa']) == '24 Jam' ? 'selected' : '' ?>>24 Jam</option>
                                        <option value="12 Jam" <?= old('durasi_sewa', $kostum['durasi_sewa']) == '12 Jam' ? 'selected' : '' ?>>12 Jam</option>
                                        <option value="1 Minggu" <?= old('durasi_sewa', $kostum['durasi_sewa']) == '1 Minggu' ? 'selected' : '' ?>>1 Minggu</option>
                                        <option value="2 Minggu" <?= old('durasi_sewa', $kostum['durasi_sewa']) == '2 Minggu' ? 'selected' : '' ?>>2 Minggu</option>
                                        <option value="1 Bulan" <?= old('durasi_sewa', $kostum['durasi_sewa']) == '1 Bulan' ? 'selected' : '' ?>>1 Bulan</option>
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
                                    <label for="harga_sewa" class="form-label">
                                        Harga Sewa <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" 
                                               class="form-control <?= (isset($validation) && $validation->hasError('harga_sewa')) ? 'is-invalid' : '' ?>" 
                                               id="harga_sewa" 
                                               name="harga_sewa" 
                                               value="<?= old('harga_sewa', $kostum['harga_sewa']) ?>" 
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
                                    <label for="stok" class="form-label">
                                        Stok Total <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control <?= (isset($validation) && $validation->hasError('stok')) ? 'is-invalid' : '' ?>" 
                                           id="stok" 
                                           name="stok" 
                                           value="<?= old('stok', $kostum['stok']) ?>" 
                                           required 
                                           min="0">
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
                                           value="<?= old('stok_tersedia', $kostum['stok_tersedia']) ?>" 
                                           min="0" 
                                           max="<?= $kostum['stok'] ?>">
                                    <small class="text-muted">
                                        Sisa kostum yang bisa disewa. 
                                        Maksimal <?= $kostum['stok'] ?> (stok total)
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
                                                   <?= old('is_active', $kostum['is_active']) ? 'checked' : '' ?>>
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
                                                   <?= old('is_featured', $kostum['is_featured']) ? 'checked' : '' ?>>
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
                        
                        <!-- Additional Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Informasi Tambahan</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Dibuat</th>
                                                <td><?= date('d/m/Y H:i', strtotime($kostum['created_at'])) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Diperbarui</th>
                                                <td><?= date('d/m/Y H:i', strtotime($kostum['updated_at'])) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">ID Kostum</th>
                                                <td><code>#<?= $kostum['id'] ?></code></td>
                                            </tr>
                                            <tr>
                                                <th>Status Saat Ini</th>
                                                <td>
                                                    <?php if ($kostum['is_active']): ?>
                                                        <span class="badge bg-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Nonaktif</span>
                                                    <?php endif; ?>
                                                    <?php if ($kostum['is_featured']): ?>
                                                        <span class="badge bg-warning ms-1">Featured</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="<?= base_url('admin/kostum/view/' . $kostum['id']) ?>" 
                                   class="btn btn-outline-info">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                                <a href="<?= base_url('admin/kostum/toggle-status/' . $kostum['id']) ?>" 
                                   class="btn btn-outline-<?= $kostum['is_active'] ? 'danger' : 'success' ?>"
                                   onclick="return confirm('Ubah status aktif?')">
                                    <i class="bi bi-power"></i> 
                                    <?= $kostum['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                </a>
                            </div>
                            
                            <div>
                                <a href="<?= base_url('admin/kostum') ?>" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Simpan Perubahan
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

// Initialize custom duration if current value is not in options
const currentDurasi = '<?= $kostum['durasi_sewa'] ?>';
const durasiOptions = ['24 Jam', '12 Jam', '1 Minggu', '2 Minggu', '1 Bulan'];

if (!durasiOptions.includes(currentDurasi) && currentDurasi) {
    durasiSelect.value = 'Kustom';
    durasiKustomInput.classList.remove('d-none');
    durasiKustomInput.setAttribute('name', 'durasi_sewa');
    durasiKustomInput.value = currentDurasi;
}

// Preview image before upload
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Remove existing preview if exists
            const existingPreview = document.querySelector('.image-preview');
            if (existingPreview) {
                existingPreview.remove();
            }
            
            // Create new preview
            const previewDiv = document.createElement('div');
            previewDiv.className = 'mt-3 text-center image-preview';
            previewDiv.innerHTML = `
                <label class="form-label">Preview Gambar Baru:</label>
                <img src="${e.target.result}" 
                     class="img-thumbnail" 
                     style="max-height: 150px; max-width: 100%;">
            `;
            
            // Insert after file input
            const gambarInput = document.getElementById('gambar');
            gambarInput.parentNode.insertBefore(previewDiv, gambarInput.nextSibling);
        }
        reader.readAsDataURL(file);
    }
});

// Warn before leaving unsaved changes
let formChanged = false;
const form = document.querySelector('form');
const formInputs = form.querySelectorAll('input, select, textarea');

formInputs.forEach(input => {
    input.addEventListener('input', () => {
        formChanged = true;
    });
    input.addEventListener('change', () => {
        formChanged = true;
    });
});

window.addEventListener('beforeunload', (e) => {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Handle form submission
form.addEventListener('submit', () => {
    formChanged = false;
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
/* Custom styles for edit form */
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

.badge {
    font-size: 0.85em;
    padding: 0.35em 0.65em;
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

/* Table styling */
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}
</style>
<?= $this->endSection() ?>