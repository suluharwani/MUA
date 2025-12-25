<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <!-- <button type="button" class="btn btn-primary me-2" id="addBtn">
                <i class="bi bi-plus-circle"></i> Tambah
            </button> -->
            <button type="button" class="btn btn-outline-secondary me-2" id="backupBtn">
                <i class="bi bi-download"></i> Backup
            </button>
            <button type="button" class="btn btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#restoreModal">
                <i class="bi bi-upload"></i> Restore
            </button>
            <button type="button" class="btn btn-outline-warning" id="resetBtn">
                <i class="bi bi-arrow-clockwise"></i> Reset Default
            </button>
        </div>
    </div>

    <!-- Category Tabs -->
    <ul class="nav nav-tabs mb-4" id="categoryTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link category-tab active" data-category="all">
            Semua Kategori
        </button>
    </li>
    <?php foreach ($categories as $catKey => $catLabel): ?>
    <li class="nav-item" role="presentation">
        <button class="nav-link category-tab" data-category="<?= $catKey ?>">
            <?= $catLabel ?>
        </button>
    </li>
    <?php endforeach; ?>
</ul>

    <!-- Settings Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengaturan</h5>
            <div class="d-flex align-items-center">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari pengaturan...">
                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="settingsTable" class="table table-hover w-100">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="15%">Key Name</th>
                            <th width="20%">Label</th>
                            <th width="25%">Value</th>
                            <th width="10%">Tipe</th>
                            <th width="10%">Kategori</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Save Form -->
    <div class="card mt-4 d-none" id="quickSaveForm">
        <div class="card-header">
            <h5 class="mb-0">Simpan Cepat Multiple Pengaturan</h5>
        </div>
        <div class="card-body">
            <form id="quickSaveSettingsForm">
                <div id="quickSettingsFields">
                    <!-- Fields will be generated dynamically -->
                </div>
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelQuickSave">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
                    </button>
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="settingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah/Edit Pengaturan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="settingForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="settingId">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Key Name <span class="text-danger">*</span></label>
                            <input type="text" name="key_name" class="form-control" required>
                            <div class="form-text">Hanya huruf, angka, dash, underscore. Contoh: nama_toko, whatsapp_number</div>
                            <div class="invalid-feedback" id="key_name_error"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" class="form-control" required>
                            <div class="invalid-feedback" id="label_error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" id="typeSelect" required>
                                <option value="">Pilih Tipe</option>
                                <?php foreach ($field_types as $typeKey => $typeLabel): ?>
                                <option value="<?= $typeKey ?>"><?= $typeLabel ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="type_error"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $catKey => $catLabel): ?>
                                <option value="<?= $catKey ?>"><?= $catLabel ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="category_error"></div>
                        </div>
                    </div>
                    
                    <!-- Options Field (for select, checkbox, radio) -->
                    <div class="mb-3 d-none" id="optionsField">
                        <label class="form-label">Options</label>
                        <textarea name="options" class="form-control" rows="3" placeholder="Format: value:label (satu per baris)&#10;Contoh:&#10;id:Indonesia&#10;en:English&#10;other:Lainnya"></textarea>
                        <div class="form-text">Untuk tipe select, checkbox, radio. Format: value:label (satu per baris)</div>
                    </div>
                    
                    <!-- Value Field -->
                    <div class="mb-3" id="valueField">
                        <label class="form-label">Value</label>
                        <div id="valueInputContainer">
                            <input type="text" name="value" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Placeholder</label>
                            <input type="text" name="placeholder" class="form-control">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" name="order" class="form-control" value="0">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" name="required" class="form-check-input" id="requiredCheck">
                        <label class="form-check-label" for="requiredCheck">Wajib diisi</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restore Pengaturan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="restoreForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan!</strong> Restore akan menghapus semua pengaturan saat ini.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih File Backup (.json)</label>
                        <input type="file" name="backup_file" class="form-control" accept=".json" required>
                        <small class="text-muted">File harus berformat JSON yang dihasilkan dari fitur backup</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Restore</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Edit Modal -->
<div class="modal fade" id="quickEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickEditForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="quickEditId">
                    
                    <div class="mb-3">
                        <label class="form-label" id="quickEditLabel"></label>
                        <div id="quickEditInputContainer"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add/Edit Modal -->
<div class="modal fade" id="settingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah/Edit Pengaturan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="settingForm" enctype="multipart/form-data"> <!-- TAMBAHKAN INI -->
                <!-- ... konten form ... -->
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    let currentCategory = 'general';
    let dataTable;
    let allSettings = [];
    
    // Initialize DataTable
    // Ganti fungsi initDataTable() dengan ini:
function initDataTable() {
    dataTable = $('#settingsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url("admin/pengaturan-ajax/getSettings") ?>',
            type: 'GET',
            data: function(d) {
                // Tambahkan parameter kategori ke DataTables
                d.category = currentCategory;
                
                // DEBUG: Cek data yang dikirim
                console.log('DataTables request:', {
                    draw: d.draw,
                    start: d.start,
                    length: d.length,
                    search: d.search.value,
                    category: d.category,
                    orderColumn: d.order[0].column,
                    orderDir: d.order[0].dir
                });
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', xhr.responseText);
                Swal.fire('Error', 'Terjadi kesalahan saat memuat data', 'error');
            }
        },
        columns: [
            { data: 'id' },
            { data: 'key_name' },
            { data: 'label' },
            { data: 'value' },
            { data: 'type' },
            { data: 'category' },
            { data: 'status' },
            { 
                data: 'actions',
                orderable: false,
                searchable: false
            }
        ],
        order: [[0, 'asc']],
        pageLength: 10,
        responsive: true,
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            zeroRecords: "Data tidak ditemukan",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });
}

// Perbaikan untuk category tab click:
$('.category-tab').click(function(e) {
    e.preventDefault();
    
    $('.category-tab').removeClass('active');
    $(this).addClass('active');
    currentCategory = $(this).data('category');
    
    console.log('Mengubah kategori ke:', currentCategory);
    
    // Refresh DataTable dengan kategori baru
    if (dataTable) {
        dataTable.ajax.reload(null, false);
    }
});

// Tambahkan event untuk semua kategori
$('.category-tab').click(function() {
    $('.category-tab').removeClass('active');
    $(this).addClass('active');
    currentCategory = $(this).data('category');
    
    // Reset pencarian
    $('#searchInput').val('');
    
    // Reload DataTable
    dataTable.ajax.reload(null, false);
});
    // Initialize
    initDataTable();
    
    // Category tab click
    $('.category-tab').click(function() {
        $('.category-tab').removeClass('active');
        $(this).addClass('active');
        currentCategory = $(this).data('category');
        
        // Reload DataTable
        dataTable.ajax.reload(null, false);
    });
    
    // Search function
    $('#searchBtn').click(function() {
        dataTable.search($('#searchInput').val()).draw();
    });
    
    $('#searchInput').keypress(function(e) {
        if (e.which == 13) {
            dataTable.search($(this).val()).draw();
        }
    });
    
    // Add button click
    $('#addBtn').click(function() {
        $('#settingForm')[0].reset();
        $('#settingId').val('');
        $('#settingModal .modal-title').text('Tambah Pengaturan');
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
        
        // Reset value field to text input
        $('#valueInputContainer').html('<input type="text" name="value" class="form-control">');
        
        $('#settingModal').modal('show');
    });
    
    // Edit button click
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url("admin/pengaturan-ajax/getSettingDetail/") ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const setting = response.data;
                    
                    $('#settingId').val(setting.id);
                    $('#settingForm [name="key_name"]').val(setting.key_name);
                    $('#settingForm [name="label"]').val(setting.label);
                    $('#settingForm [name="type"]').val(setting.type);
                    $('#settingForm [name="category"]').val(setting.category);
                    $('#settingForm [name="placeholder"]').val(setting.placeholder);
                    $('#settingForm [name="order"]').val(setting.order);
                    $('#settingForm [name="is_active"]').val(setting.is_active);
                    $('#settingForm [name="required"]').prop('checked', setting.required == 1);
                    
                    // Handle options
                    if (setting.options && typeof setting.options === 'object') {
                        let optionsText = '';
                        for (const [value, label] of Object.entries(setting.options)) {
                            optionsText += value + ':' + label + '\n';
                        }
                        $('#settingForm [name="options"]').val(optionsText.trim());
                    } else {
                        $('#settingForm [name="options"]').val(setting.options || '');
                    }
                    
                    // Handle value based on type
                    updateValueField(setting.type, setting.value);
                    
                    $('#settingModal .modal-title').text('Edit Pengaturan: ' + setting.label);
                    $('#settingModal').modal('show');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat mengambil data', 'error');
            }
        });
    });
    
    // Update value field based on type
    // Update value field based on type
function updateValueField(type, value = '') {
    let html = '';
    
    switch (type) {
        case 'textarea':
            html = `<textarea name="value" class="form-control" rows="3">${value}</textarea>`;
            break;
        case 'number':
            html = `<input type="number" name="value" class="form-control" value="${value}">`;
            break;
        case 'email':
            html = `<input type="email" name="value" class="form-control" value="${value}">`;
            break;
        case 'tel':
            html = `<input type="tel" name="value" class="form-control" value="${value}">`;
            break;
        case 'password':
            html = `<input type="password" name="value" class="form-control" value="${value}" placeholder="Kosongkan jika tidak ingin mengubah">`;
            break;
        case 'select':
            // This will be populated when saving, show textarea for now
            html = `<input type="text" name="value" class="form-control" value="${value}">`;
            break;
        case 'checkbox':
            // For checkbox, value is comma-separated
            html = `<input type="text" name="value" class="form-control" value="${value}" placeholder="nilai1,nilai2,nilai3">`;
            break;
        case 'radio':
            html = `<input type="text" name="value" class="form-control" value="${value}">`;
            break;
        case 'color':
            html = `<input type="color" name="value" class="form-control form-control-color" value="${value || '#000000'}">`;
            break;
        case 'date':
            html = `<input type="date" name="value" class="form-control" value="${value}">`;
            break;
        case 'file':
            // File input dengan preview jika ada
            let filePreview = '';
            if (value) {
                const fileName = value.split('/').pop();
                filePreview = `
                    <div class="mb-2">
                        <small>File saat ini: <a href="${base_url(value)}" target="_blank">${fileName}</a></small>
                        <br>
                        <small class="text-muted">Unggah file baru untuk mengganti</small>
                    </div>`;
            }
            html = filePreview + `<input type="file" name="value" class="form-control" accept=".jpg,.jpeg,.png,.gif,.svg,.ico,.webp">`;
            break;
        default:
            html = `<input type="text" name="value" class="form-control" value="${value}">`;
    }
    
    $('#valueInputContainer').html(html);
}
    
    // Type select change
    $('#typeSelect').change(function() {
        const type = $(this).val();
        
        // Show/hide options field
        if (['select', 'checkbox', 'radio'].includes(type)) {
            $('#optionsField').removeClass('d-none');
        } else {
            $('#optionsField').addClass('d-none');
        }
        
        // Update value field
        updateValueField(type);
    });
    
    // Save setting form
    $('#settingForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= base_url("admin/pengaturan-ajax/save") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500
                    });
                    
                    $('#settingModal').modal('hide');
                    dataTable.ajax.reload(null, false);
                } else if (response.status === 'error' && response.errors) {
                    // Display validation errors
                    for (const [field, message] of Object.entries(response.errors)) {
                        $(`[name="${field}"]`).addClass('is-invalid');
                        $(`#${field}_error`).text(message);
                    }
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan saat menyimpan', 'error');
            }
        });
    });
    
    // Toggle status
    $(document).on('click', '.toggle-status-btn', function() {
        const id = $(this).data('id');
        const button = $(this);
        
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Yakin ingin mengubah status pengaturan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, ubah',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("admin/pengaturan-ajax/toggleStatus/") ?>' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            dataTable.ajax.reload(null, false);
                            Swal.fire('Berhasil', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });
    


    
    // Backup
    $('#backupBtn').click(function() {
        Swal.fire({
            title: 'Backup Pengaturan',
            text: 'Yakin ingin membuat backup pengaturan?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, backup',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url("admin/pengaturan-ajax/backup") ?>';
            }
        });
    });
    
    // Restore form
    $('#restoreForm').submit(function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Restore Pengaturan',
            html: '<strong>Peringatan!</strong> Semua pengaturan saat ini akan dihapus dan diganti dengan data backup.<br>Yakin ingin melanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, restore',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const formData = new FormData(this);
                
                return $.ajax({
                    url: '<?= base_url("admin/pengaturan-ajax/restore") ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json'
                }).then(response => {
                    if (response.status !== 'success') {
                        throw new Error(response.message);
                    }
                    return response;
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.value.message,
                }).then(() => {
                    $('#restoreModal').modal('hide');
                    dataTable.ajax.reload(null, false);
                });
            }
        }).catch(error => {
            Swal.fire('Error', error.message, 'error');
        });
    });
    
    // Reset to default
    $('#resetBtn').click(function() {
        Swal.fire({
            title: 'Reset ke Default',
            html: 'Yakin ingin reset semua pengaturan ke nilai default?<br><small class="text-danger">Pengaturan custom akan hilang!</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, reset',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc3545',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: '<?= base_url("admin/pengaturan-ajax/initialize") ?>',
                    type: 'POST',
                    dataType: 'json'
                }).then(response => {
                    if (response.status !== 'success') {
                        throw new Error(response.message);
                    }
                    return response;
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.value.message,
                }).then(() => {
                    dataTable.ajax.reload(null, false);
                });
            }
        }).catch(error => {
            Swal.fire('Error', error.message, 'error');
        });
    });
    
$(document).on('dblclick', '#settingsTable tbody tr td:nth-child(4)', function() {
    const rowData = dataTable.row($(this).closest('tr')).data();
    if (!rowData || !rowData.id) return;
    
    // Extract numeric ID from HTML
    const idMatch = rowData.id.toString().match(/\d+/);
    if (!idMatch) return;
    
    const settingId = idMatch[0];
    
    // Get full setting data
    $.ajax({
        url: '<?= base_url("admin/pengaturan-ajax/getSettingDetail/") ?>' + settingId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const setting = response.data;
                
                $('#quickEditId').val(setting.id);
                $('#quickEditLabel').text(setting.label);
                    
                    // Create appropriate input based on type
                    let inputHtml = '';
                    switch (setting.type) {
                        case 'textarea':
                            inputHtml = `<textarea name="value" class="form-control" rows="3">${setting.value || ''}</textarea>`;
                            break;
                        case 'number':
                            inputHtml = `<input type="number" name="value" class="form-control" value="${setting.value || ''}">`;
                            break;
                        case 'email':
                            inputHtml = `<input type="email" name="value" class="form-control" value="${setting.value || ''}">`;
                            break;
                        case 'tel':
                            inputHtml = `<input type="tel" name="value" class="form-control" value="${setting.value || ''}">`;
                            break;
                        case 'password':
                            inputHtml = `<input type="password" name="value" class="form-control" value="" placeholder="Kosongkan jika tidak ingin mengubah">`;
                            break;
                        case 'select':
                            if (setting.options && typeof setting.options === 'object') {
                                inputHtml = `<select name="value" class="form-select">`;
                                for (const [optValue, optLabel] of Object.entries(setting.options)) {
                                    inputHtml += `<option value="${optValue}" ${setting.value == optValue ? 'selected' : ''}>${optLabel}</option>`;
                                }
                                inputHtml += `</select>`;
                            } else {
                                inputHtml = `<input type="text" name="value" class="form-control" value="${setting.value || ''}">`;
                            }
                            break;
                        case 'checkbox':
                            // For checkbox, we need multiple checkboxes
                            if (setting.options && typeof setting.options === 'object') {
                                const selectedValues = setting.value ? setting.value.split(',') : [];
                                inputHtml = `<div class="border rounded p-3">`;
                                for (const [optValue, optLabel] of Object.entries(setting.options)) {
                                    const checked = selectedValues.includes(optValue);
                                    inputHtml += `
                                    <div class="form-check">
                                        <input type="checkbox" name="value[]" value="${optValue}" class="form-check-input" id="opt_${optValue}" ${checked ? 'checked' : ''}>
                                        <label class="form-check-label" for="opt_${optValue}">${optLabel}</label>
                                    </div>`;
                                }
                                inputHtml += `</div>`;
                            } else {
                                inputHtml = `<input type="text" name="value" class="form-control" value="${setting.value || ''}">`;
                            }
                            break;
                        case 'radio':
                            if (setting.options && typeof setting.options === 'object') {
                                inputHtml = `<div class="border rounded p-3">`;
                                for (const [optValue, optLabel] of Object.entries(setting.options)) {
                                    const checked = setting.value == optValue;
                                    inputHtml += `
                                    <div class="form-check">
                                        <input type="radio" name="value" value="${optValue}" class="form-check-input" id="opt_${optValue}" ${checked ? 'checked' : ''}>
                                        <label class="form-check-label" for="opt_${optValue}">${optLabel}</label>
                                    </div>`;
                                }
                                inputHtml += `</div>`;
                            } else {
                                inputHtml = `<input type="text" name="value" class="form-control" value="${setting.value || ''}">`;
                            }
                            break;
                        case 'color':
                            inputHtml = `<input type="color" name="value" class="form-control form-control-color" value="${setting.value || '#000000'}">`;
                            break;
                        case 'date':
                            inputHtml = `<input type="date" name="value" class="form-control" value="${setting.value || ''}">`;
                            break;
                        default:
                            inputHtml = `<input type="text" name="value" class="form-control" value="${setting.value || ''}">`;
                    }
                    
                    $('#quickEditInputContainer').html(inputHtml);
                    $('#quickEditModal').modal('show');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });
    
// Quick edit form submit
$('#quickEditForm').submit(function(e) {
    e.preventDefault();
    
    let value = '';
    const settingId = $('#quickEditId').val();
    
    // Handle checkbox values (array to comma-separated)
    if ($('input[name="value[]"]').length > 0) {
        const checkedValues = [];
        $('input[name="value[]"]:checked').each(function() {
            checkedValues.push($(this).val());
        });
        value = checkedValues.join(',');
    } else if ($('select[name="value"]').length > 0) {
        value = $('select[name="value"]').val();
    } else if ($('textarea[name="value"]').length > 0) {
        value = $('textarea[name="value"]').val();
    } else {
        value = $('input[name="value"]').val();
    }
    
    // Buat form data
    const formData = new FormData();
    formData.append('id', settingId);
    formData.append('value', value);
    
    $.ajax({
        url: '<?= base_url("admin/pengaturan-ajax/save") ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#quickEditModal').modal('hide');
                dataTable.ajax.reload(null, false);
                Swal.fire('Berhasil', 'Nilai berhasil diperbarui', 'success');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Terjadi kesalahan: ' + error, 'error');
        }
    });
});
    
    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl + F for search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            $('#searchInput').focus();
        }
        
        // Ctrl + N for new
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            $('#addBtn').click();
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });
});
</script>
<style>
.color-preview {
    border: 1px solid #dee2e6;
}

#settingsTable tbody tr {
    cursor: pointer;
}

#settingsTable tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.badge {
    font-size: 0.8em;
}

.form-control-color {
    height: 38px;
    padding: 2px;
}

.nav-tabs .nav-link {
    cursor: pointer;
}

.modal-lg {
    max-width: 800px;
}

.table th {
    font-weight: 600;
    font-size: 0.9em;
    text-transform: uppercase;
    color: #6c757d;
}
</style>
<?= $this->endSection() ?>