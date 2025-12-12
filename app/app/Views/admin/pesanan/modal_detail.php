<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Detail Pesanan #<?= $pesanan['kode_pesaran'] ?></h4>
        <div>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="printInvoice(<?= $pesanan['id'] ?>)">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <a href="<?= base_url('admin/pesanan/detail/' . $pesanan['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-1">
                <i class="bi bi-box-arrow-up-right"></i> Full View
            </a>
        </div>
    </div>

    <!-- Informasi Pesanan -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted">Nama Lengkap</label>
            <p class="fw-bold"><?= $pesanan['nama_lengkap'] ?></p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted">WhatsApp</label>
            <p>
                <a href="https://wa.me/<?= $pesanan['no_whatsapp'] ?>" target="_blank" class="text-success">
                    <i class="bi bi-whatsapp"></i> <?= $pesanan['no_whatsapp'] ?>
                </a>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted">Tanggal Acara</label>
            <p class="fw-bold"><?= date('d F Y', strtotime($pesanan['tanggal_acara'])) ?></p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label text-muted">Status</label>
            <p>
                <?php
                $badgeClass = [
                    'pending' => 'bg-warning',
                    'dikonfirmasi' => 'bg-info',
                    'diproses' => 'bg-primary',
                    'selesai' => 'bg-success',
                    'dibatalkan' => 'bg-danger'
                ][$pesanan['status']] ?? 'bg-secondary';
                ?>
                <span class="badge <?= $badgeClass ?> fs-6">
                    <?= ucfirst($pesanan['status']) ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Summary Pembayaran -->
    <div class="card border mb-3">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Ringkasan Pembayaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <small class="text-muted">Total Tagihan</small>
                    <h5 class="mb-0">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></h5>
                </div>
                <div class="col-md-6 mb-2">
                    <small class="text-muted">Total Dibayar</small>
                    <h5 class="mb-0 text-success">Rp <span id="total-dibayar"><?= number_format($pesanan['dp_dibayar'], 0, ',', '.') ?></span></h5>
                </div>
            </div>
            <hr class="my-2">
            <div class="row">
                <div class="col-12">
                    <small class="text-muted">Sisa Pembayaran</small>
                    <h4 class="mb-0" id="sisa-pembayaran">
                        Rp <?= number_format($pesanan['total_harga'] - $pesanan['dp_dibayar'], 0, ',', '.') ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Tambah Pembayaran -->
    <div class="card border mb-3">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Tambah Pembayaran</h6>
        </div>
        <div class="card-body">
            <form id="form-tambah-pembayaran" data-pesanan-id="<?= $pesanan['id'] ?>">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="number" name="jumlah" class="form-control form-control-sm" placeholder="Jumlah" required>
                    </div>
                    <div class="col-md-3">
                        <select name="metode" class="form-select form-select-sm" required>
                            <option value="">Metode</option>
                            <option value="transfer">Transfer</option>
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="jenis_pembayaran" class="form-select form-select-sm" required>
                            <option value="dp">DP</option>
                            <option value="pelunasan">Pelunasan</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-plus"></i> Tambah
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="card border">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0">Riwayat Pembayaran</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" id="riwayat-pembayaran">
                    <thead>
                        <tr>
                            <th width="30">#</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat via AJAX -->
                        <tr>
                            <td colspan="7" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                <span class="ms-2">Memuat riwayat...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load payment history
    loadPaymentHistory(<?= $pesanan['id'] ?>);
    
    // Form tambah pembayaran
    $('#form-tambah-pembayaran').on('submit', function(e) {
        e.preventDefault();
        tambahPembayaran(<?= $pesanan['id'] ?>);
    });
});

function loadPaymentHistory(pesananId) {
    $.ajax({
        url: '<?= base_url("admin/pesanan/getPaymentHistory/") ?>' + pesananId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                let html = '';
                let totalDibayar = 0;
                
                if (response.data.length > 0) {
                    response.data.forEach(function(item, index) {
                        totalDibayar += parseFloat(item.jumlah);
                        
                        // Status badge
                        let statusBadge = '';
                        switch(item.status) {
                            case 'diterima':
                                statusBadge = '<span class="badge bg-success">Diterima</span>';
                                break;
                            case 'pending':
                                statusBadge = '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 'ditolak':
                                statusBadge = '<span class="badge bg-danger">Ditolak</span>';
                                break;
                            default:
                                statusBadge = '<span class="badge bg-secondary">' + item.status + '</span>';
                        }
                        
                        // Action buttons
                        let actions = '';
                        if (item.status === 'pending') {
                            actions = `
                                <button class="btn btn-sm btn-success" onclick="verifyPayment(${item.id}, 'diterima')">
                                    <i class="bi bi-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="verifyPayment(${item.id}, 'ditolak')">
                                    <i class="bi bi-x"></i>
                                </button>
                            `;
                        } else if (item.bukti) {
                            actions = `<a href="<?= base_url('uploads/pembayaran/') ?>${item.bukti}" target="_blank" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>`;
                        }
                        
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><small>${item.kode_pembayaran}</small></td>
                                <td><small>${item.tanggal_pembayaran}</small></td>
                                <td>Rp ${item.jumlah.toLocaleString('id-ID')}</td>
                                <td><span class="badge bg-secondary">${item.metode}</span></td>
                                <td>${statusBadge}</td>
                                <td>${actions}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `<tr><td colspan="7" class="text-center py-3">Belum ada riwayat pembayaran</td></tr>`;
                }
                
                $('#riwayat-pembayaran tbody').html(html);
                
                // Update total dibayar
                $('#total-dibayar').text(totalDibayar.toLocaleString('id-ID'));
                
                // Update sisa pembayaran
                const totalTagihan = <?= $pesanan['total_harga'] ?>;
                const sisa = totalTagihan - totalDibayar;
                $('#sisa-pembayaran').text('Rp ' + sisa.toLocaleString('id-ID'));
                
            }
        },
        error: function() {
            $('#riwayat-pembayaran tbody').html(`
                <tr><td colspan="7" class="text-center text-danger py-3">Gagal memuat riwayat</td></tr>
            `);
        }
    });
}

function tambahPembayaran(pesananId) {
    const formData = new FormData(document.getElementById('form-tambah-pembayaran'));
    formData.append('pesanan_id', pesananId);
    
    $.ajax({
        url: '<?= base_url("admin/pesanan/addPayment/") ?>' + pesananId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pembayaran berhasil ditambahkan',
                    timer: 2000
                });
                loadPaymentHistory(pesananId);
                $('#form-tambah-pembayaran')[0].reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: response.message || 'Terjadi kesalahan'
                });
            }
        }
    });
}

function verifyPayment(paymentId, status) {
    Swal.fire({
        title: 'Verifikasi Pembayaran',
        text: `Apakah Anda yakin ingin ${status === 'diterima' ? 'menerima' : 'menolak'} pembayaran ini?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url("admin/pesanan/verifyPayment/") ?>' + paymentId,
                type: 'POST',
                data: { status: status },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000
                        });
                        loadPaymentHistory(<?= $pesanan['id'] ?>);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message
                        });
                    }
                }
            });
        }
    });
}

function printInvoice(pesananId) {
    window.open('<?= base_url("admin/pesanan/printInvoice/") ?>' + pesananId, '_blank');
}
</script>