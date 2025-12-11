<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <button type="button" class="btn btn-outline-primary me-2" id="refreshBtn">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Tambah Kostum
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari...">
                </div>
                <div class="col-md-3">
                    <select id="kategoriFilter" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_options as $value => $label): ?>
                            <option value="<?= $value ?>"><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" id="filterBtn" class="btn btn-primary w-100">
                        <i class="bi bi-filter"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <select id="bulkActionSelect" class="form-select">
                        <option value="">Pilih Aksi</option>
                        <option value="activate">Aktifkan</option>
                        <option value="deactivate">Nonaktifkan</option>
                        <option value="feature">Tandai Featured</option>
                        <option value="unfeature">Hapus Featured</option>
                        <option value="delete">Hapus</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-outline-secondary" id="selectAllBtn">
                        <i class="bi bi-check-square"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="deselectAllBtn">
                        <i class="bi bi-square"></i> Batalkan Pilihan
                    </button>
                </div>
                <div class="col-md-4 text-end">
                    <button type="button" class="btn btn-primary" id="applyBulkActionBtn">
                        <i class="bi bi-check-circle"></i> Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="kostumTable" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>Gambar</th>
                            <th>Nama Kostum</th>
                            <th>Kategori</th>
                            <th>Harga Sewa</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan diisi oleh DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kostum Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="addErrorAlert"></div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label required">Nama Kostum</label>
                                <input type="text" name="nama_kostum" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Spesifikasi</label>
                                <textarea name="spesifikasi" class="form-control" rows="3" placeholder="Satu spesifikasi per baris"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gambar Kostum</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                                <small class="text-muted">Maks. 2MB (JPG, PNG, WebP)</small>
                                <div id="imagePreview" class="mt-2 text-center"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Kategori</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategori_options as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Durasi Sewa</label>
                                <select name="durasi_sewa" class="form-select">
                                    <option value="3 hari">3 Hari</option>
                                    <option value="24 Jam">24 Jam</option>
                                    <option value="1 Minggu">1 Minggu</option>
                                    <option value="2 Minggu">2 Minggu</option>
                                    <option value="1 Bulan">1 Bulan</option>
                                    <option value="Kustom">Kustom</option>
                                </select>
                                <input type="text" name="durasi_kustom" class="form-control mt-2 d-none" placeholder="Misal: 48 Jam, 5 Hari">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Harga Sewa</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_sewa" class="form-control" required min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Stok Total</label>
                                <input type="number" name="stok" class="form-control" required min="1">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stok Tersedia</label>
                                <input type="number" name="stok_tersedia" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Ukuran</label>
                                <input type="text" name="ukuran" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Warna</label>
                                <input type="text" name="warna" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Bahan</label>
                                <input type="text" name="bahan" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kondisi</label>
                                <select name="kondisi" class="form-select">
                                    <?php foreach ($kondisi_options as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Aktif</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured">
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                        </div>
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kostum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="editErrorAlert"></div>
                    <div class="alert alert-info d-none" id="currentImageAlert"></div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label required">Nama Kostum</label>
                                <input type="text" name="nama_kostum" id="edit_nama_kostum" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Spesifikasi</label>
                                <textarea name="spesifikasi" id="edit_spesifikasi" class="form-control" rows="3" placeholder="Satu spesifikasi per baris"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Gambar Kostum</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                                <small class="text-muted">Maks. 2MB (JPG, PNG, WebP)</small>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="hapus_gambar" id="hapus_gambar">
                                    <label class="form-check-label text-danger" for="hapus_gambar">
                                        Hapus gambar saat ini
                                    </label>
                                </div>
                                <div id="editImagePreview" class="mt-2 text-center"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Kategori</label>
                                <select name="kategori" id="edit_kategori" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategori_options as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Durasi Sewa</label>
                                <select name="durasi_sewa" id="edit_durasi_sewa" class="form-select">
                                    <option value="3 hari">3 Hari</option>
                                    <option value="24 Jam">24 Jam</option>
                                    <option value="1 Minggu">1 Minggu</option>
                                    <option value="2 Minggu">2 Minggu</option>
                                    <option value="1 Bulan">1 Bulan</option>
                                    <option value="Kustom">Kustom</option>
                                </select>
                                <input type="text" name="durasi_kustom" id="edit_durasi_kustom" class="form-control mt-2 d-none" placeholder="Misal: 48 Jam, 5 Hari">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Harga Sewa</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_sewa" id="edit_harga_sewa" class="form-control" required min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Stok Total</label>
                                <input type="number" name="stok" id="edit_stok" class="form-control" required min="1">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stok Tersedia</label>
                                <input type="number" name="stok_tersedia" id="edit_stok_tersedia" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Ukuran</label>
                                <input type="text" name="ukuran" id="edit_ukuran" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Warna</label>
                                <input type="text" name="warna" id="edit_warna" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Bahan</label>
                                <input type="text" name="bahan" id="edit_bahan" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kondisi</label>
                                <select name="kondisi" id="edit_kondisi" class="form-select">
                                    <?php foreach ($kondisi_options as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Urutan</label>
                                <input type="number" name="urutan" id="edit_urutan" class="form-control" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active">
                                <label class="form-check-label" for="edit_is_active">Aktif</label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="edit_is_featured">
                                <label class="form-check-label" for="edit_is_featured">Featured</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kostum</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="viewContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kostum ini?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Confirm Modal -->
<div class="modal fade" id="confirmBulkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Aksi Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="bulkConfirmText">Apakah Anda yakin ingin menerapkan aksi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmBulkBtn">Ya, Terapkan</button>
            </div>
        </div>
    </div>
</div>

<style>
.required:after {
    content: " *";
    color: #dc3545;
}
.dataTables_wrapper {
    padding: 0;
}
#kostumTable tbody tr {
    cursor: pointer;
}
#kostumTable tbody tr:hover {
    background-color: rgba(0,0,0,.02);
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#kostumTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url("admin/kostum-ajax/getKostum") ?>',
            type: 'POST',
            data: function(d) {
                d.search = $('#searchInput').val();
                d.kategori = $('#kategoriFilter').val();
                d.status = $('#statusFilter').val();
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'gambar', orderable: false, searchable: false },
            { data: 'nama_kostum' },
            { data: 'kategori' },
            { data: 'harga_sewa' },
            { data: 'stok' },
            { data: 'status' },
            { data: 'featured' },
            { data: 'created_at' },
            { data: 'aksi', orderable: false, searchable: false }
        ],
        order: [[8, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });

    // Refresh button
    $('#refreshBtn').click(function() {
        table.ajax.reload();
        showToast('Data diperbarui', 'success');
    });

    // Filter button
    $('#filterBtn').click(function() {
        table.ajax.reload();
    });

    // Enter key in search
    $('#searchInput').keypress(function(e) {
        if (e.which == 13) {
            table.ajax.reload();
        }
    });

    // Check all checkboxes
    $('#checkAll').click(function() {
        $('.row-checkbox').prop('checked', this.checked);
    });

    // Select all button
    $('#selectAllBtn').click(function() {
        $('.row-checkbox').prop('checked', true);
        $('#checkAll').prop('checked', true);
    });

    // Deselect all button
    $('#deselectAllBtn').click(function() {
        $('.row-checkbox').prop('checked', false);
        $('#checkAll').prop('checked', false);
    });

    // Bulk action button
    $('#applyBulkActionBtn').click(function() {
        var selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            showToast('Pilih minimal satu kostum', 'warning');
            return;
        }

        var action = $('#bulkActionSelect').val();
        if (!action) {
            showToast('Pilih aksi terlebih dahulu', 'warning');
            return;
        }

        var actionText = '';
        switch(action) {
            case 'activate': actionText = 'mengaktifkan'; break;
            case 'deactivate': actionText = 'menonaktifkan'; break;
            case 'feature': actionText = 'menjadikan featured'; break;
            case 'unfeature': actionText = 'menghapus featured dari'; break;
            case 'delete': actionText = 'menghapus'; break;
        }

        $('#bulkConfirmText').text(`Apakah Anda yakin ingin ${actionText} ${selectedIds.length} kostum?`);
        $('#confirmBulkModal').modal('show');
        
        $('#confirmBulkBtn').off('click').on('click', function() {
            $.ajax({
                url: '<?= base_url("admin/kostum-ajax/bulkAction") ?>',
                type: 'POST',
                data: {
                    ids: selectedIds,
                    action: action,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        table.ajax.reload();
                        $('.row-checkbox').prop('checked', false);
                        $('#checkAll').prop('checked', false);
                        $('#bulkActionSelect').val('');
                    } else {
                        showToast(response.message, 'error');
                    }
                    $('#confirmBulkModal').modal('hide');
                },
                error: function(xhr) {
                    showToast('Terjadi kesalahan', 'error');
                    console.error(xhr);
                }
            });
        });
    });

    // Add form submission
    $('#addForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        $.ajax({
            url: '<?= base_url("admin/kostum-ajax/save") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#addForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
                $('#addErrorAlert').addClass('d-none');
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#addForm')[0].reset();
                    $('#addModal').modal('hide');
                    table.ajax.reload();
                } else {
                    var errorHtml = '<strong>Error:</strong><ul class="mb-0">';
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            errorHtml += '<li>' + value + '</li>';
                        });
                    } else {
                        errorHtml += '<li>' + response.message + '</li>';
                    }
                    errorHtml += '</ul>';
                    
                    $('#addErrorAlert').html(errorHtml).removeClass('d-none');
                }
            },
            error: function(xhr) {
                showToast('Terjadi kesalahan server', 'error');
                console.error(xhr);
            },
            complete: function() {
                $('#addForm button[type="submit"]').prop('disabled', false).text('Simpan');
            }
        });
    });

    // Edit button click
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url("admin/kostum-ajax/getKostumDetail/") ?>' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var kostum = response.data;
                    
                    // Fill form fields
                    $('#edit_id').val(kostum.id);
                    $('#edit_nama_kostum').val(kostum.nama_kostum);
                    $('#edit_deskripsi').val(kostum.deskripsi);
                    $('#edit_spesifikasi').val(kostum.spesifikasi_text || '');
                    $('#edit_kategori').val(kostum.kategori);
                    $('#edit_harga_sewa').val(kostum.harga_sewa);
                    $('#edit_durasi_sewa').val(kostum.durasi_sewa);
                    $('#edit_stok').val(kostum.stok);
                    $('#edit_stok_tersedia').val(kostum.stok_tersedia);
                    $('#edit_ukuran').val(kostum.ukuran || '');
                    $('#edit_warna').val(kostum.warna || '');
                    $('#edit_bahan').val(kostum.bahan || '');
                    $('#edit_kondisi').val(kostum.kondisi || 'baik');
                    $('#edit_urutan').val(kostum.urutan || 0);
                    $('#edit_is_active').prop('checked', kostum.is_active == 1);
                    $('#edit_is_featured').prop('checked', kostum.is_featured == 1);
                    
                    // Handle custom duration
                    if (!['3 hari', '24 Jam', '1 Minggu', '2 Minggu', '1 Bulan'].includes(kostum.durasi_sewa)) {
                        $('#edit_durasi_sewa').val('Kustom');
                        $('#edit_durasi_kustom').val(kostum.durasi_sewa).removeClass('d-none');
                    }
                    
                    // Show current image
                    if (kostum.gambar) {
                        $('#editImagePreview').html(`
                            <label class="form-label">Gambar Saat Ini:</label>
                            <img src="<?= base_url('uploads/kostum/') ?>${kostum.gambar}" 
                                 class="img-thumbnail" 
                                 style="max-height: 150px; max-width: 100%;">
                        `);
                    } else {
                        $('#editImagePreview').html('<div class="alert alert-info">Belum ada gambar</div>');
                    }
                    
                    $('#editErrorAlert').addClass('d-none');
                    $('#editModal').modal('show');
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Terjadi kesalahan', 'error');
                console.error(xhr);
            }
        });
    });

// Edit form submission - FIXED VERSION
$('#editForm').submit(function(e) {
    e.preventDefault();
    
    // Debug form data
    var formData = new FormData(this);
    console.log('FormData entries:');
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ', pair[1]);
    }
    
    var id = $('#edit_id').val();
    
    // Kirim dengan Content-Type yang benar
    $.ajax({
        url: '<?= base_url("admin/kostum-ajax/update/") ?>' + id,
        type: 'POST',
        data: formData,
        processData: false, // Penting: jangan proses data
        contentType: false, // Penting: biarkan browser set Content-Type
        dataType: 'json',
        beforeSend: function() {
            $('#editForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
            $('#editErrorAlert').addClass('d-none');
        },
        success: function(response) {
            console.log('Response:', response);
            
            if (response.success) {
                showToast(response.message, 'success');
                $('#editModal').modal('hide');
                table.ajax.reload();
            } else {
                var errorHtml = '<strong>Error:</strong><ul class="mb-0">';
                if (response.errors) {
                    $.each(response.errors, function(key, value) {
                        errorHtml += '<li>' + value + '</li>';
                    });
                } else {
                    errorHtml += '<li>' + response.message + '</li>';
                }
                errorHtml += '</ul>';
                
                $('#editErrorAlert').html(errorHtml).removeClass('d-none');
                
                // Scroll ke error
                $('#editErrorAlert')[0].scrollIntoView({ behavior: 'smooth' });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
            showToast('Terjadi kesalahan server: ' + error, 'error');
            
            // Tampilkan detail error jika ada
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.message) {
                    $('#editErrorAlert').html('<strong>Error:</strong> ' + response.message).removeClass('d-none');
                }
            } catch (e) {
                $('#editErrorAlert').html('<strong>Error:</strong> ' + xhr.responseText.substring(0, 200)).removeClass('d-none');
            }
        },
        complete: function() {
            $('#editForm button[type="submit"]').prop('disabled', false).text('Simpan Perubahan');
        }
    });
});

    // View button click
    $(document).on('click', '.view-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url("admin/kostum-ajax/getKostumDetail/") ?>' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    var kostum = response.data;
                    var html = `
                        <div class="row">
                            <div class="col-md-4 text-center">
                                ${kostum.gambar ? 
                                    `<img src="<?= base_url('uploads/kostum/') ?>${kostum.gambar}" 
                                          class="img-fluid rounded mb-3" 
                                          style="max-height: 200px;">` :
                                    `<div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                                          style="height: 200px;">
                                        <i class="bi bi-image text-muted fs-1"></i>
                                     </div>`
                                }
                                <h4>${kostum.nama_kostum}</h4>
                                <div class="d-flex justify-content-center gap-2 mb-3">
                                    <span class="badge ${kostum.is_active ? 'bg-success' : 'bg-danger'}">
                                        ${kostum.is_active ? 'Aktif' : 'Nonaktif'}
                                    </span>
                                    ${kostum.is_featured ? '<span class="badge bg-warning">Featured</span>' : ''}
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <table class="table table-striped">
                                    <tr>
                                        <th width="30%">Kategori</th>
                                        <td>${$('#edit_kategori option[value="' + kostum.kategori + '"]').text() || kostum.kategori}</td>
                                    </tr>
                                    <tr>
                                        <th>Harga Sewa</th>
                                        <td><strong>Rp ${new Intl.NumberFormat('id-ID').format(kostum.harga_sewa)}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Durasi Sewa</th>
                                        <td>${kostum.durasi_sewa}</td>
                                    </tr>
                                    <tr>
                                        <th>Stok</th>
                                        <td>
                                            <span class="badge ${kostum.stok_tersedia == 0 ? 'bg-danger' : (kostum.stok_tersedia <= 2 ? 'bg-warning' : 'bg-success')}">
                                                ${kostum.stok_tersedia} / ${kostum.stok}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Ukuran</th>
                                        <td>${kostum.ukuran || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Warna</th>
                                        <td>${kostum.warna || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Bahan</th>
                                        <td>${kostum.bahan || '-'}</td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi</th>
                                        <td>${$('#edit_kondisi option[value="' + kostum.kondisi + '"]').text() || kostum.kondisi}</td>
                                    </tr>
                                    <tr>
                                        <th>Dibuat</th>
                                        <td>${new Date(kostum.created_at).toLocaleDateString('id-ID')}</td>
                                    </tr>
                                </table>
                                
                                <h6>Deskripsi:</h6>
                                <p>${kostum.deskripsi || '-'}</p>
                                
                                ${kostum.spesifikasi_text ? `
                                    <h6>Spesifikasi:</h6>
                                    <ul>
                                        ${kostum.spesifikasi_text.split('\n').map(spec => spec.trim() ? `<li>${spec}</li>` : '').join('')}
                                    </ul>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    
                    $('#viewContent').html(html);
                    $('#viewModal').modal('show');
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Terjadi kesalahan', 'error');
                console.error(xhr);
            }
        });
    });

    // Delete button click
    var deleteId;
    $(document).on('click', '.delete-btn', function() {
        deleteId = $(this).data('id');
        $('#confirmDeleteModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteBtn').click(function() {
        $.ajax({
            url: '<?= base_url("admin/kostum-ajax/delete/") ?>' + deleteId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    table.ajax.reload();
                } else {
                    showToast(response.message, 'error');
                }
                $('#confirmDeleteModal').modal('hide');
            },
            error: function(xhr) {
                showToast('Terjadi kesalahan', 'error');
                console.error(xhr);
                $('#confirmDeleteModal').modal('hide');
            }
        });
    });

    // Toggle status
    $(document).on('click', '.toggle-status-btn', function() {
        var id = $(this).data('id');
        var currentStatus = $(this).data('status');
        
        $.ajax({
            url: '<?= base_url("admin/kostum-ajax/toggleStatus/") ?>' + id,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    table.ajax.reload();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Terjadi kesalahan', 'error');
                console.error(xhr);
            }
        });
    });

    // Toggle featured
    $(document).on('click', '.toggle-featured-btn', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url("admin/kostum-ajax/toggleFeatured/") ?>' + id,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    table.ajax.reload();
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                showToast('Terjadi kesalahan', 'error');
                console.error(xhr);
            }
        });
    });

    // Custom duration handling
    $('select[name="durasi_sewa"]').change(function() {
        var customInput = $(this).closest('form').find('input[name="durasi_kustom"]');
        if ($(this).val() === 'Kustom') {
            customInput.removeClass('d-none').attr('required', true);
        } else {
            customInput.addClass('d-none').removeAttr('required').val('');
        }
    });

    // Image preview
    $('input[type="file"][name="gambar"]').change(function(e) {
        var file = e.target.files[0];
        var previewDiv = $(this).siblings('#imagePreview, #editImagePreview');
        
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewDiv.html(`
                    <label class="form-label">Preview:</label>
                    <img src="${e.target.result}" 
                         class="img-thumbnail" 
                         style="max-height: 150px; max-width: 100%;">
                `);
            }
            reader.readAsDataURL(file);
        } else {
            previewDiv.html('');
        }
    });

    // Clear form when modal is hidden
    $('#addModal, #editModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.alert').addClass('d-none');
        $(this).find('#imagePreview, #editImagePreview').html('');
        $('input[name="durasi_kustom"]').addClass('d-none');
    });

    // Toast notification function
    function showToast(message, type = 'info') {
        var bgColor = '';
        switch(type) {
            case 'success': bgColor = 'bg-success'; break;
            case 'error': bgColor = 'bg-danger'; break;
            case 'warning': bgColor = 'bg-warning'; break;
            default: bgColor = 'bg-info';
        }
        
        var toast = `
            <div class="toast align-items-center text-white ${bgColor} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        $('.toast-container').remove();
        $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
        $('.toast-container').html(toast);
        
        var bsToast = new bootstrap.Toast($('.toast'));
        bsToast.show();
        
        setTimeout(function() {
            $('.toast-container').remove();
        }, 3000);
    }
});
</script>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <!-- Toast notifications will appear here -->
</div>
<?= $this->endSection() ?>