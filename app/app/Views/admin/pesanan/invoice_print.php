<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $pesanan['kode_pesanan'] ?> - Maulia Wedding</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12pt; }
            .container { width: 100% !important; max-width: none !important; }
            .card { border: 1px solid #000 !important; }
            .badge { border: 1px solid #000 !important; }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .invoice-header { border-bottom: 3px solid #d4b8a3; }
        .logo-text { 
            color: #d4b8a3; 
            font-weight: 700;
            letter-spacing: 2px;
        }
        .total-box { background-color: #f8f9fa; border-left: 4px solid #d4b8a3; }
        .signature-area { margin-top: 100px; border-top: 1px dashed #ccc; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container py-4 print-area">
        <!-- Header Invoice -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="logo-text">MAULIA WEDDING</h1>
                <p class="text-muted mb-0">Professional Wedding Make Up Artist & Kostum Sewa</p>
                <p class="text-muted mb-0">
                    <i class="bi bi-geo-alt"></i> Desa Klambu, Kecamatan Klambu, Kabupaten Grobogan, Jawa Tengah
                </p>
                <p class="text-muted">
                    <i class="bi bi-telephone"></i> 087731310979 | 
                    <i class="bi bi-whatsapp"></i> 6287731310979
                </p>
            </div>
            <div class="col-md-6 text-end">
                <h2 class="text-primary">INVOICE</h2>
                <h3 class="text-muted">#<?= $pesanan['kode_pesanan'] ?></h3>
                <p class="mb-1">
                    <strong>Tanggal:</strong> <?= date('d F Y', strtotime($pesanan['created_at'])) ?>
                </p>
                <p class="mb-0">
                    <strong>Jatuh Tempo:</strong> <?= date('d F Y', strtotime('+7 days', strtotime($pesanan['created_at']))) ?>
                </p>
            </div>
        </div>

        <!-- Informasi Pelanggan -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0">
                    <div class="card-header bg-light">
                        <strong>Kepada:</strong>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $pesanan['nama_lengkap'] ?></h5>
                        <p class="card-text mb-1">
                            <i class="bi bi-whatsapp text-success"></i> <?= $pesanan['no_whatsapp'] ?>
                        </p>
                        <?php if($pesanan['email']): ?>
                        <p class="card-text mb-1">
                            <i class="bi bi-envelope"></i> <?= $pesanan['email'] ?>
                        </p>
                        <?php endif; ?>
                        <p class="card-text mb-0">
                            <i class="bi bi-calendar-event"></i> 
                            <strong>Tanggal Acara:</strong> <?= date('d F Y', strtotime($pesanan['tanggal_acara'])) ?>
                        </p>
                        <p class="card-text">
                            <i class="bi bi-geo-alt"></i> <?= $pesanan['lokasi_acara'] ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0">
                    <div class="card-header bg-light">
                        <strong>Status:</strong>
                    </div>
                    <div class="card-body">
                        <?php
                        $badgeClass = [
                            'pending' => 'bg-warning',
                            'dikonfirmasi' => 'bg-info',
                            'diproses' => 'bg-primary',
                            'selesai' => 'bg-success',
                            'dibatalkan' => 'bg-danger'
                        ][$pesanan['status']] ?? 'bg-secondary';
                        ?>
                        <span class="badge <?= $badgeClass ?> fs-6 mb-3">
                            <?= strtoupper($pesanan['status']) ?>
                        </span>
                        
                        <?php if($paymentSummary): ?>
                        <div class="mt-2">
                            <strong>Status Pembayaran:</strong>
                            <span class="badge <?= $paymentSummary['status_pembayaran'] == 'lunas' ? 'bg-success' : 'bg-warning' ?>">
                                <?= strtoupper($paymentSummary['status_pembayaran']) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Layanan -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border">
                    <div class="card-header bg-light">
                        <strong>Rincian Layanan</strong>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Deskripsi</th>
                                    <th width="150" class="text-end">Harga Satuan</th>
                                    <th width="100" class="text-end">Jumlah</th>
                                    <th width="150" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $itemNumber = 1; ?>
                                
                                <?php if($pesanan['paket_id']): ?>
                                <tr>
                                    <td><?= $itemNumber++ ?></td>
                                    <td>
                                        <strong>Paket Makeup:</strong> <?= $pesanan['paket_nama'] ?><br>
                                        <small class="text-muted"><?= $pesanan['paket_deskripsi'] ?></small>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($pesanan['paket_harga'], 0, ',', '.') ?></td>
                                    <td class="text-end">1</td>
                                    <td class="text-end">Rp <?= number_format($pesanan['paket_harga'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if($pesanan['kostum_id']): ?>
                                <tr>
                                    <td><?= $itemNumber++ ?></td>
                                    <td>
                                        <strong>Kostum:</strong> <?= $pesanan['kostum_nama'] ?><br>
                                        <small class="text-muted">Kategori: <?= $pesanan['kostum_kategori'] ?></small>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($pesanan['kostum_harga'], 0, ',', '.') ?></td>
                                    <td class="text-end"><?= $pesanan['lama_sewa'] ?? 1 ?> hari</td>
                                    <td class="text-end">Rp <?= number_format($pesanan['kostum_harga'] * ($pesanan['lama_sewa'] ?? 1), 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if($pesanan['biaya_transport'] > 0): ?>
                                <tr>
                                    <td><?= $itemNumber++ ?></td>
                                    <td>
                                        <strong>Biaya Transport</strong><br>
                                        <small class="text-muted">Area: <?= $pesanan['nama_area'] ?? '-' ?></small>
                                    </td>
                                    <td class="text-end">Rp <?= number_format($pesanan['biaya_transport'], 0, ',', '.') ?></td>
                                    <td class="text-end">1</td>
                                    <td class="text-end">Rp <?= number_format($pesanan['biaya_transport'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if($pesanan['diskon_nominal'] > 0): ?>
                                <tr class="table-danger">
                                    <td><?= $itemNumber++ ?></td>
                                    <td colspan="3">
                                        <strong>Diskon</strong>
                                        <?php if($pesanan['diskon_persen'] > 0): ?>
                                        <small class="text-muted">(<?= $pesanan['diskon_persen'] ?>%)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">- Rp <?= number_format($pesanan['diskon_nominal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                                
                                <?php if($pesanan['pajak_nominal'] > 0): ?>
                                <tr class="table-info">
                                    <td><?= $itemNumber++ ?></td>
                                    <td colspan="3"><strong>Pajak</strong></td>
                                    <td class="text-end">+ Rp <?= number_format($pesanan['pajak_nominal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="row justify-content-end">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 total-box">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($pesanan['subtotal'], 0, ',', '.') ?></span>
                        </div>
                        
                        <?php if($pesanan['diskon_nominal'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span>Diskon</span>
                            <span>- Rp <?= number_format($pesanan['diskon_nominal'], 0, ',', '.') ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($pesanan['pajak_nominal'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pajak</span>
                            <span>+ Rp <?= number_format($pesanan['pajak_nominal'], 0, ',', '.') ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong class="fs-5">TOTAL</strong>
                            <strong class="fs-5">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></strong>
                        </div>
                        
                        <?php if($paymentSummary): ?>
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between">
                                <span>Total Dibayar:</span>
                                <strong class="text-success">Rp <?= number_format($paymentSummary['total_dibayar'], 0, ',', '.') ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Sisa Pembayaran:</span>
                                <strong class="text-danger">Rp <?= number_format($paymentSummary['sisa_pembayaran'], 0, ',', '.') ?></strong>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        <?php if(!empty($paymentSummary['riwayat'])): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border">
                    <div class="card-header bg-light">
                        <strong>Riwayat Pembayaran</strong>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Metode</th>
                                    <th>Jenis</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach($paymentSummary['riwayat'] as $payment): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($payment['tanggal_pembayaran'])) ?></td>
                                    <td><code><?= $payment['kode_pembayaran'] ?></code></td>
                                    <td><?= ucfirst($payment['metode']) ?></td>
                                    <td><?= ucfirst($payment['jenis_pembayaran']) ?></td>
                                    <td class="text-end">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $payment['status'] == 'diterima' ? 'success' : ($payment['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($payment['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Informasi Bank & Catatan -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0">
                    <div class="card-header bg-light">
                        <strong>Informasi Pembayaran</strong>
                    </div>
                    <div class="card-body">
                        <?php 
                        $bankInfo = array_filter($pengaturan, function($item) {
                            return in_array($item['key_name'], ['bank_name', 'bank_account', 'account_name']);
                        });
                        ?>
                        <?php foreach($bankInfo as $info): ?>
                            <p class="mb-1">
                                <strong><?= $info['label'] ?>:</strong> <?= $info['value'] ?>
                            </p>
                        <?php endforeach; ?>
                        
                        <?php if($pesanan['catatan_admin']): ?>
                        <div class="mt-3">
                            <strong>Catatan Admin:</strong>
                            <p class="mb-0"><?= nl2br($pesanan['catatan_admin']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="signature-area">
                    <div class="row">
                        <div class="col-6 text-center">
                            <p>Pelanggan,</p>
                            <div style="margin-top: 60px;"></div>
                            <p>( <?= $pesanan['nama_lengkap'] ?> )</p>
                        </div>
                        <div class="col-6 text-center">
                            <p>Hormat Kami,</p>
                            <div style="margin-top: 60px;"></div>
                            <p>( Maulia Wedding )</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <hr>
                <p class="text-muted small">
                    Invoice ini sah dan dapat digunakan sebagai bukti pembayaran.<br>
                    Terima kasih telah mempercayakan kebutuhan pernikahan Anda kepada Maulia Wedding.
                </p>
                <p class="text-muted small mb-0">
                    Dicetak pada: <?= date('d F Y H:i:s') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Tombol Cetak (hanya tampil di browser) -->
    <div class="container mt-3 no-print">
        <div class="text-center">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Cetak Invoice
            </button>
            <a href="<?= base_url('admin/pesanan') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <script>
        // Auto print saat halaman dimuat (opsional)
        window.onload = function() {
            // Uncomment baris berikut untuk auto print
            // window.print();
        }
    </script>
</body>
</html>