<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="<?= base_url('admin/paket/tambah') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Paket
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Total Paket</div>
                            <div class="fw-bold fs-4"><?= $stats['total'] ?? 0 ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box-seam fs-1 text-primary"></i>
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
                            <div class="text-muted fw-semibold">Rata-rata Harga</div>
                            <div class="fw-bold fs-4">Rp <?= number_format($stats['average_price'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <form action="<?= base_url('admin/paket/bulk-action') ?>" method="POST" id="bulkForm">
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
                    <small class="text-muted"><?= $stats['total'] ?? 0 ?> paket total</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Paket -->
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
                <table class="table table-hover" id="paketTable">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Nama Paket</th>
                            <th>Harga</th>
                            <th>Durasi</th>
                            <th>Fitur</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Urutan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($paket)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data paket</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($paket as $p): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input item-checkbox" value="<?= $p['id'] ?>">
                                </td>
                                <td>
                                    <strong><?= $p['nama_paket'] ?></strong>
                                    <br>
                                    <small class="text-muted"><?= character_limiter($p['deskripsi'] ?? '', 60) ?></small>
                                </td>
                                <td>
                                    <strong>Rp <?= number_format($p['harga'], 0, ',', '.') ?></strong>
                                </td>
                                <td><?= $p['durasi'] ?? '-' ?></td>
                                <td>
                                    <?php if (!empty($p['features']) && is_array($p['features'])): ?>
                                        <small><?= count($p['features']) ?> fitur</small>
                                        <br>
                                        <small class="text-muted">
                                            <?= implode(', ', array_slice($p['features'], 0, 2)) ?>
                                            <?php if (count($p['features']) > 2): ?>...<?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($p['is_featured']): ?>
                                        <span class="badge bg-warning">Featured</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $p['urutan'] ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/paket/edit/' . $p['id']) ?>" class="btn btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="<?= base_url('admin/paket/toggle-status/' . $p['id']) ?>" class="btn btn-outline-<?= $p['is_active'] ? 'danger' : 'success' ?>" title="<?= $p['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <i class="bi bi-power"></i>
                                        </a>
                                        <a href="<?= base_url('admin/paket/toggle-featured/' . $p['id']) ?>" class="btn btn-outline-<?= $p['is_featured'] ? 'secondary' : 'warning' ?>" title="<?= $p['is_featured'] ? 'Unfeature' : 'Feature' ?>">
                                            <i class="bi bi-star"></i>
                                        </a>
                                        <a href="<?= base_url('admin/paket/hapus/' . $p['id']) ?>" class="btn btn-outline-danger" title="Hapus" onclick="return confirmDelete(<?= $p['id'] ?>)">
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
                alert('Pilih minimal satu paket terlebih dahulu.');
                return false;
            }
            
            if (!actionSelect.value) {
                alert('Pilih aksi yang ingin dilakukan.');
                actionSelect.focus();
                return false;
            }
            
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
            
            const actionText = actionSelect.options[actionSelect.selectedIndex].text;
            const confirmation = confirm(`Apakah Anda yakin ingin ${actionText.toLowerCase()} ${checkedIds.length} paket yang dipilih?`);
            
            if (confirmation) {
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
    return confirm('Apakah Anda yakin ingin menghapus paket ini?');
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
</style>

<?= $this->endSection() ?>