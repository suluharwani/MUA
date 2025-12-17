<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="<?= base_url('admin/gallery') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

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

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Form Tambah Gallery</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/gallery/simpan') ?>" method="POST" enctype="multipart/form-data" id="galleryForm">
                        <?= csrf_field() ?>
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="judul" class="form-label">Judul Gallery <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= (session('errors.judul') || (isset($validation) && $validation->hasError('judul'))) ? 'is-invalid' : '' ?>" 
                                       id="judul" name="judul" 
                                       value="<?= old('judul', session()->getFlashdata('judul') ?? '') ?>" 
                                       required maxlength="200">
                                <div class="invalid-feedback">
                                    <?= session('errors.judul') ?? (isset($validation) ? $validation->getError('judul') : '') ?>
                                </div>
                                <small class="text-muted">Judul yang menarik untuk gallery (max 200 karakter)</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="urutan" class="form-label">Urutan Tampil</label>
                                <input type="number" class="form-control" id="urutan" name="urutan" 
                                       value="<?= old('urutan', session()->getFlashdata('urutan') ?? 0) ?>" 
                                       min="0" max="999">
                                <small class="text-muted">Angka lebih kecil akan ditampilkan lebih awal (0-999)</small>
                            </div>
                        </div>
                        
                        <!-- Category and Style -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select <?= (session('errors.kategori') || (isset($validation) && $validation->hasError('kategori'))) ? 'is-invalid' : '' ?>" 
                                        id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategori_options as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('kategori', session()->getFlashdata('kategori') ?? '') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('errors.kategori') ?? (isset($validation) ? $validation->getError('kategori') : '') ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="style" class="form-label">Style Makeup</label>
                                <select class="form-select" id="style" name="style">
                                    <option value="">Pilih Style (opsional)</option>
                                    <?php foreach ($style_options as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= old('style', session()->getFlashdata('style') ?? '') == $value ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Hanya untuk kategori makeup</small>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control <?= (session('errors.deskripsi') || (isset($validation) && $validation->hasError('deskripsi'))) ? 'is-invalid' : '' ?>" 
                                      id="deskripsi" name="deskripsi" rows="4"><?= old('deskripsi', session()->getFlashdata('deskripsi') ?? '') ?></textarea>
                            <div class="invalid-feedback">
                                <?= session('errors.deskripsi') ?? (isset($validation) ? $validation->getError('deskripsi') : '') ?>
                            </div>
                            <small class="text-muted">Jelaskan detail gallery ini (minimal 10 karakter)</small>
                        </div>
                        
                        <!-- Main Image Upload -->
                        <div class="mb-4">
                            <label for="gambar" class="form-label">Gambar Utama <span class="text-danger">*</span></label>
                            <input type="file" class="form-control <?= (session('errors.gambar') || (isset($validation) && $validation->hasError('gambar'))) ? 'is-invalid' : '' ?>" 
                                   id="gambar" name="gambar" accept="image/*" required>
                            <div class="invalid-feedback">
                                <?= session('errors.gambar') ?? (isset($validation) ? $validation->getError('gambar') : '') ?>
                            </div>
                            <small class="text-muted">Ukuran maksimal 5MB, format: JPG, PNG, WebP</small>
                            <div class="mt-2">
                                <img id="gambarPreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px; display: none;">
                            </div>
                        </div>
                        
                        <!-- Additional Images Upload -->
                        <div class="mb-4">
                            <label for="gambar_tambahan" class="form-label">Gambar Tambahan</label>
                            <input type="file" class="form-control" id="gambar_tambahan" name="gambar_tambahan[]" accept="image/*" multiple>
                            <small class="text-muted">Dapat memilih multiple file (max 10 file, masing-masing max 5MB)</small>
                            
                            <div id="additionalImagesPreview" class="row g-2 mt-2"></div>
                        </div>
                        
                        <!-- Gallery Details -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Detail Gallery (Opsional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tema_warna" class="form-label">Tema Warna</label>
                                        <input type="text" class="form-control" id="tema_warna" name="tema_warna" 
                                               value="<?= old('tema_warna', session()->getFlashdata('tema_warna') ?? '') ?>">
                                        <small class="text-muted">Contoh: Pastel Pink, Gold, Navy Blue</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="produk_digunakan" class="form-label">Produk Digunakan</label>
                                        <input type="text" class="form-control" id="produk_digunakan" name="produk_digunakan" 
                                               value="<?= old('produk_digunakan', session()->getFlashdata('produk_digunakan') ?? '') ?>">
                                        <small class="text-muted">Contoh: MAC, Maybelline, L'Oreal</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="lokasi_pemotretan" class="form-label">Lokasi Pemotretan</label>
                                        <input type="text" class="form-control" id="lokasi_pemotretan" name="lokasi_pemotretan" 
                                               value="<?= old('lokasi_pemotretan', session()->getFlashdata('lokasi_pemotretan') ?? '') ?>">
                                        <small class="text-muted">Contoh: Studio Jakarta, Outdoor Bandung</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="makeup_artist" class="form-label">Makeup Artist</label>
                                        <input type="text" class="form-control" id="makeup_artist" name="makeup_artist" 
                                               value="<?= old('makeup_artist', session()->getFlashdata('makeup_artist') ?? '') ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="model" class="form-label">Model</label>
                                        <input type="text" class="form-control" id="model" name="model" 
                                               value="<?= old('model', session()->getFlashdata('model') ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO & Settings -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords (SEO)</label>
                                <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="2"><?= old('meta_keywords', session()->getFlashdata('meta_keywords') ?? '') ?></textarea>
                                <small class="text-muted">Pisahkan dengan koma</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="meta_description" class="form-label">Meta Description (SEO)</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?= old('meta_description', session()->getFlashdata('meta_description') ?? '') ?></textarea>
                                <small class="text-muted">Maksimal 160 karakter</small>
                            </div>
                        </div>
                        
                        <!-- Status Options -->
                        <div class="row mb-4">
                            <div class="col-md-6">
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
                        
                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="reset" class="btn btn-secondary" id="resetBtn">
                                <i class="bi bi-x-circle"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Simpan Gallery
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Preview Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Preview</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Judul:</label>
                        <div id="previewJudul" class="fw-bold text-primary">-</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kategori:</label>
                        <div id="previewKategori" class="text-muted">-</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Style:</label>
                        <div id="previewStyle" class="text-muted">-</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gambar Utama:</label>
                        <div id="previewGambar" class="text-center bg-light p-3 rounded">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            <div class="mt-2">No image selected</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gambar Tambahan:</label>
                        <div id="previewGambarTambahan" class="text-muted">
                            No additional images
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status:</label>
                        <div id="previewStatus" class="d-flex gap-2">
                            <span id="statusAktif" class="badge bg-success d-none">Aktif</span>
                            <span id="statusFeatured" class="badge bg-warning d-none">Featured</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Tips:</strong>
                        <ul class="mb-0 mt-1">
                            <li>Judul harus menarik dan deskriptif</li>
                            <li>Gambar utama kualitas tinggi</li>
                            <li>Pilih kategori yang tepat</li>
                            <li>Isi deskripsi dengan detail</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Category Management Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Kelola Kategori</h5>
                </div>
                <div class="card-body">
                    <div id="categoryManagement">
                        <!-- Categories will be loaded here via AJAX -->
                        <div class="text-center">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0">Memuat kategori...</p>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <!-- Add New Category Form -->
                    <form id="addCategoryForm" class="mt-3">
                        <?= csrf_field() ?>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" 
                                   id="newCategoryName" 
                                   placeholder="Nama kategori baru" 
                                   maxlength="50" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Tambah
                            </button>
                        </div>
                        <small class="text-muted">Enter untuk submit</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const form = document.getElementById('galleryForm');
    const judulInput = document.getElementById('judul');
    const kategoriSelect = document.getElementById('kategori');
    const styleSelect = document.getElementById('style');
    const deskripsiInput = document.getElementById('deskripsi');
    const gambarInput = document.getElementById('gambar');
    const gambarTambahanInput = document.getElementById('gambar_tambahan');
    const isActiveCheckbox = document.getElementById('is_active');
    const isFeaturedCheckbox = document.getElementById('is_featured');
    const urutanInput = document.getElementById('urutan');
    const temaWarnaInput = document.getElementById('tema_warna');
    const produkDigunakanInput = document.getElementById('produk_digunakan');
    
    // Preview elements
    const previewJudul = document.getElementById('previewJudul');
    const previewKategori = document.getElementById('previewKategori');
    const previewStyle = document.getElementById('previewStyle');
    const previewGambar = document.getElementById('previewGambar');
    const previewGambarTambahan = document.getElementById('previewGambarTambahan');
    const statusAktif = document.getElementById('statusAktif');
    const statusFeatured = document.getElementById('statusFeatured');
    
    // Update preview function
    function updatePreview() {
        // Judul
        previewJudul.textContent = judulInput.value || '-';
        
        // Kategori
        const kategoriText = kategoriSelect.options[kategoriSelect.selectedIndex]?.text || '-';
        previewKategori.textContent = kategoriText;
        
        // Style
        const styleText = styleSelect.options[styleSelect.selectedIndex]?.text || '-';
        previewStyle.textContent = styleText;
        
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
    }
    
    // Preview main image
    if (gambarInput) {
        gambarInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewGambar.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="Preview" 
                             class="img-fluid rounded" 
                             style="max-height: 150px; object-fit: cover;">
                        <div class="mt-2 small text-muted">${file.name}</div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Preview additional images
    if (gambarTambahanInput) {
        gambarTambahanInput.addEventListener('change', function(e) {
            const files = Array.from(this.files);
            if (files.length > 0) {
                let previewHTML = '<div class="row g-2">';
                let fileCount = 0;
                
                files.forEach((file, index) => {
                    if (index >= 3) return; // Show only first 3 images
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById(`previewImg${index}`).src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    
                    previewHTML += `
                        <div class="col-4">
                            <img id="previewImg${index}" 
                                 alt="Preview ${index + 1}" 
                                 class="img-fluid rounded" 
                                 style="height: 80px; object-fit: cover;">
                            <div class="small text-muted mt-1">${file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name}</div>
                        </div>
                    `;
                    fileCount++;
                });
                
                previewHTML += '</div>';
                if (files.length > 3) {
                    previewHTML += `<div class="mt-2 small text-info">+${files.length - 3} gambar lainnya</div>`;
                }
                
                previewGambarTambahan.innerHTML = previewHTML;
            } else {
                previewGambarTambahan.innerHTML = '<div class="text-muted">No additional images</div>';
            }
        });
    }
    
    // Attach event listeners
    judulInput.addEventListener('input', updatePreview);
    kategoriSelect.addEventListener('change', updatePreview);
    styleSelect.addEventListener('change', updatePreview);
    isActiveCheckbox.addEventListener('change', updatePreview);
    isFeaturedCheckbox.addEventListener('change', updatePreview);
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validation
        let isValid = true;
        const errors = [];
        
        // Judul validation
        if (!judulInput.value.trim()) {
            errors.push('Judul gallery harus diisi');
            judulInput.classList.add('is-invalid');
            isValid = false;
        } else if (judulInput.value.trim().length < 3) {
            errors.push('Judul minimal 3 karakter');
            judulInput.classList.add('is-invalid');
            isValid = false;
        } else {
            judulInput.classList.remove('is-invalid');
        }
        
        // Kategori validation
        if (!kategoriSelect.value) {
            errors.push('Kategori harus dipilih');
            kategoriSelect.classList.add('is-invalid');
            isValid = false;
        } else {
            kategoriSelect.classList.remove('is-invalid');
        }
        
        // Deskripsi validation
        if (deskripsiInput.value.trim() && deskripsiInput.value.trim().length < 10) {
            errors.push('Deskripsi minimal 10 karakter');
            deskripsiInput.classList.add('is-invalid');
            isValid = false;
        } else {
            deskripsiInput.classList.remove('is-invalid');
        }
        
        // Gambar validation
        if (!gambarInput.files || gambarInput.files.length === 0) {
            errors.push('Gambar utama wajib diupload');
            gambarInput.classList.add('is-invalid');
            isValid = false;
        } else {
            const file = gambarInput.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!validTypes.includes(file.type)) {
                errors.push('Format gambar tidak valid. Gunakan JPG, PNG, atau WebP');
                gambarInput.classList.add('is-invalid');
                isValid = false;
            } else if (file.size > maxSize) {
                errors.push('Ukuran gambar terlalu besar. Maksimal 5MB');
                gambarInput.classList.add('is-invalid');
                isValid = false;
            } else {
                gambarInput.classList.remove('is-invalid');
            }
        }
        
        // Check additional images
        if (gambarTambahanInput.files.length > 10) {
            errors.push('Maksimal 10 gambar tambahan');
            gambarTambahanInput.classList.add('is-invalid');
            isValid = false;
        } else {
            gambarTambahanInput.classList.remove('is-invalid');
        }
        
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
            previewGambar.innerHTML = `
                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                <div class="mt-2">No image selected</div>
            `;
            previewGambarTambahan.innerHTML = '<div class="text-muted">No additional images</div>';
            updatePreview();
        }
    });
    
    // Initial preview update
    updatePreview();
    
    // Category Management Functions
    loadCategories();
    
    // Add category form submission
    document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addNewCategory();
    });
});

// Load categories via AJAX
function loadCategories() {
    fetch('<?= base_url("admin/gallery/categories") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCategories(data.categories);
            } else {
                document.getElementById('categoryManagement').innerHTML = 
                    '<div class="alert alert-danger">Gagal memuat kategori</div>';
            }
        })
        .catch(error => {
            document.getElementById('categoryManagement').innerHTML = 
                '<div class="alert alert-danger">Error: ' + error.message + '</div>';
        });
}

// Render categories list
function renderCategories(categories) {
    if (!categories || categories.length === 0) {
        document.getElementById('categoryManagement').innerHTML = 
            '<div class="alert alert-info">Belum ada kategori. Tambahkan kategori baru.</div>';
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    
    categories.forEach((category, index) => {
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                <div>
                    <span class="badge bg-light text-dark">${category.total || 0}</span>
                    <span class="ms-2">${category.label}</span>
                    <small class="d-block text-muted">${category.value}</small>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-warning btn-sm" onclick="editCategory('${category.value}', '${category.label}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteCategory('${category.value}')" ${category.total > 0 ? 'disabled' : ''}>
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    document.getElementById('categoryManagement').innerHTML = html;
}

// Add new category
function addNewCategory() {
    const nameInput = document.getElementById('newCategoryName');
    const categoryName = nameInput.value.trim();
    
    if (!categoryName) {
        alert('Nama kategori tidak boleh kosong');
        return;
    }
    
    // Validate category name
    if (categoryName.length < 2) {
        alert('Nama kategori minimal 2 karakter');
        return;
    }
    
    if (categoryName.length > 50) {
        alert('Nama kategori maksimal 50 karakter');
        return;
    }
    
    // Create slug from name
    const categorySlug = categoryName.toLowerCase()
        .replace(/[^\w\s]/gi, '')
        .replace(/\s+/g, '-');
    
    // Check if category already exists
    fetch('<?= base_url("admin/gallery/check-category") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="<?= csrf_token() ?>"]').value
        },
        body: JSON.stringify({ 
            slug: categorySlug,
            name: categoryName 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            alert('Kategori sudah ada!');
            return;
        }
        
        // Add to database
        fetch('<?= base_url("admin/gallery/add-category") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="<?= csrf_token() ?>"]').value
            },
            body: JSON.stringify({ 
                value: categorySlug,
                label: categoryName 
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Kategori berhasil ditambahkan!');
                nameInput.value = '';
                loadCategories();
                
                // Add to select dropdown
                const kategoriSelect = document.getElementById('kategori');
                const option = document.createElement('option');
                option.value = categorySlug;
                option.textContent = categoryName;
                kategoriSelect.appendChild(option);
            } else {
                alert('Gagal menambahkan kategori: ' + (result.message || 'Unknown error'));
            }
        });
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Edit category (placeholder - implement as needed)
function editCategory(slug, currentName) {
    const newName = prompt('Edit nama kategori:', currentName);
    if (newName && newName !== currentName && newName.trim().length >= 2) {
        // Update via AJAX
        fetch('<?= base_url("admin/gallery/edit-category") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="<?= csrf_token() ?>"]').value
            },
            body: JSON.stringify({ 
                slug: slug,
                name: newName.trim() 
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Kategori berhasil diupdate!');
                loadCategories();
                
                // Update select dropdown
                const kategoriSelect = document.getElementById('kategori');
                const options = kategoriSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === slug) {
                        options[i].textContent = newName.trim();
                        break;
                    }
                }
            } else {
                alert('Gagal mengupdate kategori: ' + (result.message || 'Unknown error'));
            }
        });
    }
}

// Delete category
function deleteCategory(slug) {
    if (!confirm('Hapus kategori ini? Semua gallery dengan kategori ini akan menjadi tanpa kategori.')) {
        return;
    }
    
    fetch('<?= base_url("admin/gallery/delete-category") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="<?= csrf_token() ?>"]').value
        },
        body: JSON.stringify({ slug: slug })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Kategori berhasil dihapus!');
            loadCategories();
            
            // Remove from select dropdown
            const kategoriSelect = document.getElementById('kategori');
            const options = kategoriSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === slug) {
                    kategoriSelect.remove(i);
                    break;
                }
            }
        } else {
            alert('Gagal menghapus kategori: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Auto-fill example data for testing
function fillExampleData(type) {
    const examples = {
        'makeup': {
            judul: 'Makeup Natural untuk Pernikahan',
            kategori: 'makeup',
            style: 'natural',
            deskripsi: 'Makeup natural dengan sentuhan soft glam untuk pengantin. Fokus pada kulit flawless dan highlight natural.',
            tema_warna: 'Soft Peach, Rose Gold',
            produk_digunakan: 'MAC Foundation, NARS Blush, Urban Decay Eyeshadow',
            makeup_artist: 'Sari Dewi',
            model: 'Maya Sari'
        },
        'kostum': {
            judul: 'Kostum Pengantin Jawa Tradisional',
            kategori: 'kostum',
            style: '',
            deskripsi: 'Kostum pengantin adat Jawa lengkap dengan kebaya brokat dan kain batik.',
            tema_warna: 'Merah, Emas',
            produk_digunakan: 'Kain Brokat, Payet, Manik-manik',
            lokasi_pemotretan: 'Studio Jakarta'
        }
    };
    
    const example = examples[type];
    if (!example) return;
    
    // Fill form
    document.getElementById('judul').value = example.judul;
    document.getElementById('deskripsi').value = example.deskripsi;
    document.getElementById('tema_warna').value = example.tema_warna;
    document.getElementById('produk_digunakan').value = example.produk_digunakan;
    document.getElementById('makeup_artist').value = example.makeup_artist || '';
    document.getElementById('model').value = example.model || '';
    document.getElementById('lokasi_pemotretan').value = example.lokasi_pemotretan || '';
    
    // Set kategori
    const kategoriSelect = document.getElementById('kategori');
    for (let i = 0; i < kategoriSelect.options.length; i++) {
        if (kategoriSelect.options[i].value === example.kategori) {
            kategoriSelect.selectedIndex = i;
            break;
        }
    }
    
    // Set style if exists
    if (example.style) {
        const styleSelect = document.getElementById('style');
        for (let i = 0; i < styleSelect.options.length; i++) {
            if (styleSelect.options[i].value === example.style) {
                styleSelect.selectedIndex = i;
                break;
            }
        }
    }
    
    // Update preview
    updatePreview();
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

#previewGambar img {
    transition: transform 0.3s ease;
}

#previewGambar img:hover {
    transform: scale(1.05);
}

/* Category management styles */
.list-group-item:hover {
    background-color: #f8f9fa;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-group {
        flex-wrap: wrap;
    }
}
</style>

<?= $this->endSection() ?>