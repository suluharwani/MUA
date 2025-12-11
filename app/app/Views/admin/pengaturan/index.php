<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/pengaturan/tambah') ?>" class="btn btn-primary me-2">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
            <a href="<?= base_url('admin/pengaturan/backup') ?>" class="btn btn-outline-secondary me-2">
                <i class="bi bi-download"></i> Backup
            </a>
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#restoreModal">
                <i class="bi bi-upload"></i> Restore
            </button>
        </div>
    </div>

    <!-- Category Tabs -->
    <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
        <?php foreach ($categories as $catKey => $catLabel): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($current_category == $catKey) ? 'active' : '' ?>" 
                    id="<?= $catKey ?>-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#<?= $catKey ?>" 
                    type="button" 
                    role="tab"
                    onclick="window.location.href='<?= base_url('admin/pengaturan?category=' . $catKey) ?>'">
                <?= $catLabel ?>
            </button>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- Settings Form -->
    <div class="tab-content" id="settingsTabContent">
        <div class="tab-pane fade show active" id="<?= $current_category ?>" role="tabpanel">
            <form method="post" action="<?= base_url('admin/pengaturan/simpan') ?>">
                <?= csrf_field() ?>
                
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($settings)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Tidak ada pengaturan untuk kategori ini.
                            </div>
                        <?php else: ?>
                            <?php foreach ($settings as $setting): ?>
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">
                                        <?= $setting['label'] ?>
                                        <?php if ($setting['required']): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php if (!empty($setting['placeholder'])): ?>
                                        <p class="text-muted small mb-1"><?= $setting['placeholder'] ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-8">
                                    <?php
                                    $fieldName = $setting['key_name'];
                                    $fieldValue = old($fieldName, $setting['value']);
                                    $fieldType = $setting['type'];
                                    $fieldOptions = $setting['options'] ?? [];
                                    ?>
                                    
                                    <?php if ($fieldType == 'text'): ?>
                                        <input type="text" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    
                                    <?php elseif ($fieldType == 'textarea'): ?>
                                        <textarea name="<?= $fieldName ?>" 
                                                  class="form-control" 
                                                  rows="3"
                                                  <?= $setting['required'] ? 'required' : '' ?>><?= esc($fieldValue) ?></textarea>
                                    
                                    <?php elseif ($fieldType == 'number'): ?>
                                        <input type="number" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    
                                    <?php elseif ($fieldType == 'email'): ?>
                                        <input type="email" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    
                                    <?php elseif ($fieldType == 'tel'): ?>
                                        <input type="tel" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    
                                    <?php elseif ($fieldType == 'password'): ?>
                                        <input type="password" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    
                                    <?php elseif ($fieldType == 'select' && !empty($fieldOptions)): ?>
                                        <select name="<?= $fieldName ?>" 
                                                class="form-select"
                                                <?= $setting['required'] ? 'required' : '' ?>>
                                            <option value="">Pilih...</option>
                                            <?php foreach ($fieldOptions as $optValue => $optLabel): ?>
                                            <option value="<?= esc($optValue) ?>" 
                                                <?= ($fieldValue == $optValue) ? 'selected' : '' ?>>
                                                <?= esc($optLabel) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    
                                    <?php elseif ($fieldType == 'checkbox' && !empty($fieldOptions)): ?>
                                        <?php 
                                        $selectedValues = !empty($fieldValue) ? explode(',', $fieldValue) : [];
                                        ?>
                                        <div class="border rounded p-3">
                                            <?php foreach ($fieldOptions as $optValue => $optLabel): ?>
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       name="<?= $fieldName ?>[]" 
                                                       value="<?= esc($optValue) ?>"
                                                       class="form-check-input"
                                                       id="<?= $fieldName . '_' . $optValue ?>"
                                                       <?= in_array($optValue, $selectedValues) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="<?= $fieldName . '_' . $optValue ?>">
                                                    <?= esc($optLabel) ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    
                                    <?php elseif ($fieldType == 'radio' && !empty($fieldOptions)): ?>
                                        <div class="border rounded p-3">
                                            <?php foreach ($fieldOptions as $optValue => $optLabel): ?>
                                            <div class="form-check">
                                                <input type="radio" 
                                                       name="<?= $fieldName ?>" 
                                                       value="<?= esc($optValue) ?>"
                                                       class="form-check-input"
                                                       id="<?= $fieldName . '_' . $optValue ?>"
                                                       <?= ($fieldValue == $optValue) ? 'checked' : '' ?>
                                                       <?= $setting['required'] ? 'required' : '' ?>>
                                                <label class="form-check-label" for="<?= $fieldName . '_' . $optValue ?>">
                                                    <?= esc($optLabel) ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    
                                    <?php elseif ($fieldType == 'color'): ?>
                                        <input type="color" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control form-control-color" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    
                                    <?php elseif ($fieldType == 'date'): ?>
                                        <input type="date" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    
                                    <?php elseif ($fieldType == 'file'): ?>
                                        <div class="input-group">
                                            <input type="text" 
                                                   name="<?= $fieldName ?>" 
                                                   class="form-control" 
                                                   value="<?= esc($fieldValue) ?>"
                                                   <?= $setting['required'] ? 'required' : '' ?>>
                                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('file_<?= $fieldName ?>').click()">
                                                <i class="bi bi-upload"></i>
                                            </button>
                                            <input type="file" 
                                                   id="file_<?= $fieldName ?>" 
                                                   class="d-none" 
                                                   onchange="document.getElementsByName('<?= $fieldName ?>')[0].value = this.value">
                                        </div>
                                        <?php if (!empty($fieldValue)): ?>
                                            <small class="text-muted">File saat ini: <?= basename($fieldValue) ?></small>
                                        <?php endif; ?>
                                    
                                    <?php else: ?>
                                        <input type="text" 
                                               name="<?= $fieldName ?>" 
                                               class="form-control" 
                                               value="<?= esc($fieldValue) ?>"
                                               <?= $setting['required'] ? 'required' : '' ?>>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">Key: <code><?= $setting['key_name'] ?></code></small>
                                        <span class="float-end">
                                            <a href="<?= base_url('admin/pengaturan/edit/' . $setting['id']) ?>" class="text-primary me-2">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="<?= base_url('admin/pengaturan/toggle-status/' . $setting['id']) ?>" 
                                               class="text-<?= $setting['is_active'] ? 'danger' : 'success' ?> me-2">
                                                <i class="bi bi-power"></i> <?= $setting['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </a>
                                            <a href="<?= base_url('admin/pengaturan/hapus/' . $setting['id']) ?>" 
                                               class="text-danger" 
                                               onclick="return confirm('Hapus pengaturan ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (!$loop->last): ?>
                                <hr>
                            <?php endif; ?>
                            
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                        <a href="<?= base_url('admin/pengaturan/initialize') ?>" class="btn btn-outline-secondary" onclick="return confirm('Reset ke pengaturan default?')">
                            <i class="bi bi-arrow-clockwise"></i> Reset Default
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- System Information -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Informasi Sistem</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Nama Aplikasi</th>
                            <td><?= $system_info['app_name'] ?></td>
                        </tr>
                        <tr>
                            <th>Versi Aplikasi</th>
                            <td><?= $system_info['app_version'] ?></td>
                        </tr>
                        <tr>
                            <th>CodeIgniter</th>
                            <td><?= $system_info['ci_version'] ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">PHP Version</th>
                            <td><?= $system_info['php_version'] ?></td>
                        </tr>
                        <tr>
                            <th>Server</th>
                            <td><?= $system_info['server'] ?></td>
                        </tr>
                        <tr>
                            <th>Database</th>
                            <td><?= $system_info['database'] ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Environment</th>
                            <td><?= $system_info['environment'] ?></td>
                        </tr>
                        <tr>
                            <th>Timezone</th>
                            <td><?= $system_info['timezone'] ?></td>
                        </tr>
                        <tr>
                            <th>Base URL</th>
                            <td><?= $system_info['base_url'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="restoreModalLabel">Restore Pengaturan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?= base_url('admin/pengaturan/restore') ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan!</strong> Restore akan menghapus semua pengaturan saat ini dan menggantinya dengan data dari file backup.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih File Backup (.json)</label>
                        <input type="file" name="backup_file" class="form-control" accept=".json" required>
                        <small class="text-muted">File harus berformat JSON yang dihasilkan dari fitur backup</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Yakin ingin restore? Semua pengaturan saat ini akan hilang.')">
                        <i class="bi bi-upload"></i> Restore
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>