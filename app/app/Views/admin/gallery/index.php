<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="<?= base_url('admin/gallery/tambah') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Gallery
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Total Gallery</div>
                            <div class="fw-bold fs-4"><?= $stats['total'] ?? 0 ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-images fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Aktif</div>
                            <div class="fw-bold fs-4"><?= $stats['active'] ?? 0 ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Featured</div>
                            <div class="fw-bold fs-4"><?= $stats['featured'] ?? 0 ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-star fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Kategori</div>
                            <div class="fw-bold fs-4"><?= count($stats['by_kategori'] ?? []) ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-tags fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_options as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($filter_kategori == $value) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="style" class="form-label">Style</label>
                    <select name="style" class="form-select">
                        <option value="">Semua Style</option>
                        <?php foreach ($style_options as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($filter_style == $value) ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" <?= ($filter_status == 'all') ? 'selected' : '' ?>>Semua Status</option>
                        <option value="active" <?= ($filter_status == 'active') ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= ($filter_status == 'inactive') ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari judul/deskripsi..." 
                               value="<?= $filter_search ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($filter_kategori || $filter_status || $filter_search || $filter_style): ?>
                        <a href="<?= base_url('admin/gallery') ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <form action="<?= base_url('admin/gallery/bulk-action') ?>" method="POST" id="bulkForm">
                        <?= csrf_field() ?>
                        <div class="d-flex align-items-center">
                            <select name="action" class="form-select form-select-sm me-2" style="width: auto;" required>
                                <option value="">Pilih Aksi</option>
                                <option value="activate">Aktifkan</option>
                                <option value="deactivate">Nonaktifkan</option>
                                <option value="feature">Set Featured</option>
                                <option value="unfeature">Unfeature</option>
                                <option value="delete">Hapus</option>
                            </select>
                            <input type="hidden" name="ids[]" id="bulkIds">
                            <button type="submit" class="btn btn-sm btn-primary" id="bulkSubmitBtn">
                                Terapkan
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="selectAllCheckboxes()">
                                Pilih Semua
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted"><?= count($gallery) ?> gallery ditemukan</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Gallery -->
    <div class="card">
        <div class="card-body">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('warning') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover" id="galleryTable">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Style</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($gallery)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data gallery</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($gallery as $g): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input item-checkbox" value="<?= $g['id'] ?>">
                                </td>
                                <td>
                                    <?php if ($g['gambar']): ?>
                                        <img src="<?= base_url('uploads/gallery/' . $g['gambar']) ?>" 
                                             alt="<?= $g['judul'] ?>" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light text-center" style="width: 80px; height: 60px; line-height: 60px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $g['judul'] ?></strong>
                                    <br>
                                    <small class="text-muted"><?= character_limiter($g['deskripsi'] ?? '', 60) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $kategori_options[$g['kategori']] ?? ucfirst($g['kategori']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($g['style'])): ?>
                                        <span class="badge bg-info">
                                            <?= $style_options[$g['style']] ?? $g['style'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($g['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($g['is_featured']): ?>
                                        <span class="badge bg-warning">Featured</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $g['urutan'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('gallery/' . $g['id']) ?>" 
                                           class="btn btn-outline-info" 
                                           title="Preview" 
                                           target="_blank">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= base_url('admin/gallery/edit/' . $g['id']) ?>" 
                                           class="btn btn-outline-warning" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('admin/gallery/toggle-status/' . $g['id']) ?>" 
                                           class="btn btn-outline-<?= $g['is_active'] ? 'danger' : 'success' ?>" 
                                           title="<?= $g['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi bi-power"></i>
                                        </a>
                                        <a href="<?= base_url('admin/gallery/toggle-featured/' . $g['id']) ?>" 
                                           class="btn btn-outline-<?= $g['is_featured'] ? 'secondary' : 'warning' ?>" 
                                           title="<?= $g['is_featured'] ? 'Unfeature' : 'Feature' ?>">
                                            <i class="bi bi-star"></i>
                                        </a>
                                        <a href="<?= base_url('admin/gallery/hapus/' . $g['id']) ?>" 
                                           class="btn btn-outline-danger" 
                                           title="Hapus" 
                                           onclick="return confirmDelete(<?= $g['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const bulkForm = document.getElementById('bulkForm');
    const bulkSubmitBtn = document.getElementById('bulkSubmitBtn');
    const bulkIdsInput = document.getElementById('bulkIds');
    
    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateBulkButton();
        });
    }
    
    // Individual checkbox functionality
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateBulkButton();
        });
    });
    
    // Update select all checkbox based on individual checkboxes
    function updateSelectAll() {
        if (checkboxes.length === 0) return;
        
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const someChecked = Array.from(checkboxes).some(cb => cb.checked);
        
        if (selectAll) {
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        }
    }
    
    // Update bulk action button state
    function updateBulkButton() {
        const checkedCount = getCheckedCount();
        if (bulkSubmitBtn) {
            bulkSubmitBtn.disabled = checkedCount === 0;
            bulkSubmitBtn.textContent = checkedCount > 0 
                ? `Terapkan (${checkedCount})` 
                : 'Terapkan';
        }
    }
    
    // Get count of checked checkboxes
    function getCheckedCount() {
        return Array.from(checkboxes).filter(cb => cb.checked).length;
    }
    
    // Get array of checked IDs
    function getCheckedIds() {
        return Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
    }
    
    // Select all checkboxes
    window.selectAllCheckboxes = function() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const newState = !allChecked;
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = newState;
        });
        
        if (selectAll) {
            selectAll.checked = newState;
            selectAll.indeterminate = false;
        }
        
        updateBulkButton();
    };
    
    // Bulk form submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const checkedIds = getCheckedIds();
            const actionSelect = this.querySelector('select[name="action"]');
            
            if (checkedIds.length === 0) {
                alert('Pilih minimal satu gallery terlebih dahulu.');
                return false;
            }
            
            if (!actionSelect.value) {
                alert('Pilih aksi yang ingin dilakukan.');
                actionSelect.focus();
                return false;
            }
            
            const actionText = actionSelect.options[actionSelect.selectedIndex].text;
            const confirmation = confirm(`Apakah Anda yakin ingin ${actionText.toLowerCase()} ${checkedIds.length} gallery yang dipilih?`);
            
            if (confirmation) {
                // Set the IDs in hidden input
                if (bulkIdsInput) {
                    bulkIdsInput.value = checkedIds.join(',');
                } else {
                    // Create hidden inputs for each ID
                    checkedIds.forEach(id => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        this.appendChild(input);
                    });
                }
                
                // Disable button and show loading
                if (bulkSubmitBtn) {
                    bulkSubmitBtn.disabled = true;
                    bulkSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
                }
                
                // Submit the form
                this.submit();
            }
            
            return false;
        });
    }
    
    // Initialize button state
    updateBulkButton();
});

function confirmDelete(id) {
    return confirm('Apakah Anda yakin ingin menghapus gallery ini?');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+A to select all
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        window.selectAllCheckboxes();
    }
});
</script>

<style>
/* Style for indeterminate checkbox */
input[type="checkbox"]:indeterminate {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

#bulkSubmitBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.item-checkbox {
    cursor: pointer;
}

#selectAll {
    cursor: pointer;
}

.img-thumbnail {
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
</style>

<?= $this->endSection() ?>