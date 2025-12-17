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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Edit Gallery</h5>
                    <div class="badge bg-info">ID: #<?= $gallery['id'] ?></div>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/gallery/update/' . $gallery['id']) ?>" method="POST" enctype="multipart/form-data" id="galleryForm">
                        <?= csrf_field() ?>
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="judul" class="form-label">Judul Gallery <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= (session('errors.judul') || (isset($validation) && $validation->hasError('judul'))) ? 'is-invalid' : '' ?>" 
                                       id="judul" name="judul" 
                                       value="<?= old('judul', $gallery['judul'] ?? '') ?>" 
                                       required maxlength="200">
                                <div class="invalid-feedback">
                                    <?= session('errors.judul') ?? (isset($validation) ? $validation->getError('judul') : '') ?>
                                </div>
                                <small class="text-muted">Judul yang menarik untuk gallery (max 200 karakter)</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="urutan" class="form-label">Urutan Tampil</label>
                                <input type="number" class="form-control" id="urutan" name="urutan" 
                                       value="<?= old('urutan', $gallery['urutan'] ?? 0) ?>" 
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
                                    <option value="<?= $value ?>" <?= old('kategori', $gallery['kategori'] ?? '') == $value ? 'selected' : '' ?>>
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
                                    <option value="<?= $value ?>" <?= old('style', $gallery['style'] ?? '') == $value ? 'selected' : '' ?>>
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
                                      id="deskripsi" name="deskripsi" rows="4"><?= old('deskripsi', $gallery['deskripsi'] ?? '') ?></textarea>
                            <div class="invalid-feedback">
                                <?= session('errors.deskripsi') ?? (isset($validation) ? $validation->getError('deskripsi') : '') ?>
                            </div>
                            <small class="text-muted">Jelaskan detail gallery ini (minimal 10 karakter)</small>
                        </div>
                        
                        <!-- Main Image Upload -->
                       <!-- Main Image Upload -->
<div class="mb-4">
    <label for="gambar" class="form-label">Gambar Utama</label>
    <div class="d-flex align-items-start gap-3">
        <div class="flex-shrink-0">
            <?php 
            $mainImagePath = ROOTPATH . 'public/uploads/gallery/' . ($gallery['gambar'] ?? '');
            if ($gallery['gambar'] && file_exists($mainImagePath)): 
            ?>
                <img src="<?= base_url('uploads/gallery/' . $gallery['gambar']) ?>" 
                     alt="Current Image" 
                     class="img-thumbnail" 
                     style="width: 150px; height: 100px; object-fit: cover;"
                     id="currentMainImage">
            <?php else: ?>
                <div class="bg-light text-center border rounded" style="width: 150px; height: 100px; line-height: 100px;">
                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                    <div class="small text-muted mt-1">No image</div>
                </div>
                <?php 
                // Clear invalid image reference
                if ($gallery['gambar'] && !file_exists($mainImagePath)) {
                    // Update database to remove invalid reference
                    $gallery['gambar'] = '';
                }
                ?>
            <?php endif; ?>
        </div>
        <div class="flex-grow-1">
            <input type="file" class="form-control <?= (session('errors.gambar') || (isset($validation) && $validation->hasError('gambar'))) ? 'is-invalid' : '' ?>" 
                   id="gambar" name="gambar" accept="image/*">
            <div class="invalid-feedback">
                <?= session('errors.gambar') ?? (isset($validation) ? $validation->getError('gambar') : '') ?>
            </div>
            <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar. Ukuran maksimal 5MB, format: JPG, PNG, WebP</small>
            <div class="mt-2">
                <img id="gambarPreview" src="" alt="Preview" class="img-fluid rounded" style="max-height: 150px; display: none;">
            </div>
        </div>
    </div>
</div>

<!-- Additional Images Upload -->
<div class="mb-4">
    <label for="gambar_tambahan" class="form-label">Gambar Tambahan</label>
    
    <!-- Current Additional Images -->
    <?php 
    $additionalImages = $gallery['gambar_tambahan'] ?? [];
    $validAdditionalImages = [];
    
    // Filter only existing images
    foreach ($additionalImages as $image) {
        $imagePath = ROOTPATH . 'public/uploads/gallery/tambahan/' . $image;
        if (file_exists($imagePath)) {
            $validAdditionalImages[] = $image;
        }
    }
    ?>
    
    <?php if (!empty($validAdditionalImages)): ?>
    <div class="mb-3">
        <label class="form-label d-block">Gambar Saat Ini:</label>
        <div class="row g-2" id="currentAdditionalImages">
            <?php foreach ($validAdditionalImages as $index => $image): ?>
            <div class="col-4 col-md-3 position-relative" data-image="<?= $image ?>">
                <img src="<?= base_url('uploads/gallery/tambahan/' . $image) ?>" 
                     alt="Additional Image <?= $index + 1 ?>" 
                     class="img-fluid rounded" 
                     style="height: 100px; object-fit: cover;"
                     onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgZmlsbD0iI2Y4ZjlmYSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTIiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIwLjNlbSIgZmlsbD0iIzZjNzU3ZCI+Tm8gaW1hZ2U8L3RleHQ+PC9zdmc+';">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                        onclick="removeAdditionalImage('<?= $image ?>')">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="deleted_images" id="deletedImages" value="">
        <small class="text-muted">Klik tombol X untuk menghapus gambar</small>
    </div>
    <?php endif; ?>
    
    <!-- Upload New Images -->
    <input type="file" class="form-control" id="gambar_tambahan" name="gambar_tambahan[]" accept="image/*" multiple>
    <small class="text-muted">Dapat memilih multiple file (max 10 file, masing-masing max 5MB)</small>
    
    <div id="newImagesPreview" class="row g-2 mt-2"></div>
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
                                               value="<?= old('tema_warna', $gallery['tema_warna'] ?? '') ?>">
                                        <small class="text-muted">Contoh: Pastel Pink, Gold, Navy Blue</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="produk_digunakan" class="form-label">Produk Digunakan</label>
                                        <input type="text" class="form-control" id="produk_digunakan" name="produk_digunakan" 
                                               value="<?= old('produk_digunakan', $gallery['produk_digunakan'] ?? '') ?>">
                                        <small class="text-muted">Contoh: MAC, Maybelline, L'Oreal</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="lokasi_pemotretan" class="form-label">Lokasi Pemotretan</label>
                                        <input type="text" class="form-control" id="lokasi_pemotretan" name="lokasi_pemotretan" 
                                               value="<?= old('lokasi_pemotretan', $gallery['lokasi_pemotretan'] ?? '') ?>">
                                        <small class="text-muted">Contoh: Studio Jakarta, Outdoor Bandung</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="makeup_artist" class="form-label">Makeup Artist</label>
                                        <input type="text" class="form-control" id="makeup_artist" name="makeup_artist" 
                                               value="<?= old('makeup_artist', $gallery['makeup_artist'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="model" class="form-label">Model</label>
                                        <input type="text" class="form-control" id="model" name="model" 
                                               value="<?= old('model', $gallery['model'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO & Settings -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords (SEO)</label>
                                <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="2"><?= old('meta_keywords', $gallery['meta_keywords'] ?? '') ?></textarea>
                                <small class="text-muted">Pisahkan dengan koma</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="meta_description" class="form-label">Meta Description (SEO)</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?= old('meta_description', $gallery['meta_description'] ?? '') ?></textarea>
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
                                            <?= old('is_active', $gallery['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                            <?= old('is_featured', $gallery['is_featured'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_featured">
                                            Featured
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('admin/gallery') ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Gallery
                                </button>
                                <a href="<?= base_url('admin/gallery/hapus/' . $gallery['id']) ?>" 
                                   class="btn btn-danger" onclick="return confirmDelete()">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Preview & Info Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Gallery</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Info Gallery</h6>
                        <ul class="mb-0">
                            <li>ID: #<?= $gallery['id'] ?></li>
                            <li>Dibuat: <?= date('d/m/Y H:i', strtotime($gallery['created_at'])) ?></li>
                            <li>Diupdate: <?= date('d/m/Y H:i', strtotime($gallery['updated_at'])) ?></li>
                            <li>Gambar: <?= $gallery['gambar'] ? '✓' : '×' ?></li>
                            <li>Gambar Tambahan: <?= count($gallery['gambar_tambahan'] ?? []) ?> file</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status Saat Ini:</label>
                        <div>
                            <?php if ($gallery['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                            
                            <?php if ($gallery['is_featured']): ?>
                                <span class="badge bg-warning ms-2">Featured</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kategori:</label>
                        <div class="badge bg-light text-dark">
                            <?= $kategori_options[$gallery['kategori']] ?? ucfirst($gallery['kategori']) ?>
                        </div>
                        <?php if (!empty($gallery['style'])): ?>
                            <div class="badge bg-info mt-1">
                                <?= $style_options[$gallery['style']] ?? $gallery['style'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Preview di Website:</label>
                        <a href="<?= base_url('gallery/' . $gallery['id']) ?>" 
                           class="btn btn-sm btn-outline-info w-100" 
                           target="_blank">
                            <i class="bi bi-eye"></i> Lihat di Website
                        </a>
                    </div>
                    
                    <hr>
                    
                    <!-- Quick Stats -->
                    <div class="mb-3">
                        <h6>Statistik:</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="small text-muted">Views</div>
                                <div class="fw-bold">0</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">Shares</div>
                                <div class="fw-bold">0</div>
                            </div>
                        </div>
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
    const deletedImagesInput = document.getElementById('deletedImages');
    
    // Track deleted images
    let deletedImages = [];
    
    // Preview main image
    if (gambarInput) {
        gambarInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('gambarPreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    
                    // Hide current image preview
                    const currentImage = document.getElementById('currentMainImage');
                    if (currentImage) {
                        currentImage.style.opacity = '0.5';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Preview new additional images
    if (gambarTambahanInput) {
        gambarTambahanInput.addEventListener('change', function(e) {
            const files = Array.from(this.files);
            const previewContainer = document.getElementById('newImagesPreview');
            
            if (files.length > 0) {
                let previewHTML = '<div class="row g-2">';
                
                files.forEach((file, index) => {
                    if (index >= 4) return; // Show only first 4 images
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.getElementById(`newPreviewImg${index}`);
                        if (img) img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    
                    previewHTML += `
                        <div class="col-3">
                            <img id="newPreviewImg${index}" 
                                 alt="New Image ${index + 1}" 
                                 class="img-fluid rounded border" 
                                 style="height: 80px; object-fit: cover;"
                                 onload="this.style.display='block'">
                            <div class="small text-muted mt-1">${file.name.length > 10 ? file.name.substring(0, 8) + '...' : file.name}</div>
                        </div>
                    `;
                });
                
                previewHTML += '</div>';
                if (files.length > 4) {
                    previewHTML += `<div class="mt-2 small text-info">+${files.length - 4} gambar lainnya</div>`;
                }
                
                previewContainer.innerHTML = previewHTML;
            } else {
                previewContainer.innerHTML = '';
            }
        });
    }
    
    // Form submission handler
    // Di bagian form submission handler
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
    
    // Check new main image if uploaded
    if (gambarInput.files && gambarInput.files.length > 0) {
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
    
    // Check new additional images
    if (gambarTambahanInput.files.length > 10) {
        errors.push('Maksimal 10 gambar tambahan');
        gambarTambahanInput.classList.add('is-invalid');
        isValid = false;
    } else {
        gambarTambahanInput.classList.remove('is-invalid');
    }
    
    // FIX: Check if deletedImagesInput exists before setting value
    if (deletedImagesInput) {
        deletedImagesInput.value = JSON.stringify(deletedImages);
    }
    
    if (!isValid) {
        // Show errors
        alert('Perbaiki kesalahan berikut:\n\n' + errors.join('\n'));
        return false;
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memperbarui...';
    submitBtn.disabled = true;
    
    // Submit form
    this.submit();
});
    
    // Delete confirmation
    window.confirmDelete = function() {
        return confirm('Apakah Anda yakin ingin menghapus gallery ini? Semua gambar akan dihapus permanen.');
    };
    
    // Initial category management load
    loadCategories();
    
    // Add category form submission
    document.getElementById('addCategoryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addNewCategory();
    });
});

// Remove additional image
function removeAdditionalImage(imageName) {
    if (confirm('Hapus gambar ini?')) {
        // Add to deleted images array
        if (!deletedImages.includes(imageName)) {
            deletedImages.push(imageName);
        }
        
        // Remove from DOM
        const element = document.querySelector(`[data-image="${imageName}"]`);
        if (element) {
            element.remove();
        }
        
        // Update message if no images left
        const container = document.getElementById('currentAdditionalImages');
        if (container && container.children.length === 0) {
            container.innerHTML = '<div class="text-muted">Tidak ada gambar tambahan</div>';
        }
    }
}

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
        const isCurrentCategory = '<?= $gallery["kategori"] ?>' === category.value;
        
        html += `
            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 ${isCurrentCategory ? 'active' : ''}">
                <div>
                    <span class="badge bg-light text-dark">${category.total || 0}</span>
                    <span class="ms-2 ${isCurrentCategory ? 'text-white' : ''}">${category.label}</span>
                    <small class="d-block ${isCurrentCategory ? 'text-white-50' : 'text-muted'}">${category.value}</small>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-${isCurrentCategory ? 'light' : 'warning'} btn-sm" 
                            onclick="editCategory('${category.value}', '${category.label}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-${isCurrentCategory ? 'light' : 'danger'} btn-sm" 
                            onclick="deleteCategory('${category.value}')" 
                            ${category.total > 0 ? 'disabled' : ''}>
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

// Edit category
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

// Auto-save description to localStorage (draft)
function autoSaveDraft() {
    const draftKey = 'gallery_draft_<?= $gallery["id"] ?>';
    const formData = {
        judul: document.getElementById('judul').value,
        deskripsi: document.getElementById('deskripsi').value,
        tema_warna: document.getElementById('tema_warna').value,
        produk_digunakan: document.getElementById('produk_digunakan').value,
        timestamp: new Date().toISOString()
    };
    
    localStorage.setItem(draftKey, JSON.stringify(formData));
    
    // Show saved indicator
    const indicator = document.createElement('div');
    indicator.className = 'alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
    indicator.style.zIndex = '1050';
    indicator.innerHTML = `
        <i class="bi bi-check-circle"></i> Draft disimpan
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(indicator);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        indicator.remove();
    }, 3000);
}

// Load draft from localStorage
function loadDraft() {
    const draftKey = 'gallery_draft_<?= $gallery["id"] ?>';
    const draft = localStorage.getItem(draftKey);
    
    if (draft) {
        if (confirm('Ada draft yang tersimpan. Load draft?')) {
            const data = JSON.parse(draft);
            document.getElementById('judul').value = data.judul || '';
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.getElementById('tema_warna').value = data.tema_warna || '';
            document.getElementById('produk_digunakan').value = data.produk_digunakan || '';
        }
    }
}

// Auto-save on input (debounced)
let saveTimeout;
document.getElementById('deskripsi').addEventListener('input', function() {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(autoSaveDraft, 2000);
});
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

.img-thumbnail {
    border-radius: 5px;
    border: 1px solid #dee2e6;
    transition: opacity 0.3s ease;
}

.position-relative .btn {
    border-radius: 50%;
    width: 24px;
    height: 24px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.position-relative .btn:hover {
    opacity: 1;
}

.position-relative:hover .btn {
    opacity: 1;
}

/* Category management styles */
.list-group-item.active {
    background-color: var(--accent-color);
    border-color: var(--accent-color);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Preview image styles */
#gambarPreview {
    border: 2px dashed #dee2e6;
    padding: 5px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-group {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .btn-group .btn {
        margin-bottom: 0.5rem;
    }
}

/* Animation for deleted images */
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; transform: scale(0.8); }
}

.removing {
    animation: fadeOut 0.3s ease forwards;
}
</style>

<?= $this->endSection() ?>