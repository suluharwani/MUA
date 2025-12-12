<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/pesanan/create') ?>" class="btn btn-primary me-2" id="btn-create">
                <i class="bi bi-plus-circle"></i> Buat Pesanan
            </a>
            <a href="<?= base_url('admin/pesanan/calendar') ?>" class="btn btn-outline-primary me-2">
                <i class="bi bi-calendar-week"></i> Kalendar
            </a>
            <button type="button" class="btn btn-success" onclick="exportExcel()">
                <i class="bi bi-file-earmark-excel"></i> Export
            </button>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row mb-4" id="stats-container">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Total Pesanan</div>
                            <div class="fw-bold fs-4" id="stat-total">0</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-cart fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Pending</div>
                            <div class="fw-bold fs-4" id="stat-pending">0</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-clock fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Dikonfirmasi</div>
                            <div class="fw-bold fs-4" id="stat-dikonfirmasi">0</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Diproses</div>
                            <div class="fw-bold fs-4" id="stat-diproses">0</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-gear fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Selesai</div>
                            <div class="fw-bold fs-4" id="stat-selesai">0</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-muted fw-semibold">Dibatalkan</div>
                            <div class="fw-bold fs-4" id="stat-dibatalkan">0</div>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" id="search-input" class="form-control" placeholder="Cari kode/nama/telepon...">
                </div>
                <div class="col-md-2">
                    <select id="status-filter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="dikonfirmasi">Dikonfirmasi</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" id="date-filter" class="form-control">
                </div>
                <div class="col-md-2">
                    <select id="layanan-filter" class="form-select">
                        <option value="">Semua Layanan</option>
                        <option value="makeup">Makeup</option>
                        <option value="kostum">Kostum</option>
                        <option value="keduanya">Keduanya</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="button" id="filter-btn" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <button type="button" id="reset-btn" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="pesanan-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>WhatsApp</th>
                            <th>Layanan</th>
                            <th>Tanggal Acara</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal Pesan</th>
                            <th width="120">Aksi</th>
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

<!-- Modal Create/Edit -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Buat Pesanan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="create-content">
                <!-- Konten akan dimuat via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detail-content">
                <!-- Konten akan dimuat via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Ubah Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="status-form">
                    <input type="hidden" id="pesanan-id-status" name="pesanan_id">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status-select" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="dikonfirmasi">Dikonfirmasi</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan-status" name="catatan" rows="3"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Debug: Log saat DataTables diinisialisasi
    console.log('Initializing DataTables...');
    
    // Inisialisasi DataTables dengan konfigurasi yang lebih sederhana
    const table = $('#pesanan-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url("admin/pesanan/getData") ?>',
            type: 'POST',
            data: function(d) {
                // Format data untuk server
                return {
                    draw: d.draw,
                    start: d.start,
                    length: d.length,
                    search: {
                        value: d.search.value
                    },
                    status: $('#status-filter').val(),
                    date: $('#date-filter').val(),
                    layanan: $('#layanan-filter').val()
                };
            },
            dataSrc: function(json) {
                // Debug: Log response dari server
                console.log('DataTables response:', json);
                
                if (json.error) {
                    console.error('DataTables error:', json.error);
                    // Tampilkan pesan error di tabel
                    $('#pesanan-table tbody').html(`
                        <tr>
                            <td colspan="10" class="text-center text-danger">
                                <i class="bi bi-exclamation-triangle"></i> 
                                ${json.error}
                            </td>
                        </tr>
                    `);
                    return [];
                }
                
                // Pastikan data ada
                if (!json.data) {
                    console.warn('No data in response');
                    return [];
                }
                
                return json.data;
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                
                // Tampilkan pesan error
                $('#pesanan-table tbody').html(`
                    <tr>
                        <td colspan="10" class="text-center text-danger">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Gagal memuat data. Silakan refresh halaman.
                            <br><small>${thrown}</small>
                        </td>
                    </tr>
                `);
            }
        },
        columns: [
            { 
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                width: '50px'
            },
            { 
                data: 'kode_pesanan',
                name: 'kode_pesanan',
                render: function(data, type, row) {
                    return data ? `<strong>${data}</strong>` : '-';
                }
            },
            { 
                data: 'nama_lengkap',
                name: 'nama_lengkap',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'no_whatsapp',
                name: 'no_whatsapp',
                render: function(data, type, row) {
                    if (!data) return '-';
                    const cleanNumber = data.replace(/\D/g, '');
                    return `<a href="https://wa.me/${cleanNumber}" target="_blank" class="text-success">
                                <i class="bi bi-whatsapp"></i> ${data}
                            </a>`;
                }
            },
            { 
                data: 'jenis_layanan',
                name: 'jenis_layanan',
                render: function(data, type, row) {
                    if (!data) return '-';
                    
                    const badgeClass = {
                        'Makeup': 'bg-info',
                        'Kostum': 'bg-warning',
                        'Keduanya': 'bg-primary'
                    }[data] || 'bg-secondary';
                    
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'tanggal_acara',
                name: 'tanggal_acara',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'total_harga',
                name: 'total_harga',
                render: function(data, type, row) {
                    if (!data && data !== 0) return '-';
                    return `Rp ${new Intl.NumberFormat('id-ID').format(data)}`;
                }
            },
            { 
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    if (!data) return '-';
                    
                    // Gunakan class dari server atau tentukan di sini
                    const badgeClass = row.status_class || 
                        (data === 'Pending' ? 'bg-warning' :
                         data === 'Dikonfirmasi' ? 'bg-info' :
                         data === 'Diproses' ? 'bg-primary' :
                         data === 'Selesai' ? 'bg-success' :
                         data === 'Dibatalkan' ? 'bg-danger' : 'bg-secondary');
                    
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { 
                data: 'created_at',
                name: 'created_at',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    const id = row.id || data;
                    const kode = row.kode_pesanan || '';
                    
                    return `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-detail" data-id="${id}" title="Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-info btn-edit" data-id="${id}" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-delete" data-id="${id}" data-kode="${kode}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[8, 'desc']], // Urutkan berdasarkan created_at descending
        language: {
            processing: "<div class='spinner-border spinner-border-sm' role='status'></div> Memproses...",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            zeroRecords: "Tidak ada data yang ditemukan",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            search: "Cari:",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        initComplete: function() {
            console.log('DataTables initialized successfully');
        },
        drawCallback: function() {
            console.log('DataTables draw complete');
        }
    });

    // Debug: Test DataTables
    console.log('DataTables instance:', table);

    // Load stats
    loadStats();

    // Event listeners untuk filter
    $('#filter-btn').on('click', function() {
        console.log('Filter button clicked');
        table.ajax.reload();
        loadStats();
    });

    $('#reset-btn').on('click', function() {
        console.log('Reset button clicked');
        $('#search-input').val('');
        $('#status-filter').val('');
        $('#date-filter').val('');
        $('#layanan-filter').val('');
        table.search('').draw();
        loadStats();
    });

    // Search input dengan debounce
    let searchTimeout;
    $('#search-input').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            table.search($('#search-input').val()).draw();
            loadStats();
        }, 500);
    });

    // Create button
    $('#btn-create').on('click', function(e) {
        e.preventDefault();
        loadCreateForm();
    });

    // Edit button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        loadEditForm(id);
    });

    // Detail button
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        loadDetail(id);
    });

    // Delete button
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const kode = $(this).data('kode');
        
        Swal.fire({
            title: 'Hapus Pesanan?',
            text: `Apakah Anda yakin ingin menghapus pesanan ${kode ? kode : 'ini'}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deletePesanan(id);
            }
        });
    });

    // Status form
    $('#status-form').on('submit', function(e) {
        e.preventDefault();
        updateStatus();
    });

    // Auto refresh stats every 30 seconds
    setInterval(loadStats, 30000);
});

// Load stats
function loadStats() {
    const filterData = {
        search: $('#search-input').val(),
        status: $('#status-filter').val(),
        date: $('#date-filter').val(),
        layanan: $('#layanan-filter').val()
    };

    $.ajax({
        url: '<?= base_url("admin/pesanan/getStats") ?>',
        type: 'POST',
        data: filterData,
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                $('#stat-total').text(response.data.total || 0);
                $('#stat-pending').text(response.data.pending || 0);
                $('#stat-dikonfirmasi').text(response.data.dikonfirmasi || 0);
                $('#stat-diproses').text(response.data.diproses || 0);
                $('#stat-selesai').text(response.data.selesai || 0);
                $('#stat-dibatalkan').text(response.data.dibatalkan || 0);
            }
        },
        error: function() {
            console.error('Failed to load stats');
        }
    });
}

// Load create form
function loadCreateForm() {
    $.ajax({
        url: '<?= base_url("admin/pesanan/create") ?>',
        type: 'GET',
        beforeSend: function() {
            $('#create-content').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Memuat form...</p>
                </div>
            `);
        },
        success: function(response) {
            $('#create-content').html(response);
            $('#createModal').modal('show');
            initCreateForm();
        },
        error: function() {
            $('#create-content').html(`
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    <p class="mt-2">Gagal memuat form</p>
                </div>
            `);
        }
    });
}

// Load edit form
function loadEditForm(id) {
    $.ajax({
        url: '<?= base_url("admin/pesanan/create") ?>',
        type: 'GET',
        beforeSend: function() {
            $('#create-content').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `);
        },
        success: function(response) {
            $('#create-content').html(response);
            
            // Load order data
            $.ajax({
                url: '<?= base_url("admin/pesanan/getOrderForEdit") ?>/' + id,
                type: 'GET',
                success: function(orderResponse) {
                    if (orderResponse.success && orderResponse.data) {
                        populateEditForm(orderResponse.data);
                    }
                }
            });
            
            $('#createModal').modal('show');
            initCreateForm();
        },
        error: function() {
            $('#create-content').html(`
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    <p class="mt-2">Gagal memuat form</p>
                </div>
            `);
        }
    });
}

// Load detail
function loadDetail(id) {
    $.ajax({
        url: '<?= base_url("admin/pesanan/getDetail") ?>/' + id,
        type: 'GET',
        beforeSend: function() {
            $('#detail-content').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            `);
        },
        success: function(response) {
            $('#detail-content').html(response);
            $('#detailModal').modal('show');
        },
        error: function() {
            $('#detail-content').html(`
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    <p class="mt-2">Gagal memuat data</p>
                </div>
            `);
        }
    });
}

// Delete pesanan
function deletePesanan(id) {
    $.ajax({
        url: '<?= base_url("admin/pesanan/delete") ?>/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                $('#pesanan-table').DataTable().draw(false);
                loadStats();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus'
            });
        }
    });
}

// Update status
function updateStatus() {
    const formData = $('#status-form').serialize();
    
    $.ajax({
        url: '<?= base_url("admin/pesanan/updateStatus") ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#status-form button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                
                $('#pesanan-table').DataTable().draw(false);
                $('#statusModal').modal('hide');
                loadStats();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat mengubah status'
            });
        },
        complete: function() {
            $('#status-form button[type="submit"]').prop('disabled', false).text('Simpan');
        }
    });
}

// Export Excel
function exportExcel() {
    const search = $('#search-input').val();
    const status = $('#status-filter').val();
    const date = $('#date-filter').val();
    const layanan = $('#layanan-filter').val();
    
    let url = '<?= base_url("admin/pesanan/export/excel") ?>?';
    if (search) url += 'search=' + encodeURIComponent(search) + '&';
    if (status) url += 'status=' + encodeURIComponent(status) + '&';
    if (date) url += 'date=' + encodeURIComponent(date) + '&';
    if (layanan) url += 'layanan=' + encodeURIComponent(layanan);
    
    window.location.href = url;
}

// Initialize create form (this function should be in the create.php view)
function initCreateForm() {
    // This will be initialized in the create.php view
    // The actual implementation is in the create.php file
}

// Populate edit form (this function should be in the create.php view)
function populateEditForm(data) {
    // This will be implemented in the create.php view
    // The actual implementation is in the create.php file
}
</script>
<?= $this->endSection() ?>