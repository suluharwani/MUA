<?= $this->extend('admin/layout/modal_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header dengan tombol cetak -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Pesanan #<?= $pesanan['kode_pesanan'] ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pesanan') ?>">Pesanan</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" onclick="printInvoice()">
                <i class="bi bi-printer"></i> Cetak Invoice
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="generatePDF()">
                <i class="bi bi-file-pdf"></i> PDF
            </button>
            <a href="<?= base_url('admin/pesanan') ?>" class="btn btn-outline-dark">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Informasi Pesanan -->
        <div class="col-lg-8">
            <!-- Informasi Pesanan -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kode Pesanan</label>
                            <p class="fw-bold h5"><?= $pesanan['kode_pesanan'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Pesanan</label>
                            <p class="fw-bold"><?= date('d F Y H:i', strtotime($pesanan['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nama Pelanggan</label>
                            <p class="fw-bold h5"><?= $pesanan['nama_lengkap'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kontak</label>
                            <p>
                                <a href="https://wa.me/<?= $pesanan['no_whatsapp'] ?>" target="_blank" class="btn btn-sm btn-success me-2">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </a>
                                <?php if(!empty($pesanan['email'])): ?>
                                <a href="mailto:<?= $pesanan['email'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-envelope"></i> Email
                                </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Acara</label>
                            <div class="alert alert-primary py-2">
                                <i class="bi bi-calendar-event"></i>
                                <strong><?= date('d F Y', strtotime($pesanan['tanggal_acara'])) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status Pesanan</label>
                            <div>
                                <?php
                                $badgeClass = [
                                    'pending' => 'bg-warning',
                                    'dikonfirmasi' => 'bg-info',
                                    'diproses' => 'bg-primary',
                                    'selesai' => 'bg-success',
                                    'dibatalkan' => 'bg-danger'
                                ][$pesanan['status']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgeClass ?> fs-6 px-3 py-2">
                                    <?= ucfirst($pesanan['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Lokasi Acara</label>
                            <div class="card border">
                                <div class="card-body">
                                    <i class="bi bi-geo-alt"></i> <?= $pesanan['lokasi_acara'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(!empty($pesanan['informasi_tambahan'])): ?>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Informasi Tambahan</label>
                            <div class="card border">
                                <div class="card-body">
                                    <?= nl2br($pesanan['informasi_tambahan']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Layanan -->
            <div class="row">
                <?php if(!empty($pesanan['paket_id'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-palette"></i> Paket Makeup</h6>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= !empty($pesanan['paket_nama']) ? $pesanan['paket_nama'] : 'Paket Tidak Tersedia' ?></h5>
                            <p class="card-text"><?= !empty($pesanan['paket_deskripsi']) ? $pesanan['paket_deskripsi'] : '-' ?></p>
                            <div class="mt-3">
                                <span class="badge bg-primary fs-6">Rp <?= !empty($pesanan['paket_harga']) ? number_format($pesanan['paket_harga'], 0, ',', '.') : '0' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($pesanan['kostum_id'])): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-person-badge"></i> Kostum</h6>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= !empty($pesanan['kostum_nama']) ? $pesanan['kostum_nama'] : 'Kostum Tidak Tersedia' ?></h5>
                            <p class="card-text"><?= !empty($pesanan['kostum_deskripsi']) ? $pesanan['kostum_deskripsi'] : '-' ?></p>
                            <div class="mt-3">
                                <span class="badge bg-primary fs-6">Rp <?= !empty($pesanan['kostum_harga']) ? number_format($pesanan['kostum_harga'], 0, ',', '.') : '0' ?></span>
                                <?php if(!empty($pesanan['kostum_kategori'])): ?>
                                <span class="badge bg-secondary ms-1"><?= $pesanan['kostum_kategori'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Pembayaran & Aksi -->
        <div class="col-lg-4">
            <!-- Ringkasan Pembayaran -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Ringkasan Pembayaran</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($paymentSummary)): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Tagihan</span>
                            <strong class="fs-5">Rp <?= number_format($paymentSummary['total_harga'], 0, ',', '.') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Dibayar</span>
                            <strong class="fs-5 text-success">Rp <?= number_format($paymentSummary['total_dibayar'], 0, ',', '.') ?></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Sisa Pembayaran</span>
                            <strong class="fs-4 <?= $paymentSummary['sisa_pembayaran'] > 0 ? 'text-danger' : 'text-success' ?>">
                                Rp <?= number_format($paymentSummary['sisa_pembayaran'], 0, ',', '.') ?>
                            </strong>
                        </div>
                        
                        <div class="progress mb-3" style="height: 20px;">
                            <?php 
                            $percentage = $paymentSummary['total_harga'] > 0 ? 
                                ($paymentSummary['total_dibayar'] / $paymentSummary['total_harga']) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= $percentage ?>%" 
                                 aria-valuenow="<?= $percentage ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?= round($percentage, 1) ?>%
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i> 
                                DP Minimum: <?= $paymentSummary['dp_percentage'] ?>% 
                                (Rp <?= number_format($paymentSummary['dp_minimum'], 0, ',', '.') ?>)
                            </small>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-cash-coin fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">Data pembayaran tidak tersedia</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Tambah Pembayaran -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form id="form-pembayaran">
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" class="form-control" 
                                   placeholder="Masukkan jumlah" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Metode</label>
                                <select name="metode" class="form-select" required>
                                    <option value="transfer">Transfer</option>
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Jenis</label>
                                <select name="jenis_pembayaran" class="form-select" required>
                                    <option value="dp">DP</option>
                                    <option value="pelunasan">Pelunasan</option>
                                    <option value="lunas">Lunas</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bukti Pembayaran (opsional)</label>
                            <input type="file" name="bukti" class="form-control" accept="image/*,.pdf">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pembayaran..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Simpan Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            <!-- Aksi Cepat -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <!-- Form Ubah Status -->
                        <form method="post" action="<?= base_url('admin/pesanan/updateStatus') ?>" class="mb-3">
                            <input type="hidden" name="pesanan_id" value="<?= $pesanan['id'] ?>">
                            <div class="input-group">
                                <select name="status" class="form-select">
                                    <option value="pending" <?= $pesanan['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="dikonfirmasi" <?= $pesanan['status'] == 'dikonfirmasi' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                    <option value="diproses" <?= $pesanan['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="selesai" <?= $pesanan['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="dibatalkan" <?= $pesanan['status'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                </select>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-save"></i>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Tombol WhatsApp -->
                        <a href="https://wa.me/<?= $pesanan['no_whatsapp'] ?>?text=Halo%20<?= urlencode($pesanan['nama_lengkap']) ?>%2C%20saya%20dari%20Maulia%20Wedding.%20Mengenai%20pesanan%20Anda%20dengan%20kode%20<?= $pesanan['kode_pesanan'] ?>%20..." 
                           target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp"></i> Chat WhatsApp
                        </a>
                        
                        <!-- Tombol Edit -->
                        <a href="javascript:void(0)" onclick="loadEditForm(<?= $pesanan['id'] ?>)" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Pembayaran</h5>
                    <?php if(isset($paymentSummary) && isset($paymentSummary['riwayat'])): ?>
                    <span class="badge bg-primary"><?= count($paymentSummary['riwayat']) ?> Transaksi</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if(isset($paymentSummary) && !empty($paymentSummary['riwayat'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Kode Pembayaran</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Metode</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Diverifikasi Oleh</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach($paymentSummary['riwayat'] as $payment): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= $payment['kode_pembayaran'] ?></code></td>
                                    <td><?= date('d/m/Y', strtotime($payment['tanggal_pembayaran'])) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= ucfirst($payment['jenis_pembayaran']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= ucfirst($payment['metode']) ?></span>
                                    </td>
                                    <td class="fw-bold">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $statusBadge = [
                                            'pending' => 'warning',
                                            'diterima' => 'success',
                                            'ditolak' => 'danger',
                                            'dikembalikan' => 'secondary'
                                        ][$payment['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusBadge ?>">
                                            <?= ucfirst($payment['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= !empty($payment['verifikator_nama']) ? $payment['verifikator_nama'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td>
                                        <small><?= !empty($payment['catatan']) ? $payment['catatan'] : '-' ?></small>
                                    </td>
                                    <td>
                                        <?php if(!empty($payment['bukti'])): ?>
                                        <a href="<?= base_url('uploads/pembayaran/' . $payment['bukti']) ?>" 
                                           target="_blank" class="btn btn-sm btn-info" title="Lihat Bukti">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if($payment['status'] == 'pending'): ?>
                                        <button class="btn btn-sm btn-success" 
                                                onclick="verifyPayment(<?= $payment['id'] ?>, 'diterima')"
                                                title="Terima">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="verifyPayment(<?= $payment['id'] ?>, 'ditolak')"
                                                title="Tolak">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-cash-coin fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">Belum ada riwayat pembayaran</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
                <textarea id="confirmCatatan" class="form-control mt-2" placeholder="Catatan (opsional)" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Form tambah pembayaran
    $('#form-pembayaran').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('pesanan_id', <?= $pesanan['id'] ?>);
        formData.append('tanggal_pembayaran', new Date().toISOString().split('T')[0]);
        
        $.ajax({
            url: '<?= base_url("admin/pesanan/addPayment/" . $pesanan['id']) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('#form-pembayaran button[type="submit"]').prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.errors ? Object.values(response.errors).join('\n') : response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan pembayaran'
                });
            },
            complete: function() {
                $('#form-pembayaran button[type="submit"]').prop('disabled', false)
                    .html('<i class="bi bi-check-circle"></i> Simpan Pembayaran');
            }
        });
    });
});

let currentPaymentId = null;
let currentAction = null;

function verifyPayment(paymentId, action) {
    currentPaymentId = paymentId;
    currentAction = action;
    
    const messages = {
        'diterima': 'menerima pembayaran ini?',
        'ditolak': 'menolak pembayaran ini?',
        'dikembalikan': 'mengembalikan pembayaran ini?'
    };
    
    $('#confirmMessage').text(`Apakah Anda yakin ingin ${messages[action]}`);
    $('#confirmCatatan').val('');
    $('#confirmModal').modal('show');
}

$('#confirmButton').on('click', function() {
    if (!currentPaymentId || !currentAction) return;
    
    $.ajax({
        url: '<?= base_url("admin/pesanan/verifyPayment/") ?>' + currentPaymentId,
        type: 'POST',
        data: {
            status: currentAction,
            catatan: $('#confirmCatatan').val()
        },
        dataType: 'json',
        beforeSend: function() {
            $('#confirmButton').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm"></span> Memproses...');
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
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
                text: 'Terjadi kesalahan saat memverifikasi pembayaran'
            });
        },
        complete: function() {
            $('#confirmButton').prop('disabled', false).text('Konfirmasi');
            $('#confirmModal').modal('hide');
        }
    });
});

function printInvoice() {
    window.open('<?= base_url("admin/pesanan/printInvoice/" . $pesanan['id']) ?>', '_blank');
}

function generatePDF() {
    window.open('<?= base_url("admin/pesanan/generatePdf/" . $pesanan['id']) ?>', '_blank');
}

function loadEditForm(id) {
    // Load modal edit form dari halaman index
    $.ajax({
        url: '<?= base_url("admin/pesanan/create") ?>',
        type: 'GET',
        success: function(response) {
            $('#create-content').html(response);
            
            // Load order data
            $.ajax({
                url: '<?= base_url("admin/pesanan/getOrderForEdit") ?>/' + id,
                type: 'GET',
                success: function(orderResponse) {
                    if (orderResponse.success && orderResponse.data) {
                        // Panggil fungsi populateEditForm dari create_modal.php
                        if (typeof populateEditForm === 'function') {
                            populateEditForm(orderResponse.data);
                        }
                    }
                }
            });
            
            $('#createModal').modal('show');
        }
    });
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>