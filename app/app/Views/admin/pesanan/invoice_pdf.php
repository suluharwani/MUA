<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $pesanan['kode_pesanan'] ?> - Maulia Wedding</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif; 
            font-size: 10pt;
            line-height: 1.3;
        }
        .container { width: 100%; padding: 10mm; }
        .header { margin-bottom: 15mm; }
        .logo { color: #d4b8a3; font-weight: bold; font-size: 18pt; }
        .invoice-title { font-size: 20pt; color: #2c3e50; text-align: right; }
        .section { margin-bottom: 10mm; }
        .section-title { 
            background-color: #f8f9fa; 
            padding: 5px 10px; 
            border-left: 4px solid #d4b8a3;
            margin-bottom: 5mm;
        }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #f8f9fa; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .total-box { 
            background-color: #f8f9fa; 
            padding: 15px;
            border: 1px solid #ddd;
            width: 60%;
            margin-left: auto;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { 
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9pt;
        }
        .bg-success { background-color: #28a745; color: white; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-danger { background-color: #dc3545; color: white; }
        .bg-info { background-color: #17a2b8; color: white; }
        .signature-area { 
            margin-top: 30mm;
            border-top: 1px dashed #999;
            padding-top: 10mm;
        }
        .footer { 
            margin-top: 15mm;
            border-top: 1px solid #ddd;
            padding-top: 5mm;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table>
                <tr>
                    <td width="60%">
                        <div class="logo">MAULIA WEDDING</div>
                        <div>Professional Wedding Make Up Artist & Kostum Sewa</div>
                        <div>Desa Klambu, Kecamatan Klambu, Kabupaten Grobogan, Jawa Tengah</div>
                        <div>Telp: 087731310979 | WhatsApp: 6287731310979</div>
                    </td>
                    <td class="text-right">
                        <div class="invoice-title">INVOICE</div>
                        <div><strong>No: <?= $pesanan['kode_pesanan'] ?></strong></div>
                        <div>Tanggal: <?= date('d/m/Y', strtotime($pesanan['created_at'])) ?></div>
                        <div>Jatuh Tempo: <?= date('d/m/Y', strtotime('+7 days', strtotime($pesanan['created_at']))) ?></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Informasi Pelanggan -->
        <div class="section">
            <div class="section-title">INFORMASI PELANGGAN</div>
            <table>
                <tr>
                    <td width="60%">
                        <strong><?= $pesanan['nama_lengkap'] ?></strong><br>
                        WhatsApp: <?= $pesanan['no_whatsapp'] ?><br>
                        <?php if($pesanan['email']): ?>Email: <?= $pesanan['email'] ?><br><?php endif; ?>
                        Tanggal Acara: <?= date('d/m/Y', strtotime($pesanan['tanggal_acara'])) ?><br>
                        Lokasi: <?= $pesanan['lokasi_acara'] ?>
                    </td>
                    <td>
                        <strong>Status Pesanan:</strong><br>
                        <span class="badge <?= $pesanan['status'] == 'selesai' ? 'bg-success' : 
                                               ($pesanan['status'] == 'pending' ? 'bg-warning' : 'bg-info') ?>">
                            <?= strtoupper($pesanan['status']) ?>
                        </span><br><br>
                        <strong>Status Pembayaran:</strong><br>
                        <?php if($paymentSummary): ?>
                        <span class="badge <?= $paymentSummary['status_pembayaran'] == 'lunas' ? 'bg-success' : 'bg-warning' ?>">
                            <?= strtoupper($paymentSummary['status_pembayaran']) ?>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Rincian Layanan -->
        <div class="section">
            <div class="section-title">RINCIAN LAYANAN</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Deskripsi</th>
                        <th width="15%" class="text-right">Harga</th>
                        <th width="10%" class="text-right">Jml</th>
                        <th width="15%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $itemNumber = 1; ?>
                    
                    <?php if($pesanan['paket_id']): ?>
                    <tr>
                        <td><?= $itemNumber++ ?></td>
                        <td>
                            <strong>Paket Makeup:</strong> <?= $pesanan['paket_nama'] ?><br>
                            <small><?= $pesanan['paket_deskripsi'] ?></small>
                        </td>
                        <td class="text-right">Rp <?= number_format($pesanan['paket_harga'], 0, ',', '.') ?></td>
                        <td class="text-right">1</td>
                        <td class="text-right">Rp <?= number_format($pesanan['paket_harga'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if($pesanan['kostum_id']): ?>
                    <tr>
                        <td><?= $itemNumber++ ?></td>
                        <td>
                            <strong>Kostum:</strong> <?= $pesanan['kostum_nama'] ?><br>
                            <small>Kategori: <?= $pesanan['kostum_kategori'] ?></small>
                        </td>
                        <td class="text-right">Rp <?= number_format($pesanan['kostum_harga'], 0, ',', '.') ?></td>
                        <td class="text-right"><?= $pesanan['lama_sewa'] ?? 1 ?> hari</td>
                        <td class="text-right">Rp <?= number_format($pesanan['kostum_harga'] * ($pesanan['lama_sewa'] ?? 1), 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if($pesanan['biaya_transport'] > 0): ?>
                    <tr>
                        <td><?= $itemNumber++ ?></td>
                        <td><strong>Biaya Transport</strong></td>
                        <td class="text-right">Rp <?= number_format($pesanan['biaya_transport'], 0, ',', '.') ?></td>
                        <td class="text-right">1</td>
                        <td class="text-right">Rp <?= number_format($pesanan['biaya_transport'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if($pesanan['diskon_nominal'] > 0): ?>
                    <tr>
                        <td><?= $itemNumber++ ?></td>
                        <td colspan="3">
                            <strong>Diskon</strong>
                            <?php if($pesanan['diskon_persen'] > 0): ?>
                            <small>(<?= $pesanan['diskon_persen'] ?>%)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">- Rp <?= number_format($pesanan['diskon_nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if($pesanan['pajak_nominal'] > 0): ?>
                    <tr>
                        <td><?= $itemNumber++ ?></td>
                        <td colspan="3"><strong>Pajak</strong></td>
                        <td class="text-right">+ Rp <?= number_format($pesanan['pajak_nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="total-box">
            <table style="width: 100%;">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">Rp <?= number_format($pesanan['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php if($pesanan['diskon_nominal'] > 0): ?>
                <tr>
                    <td>Diskon:</td>
                    <td class="text-right">- Rp <?= number_format($pesanan['diskon_nominal'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                <?php if($pesanan['pajak_nominal'] > 0): ?>
                <tr>
                    <td>Pajak:</td>
                    <td class="text-right">+ Rp <?= number_format($pesanan['pajak_nominal'], 0, ',', '.') ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="2"><hr style="margin: 5px 0;"></td>
                </tr>
                <tr>
                    <td><strong>TOTAL:</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></strong></td>
                </tr>
            </table>
            
            <?php if($paymentSummary): ?>
            <div style="margin-top: 10px; padding: 10px; background-color: #e8f4f8; border-radius: 3px;">
                <table style="width: 100%;">
                    <tr>
                        <td>Total Dibayar:</td>
                        <td class="text-right"><strong>Rp <?= number_format($paymentSummary['total_dibayar'], 0, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <td>Sisa Pembayaran:</td>
                        <td class="text-right"><strong>Rp <?= number_format($paymentSummary['sisa_pembayaran'], 0, ',', '.') ?></strong></td>
                    </tr>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- Riwayat Pembayaran -->
        <?php if(!empty($paymentSummary['riwayat'])): ?>
        <div class="section">
            <div class="section-title">RIWAYAT PEMBAYARAN</div>
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Metode</th>
                        <th>Jenis</th>
                        <th width="15%" class="text-right">Jumlah</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach($paymentSummary['riwayat'] as $payment): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($payment['tanggal_pembayaran'])) ?></td>
                        <td><?= $payment['kode_pembayaran'] ?></td>
                        <td><?= ucfirst($payment['metode']) ?></td>
                        <td><?= ucfirst($payment['jenis_pembayaran']) ?></td>
                        <td class="text-right">Rp <?= number_format($payment['jumlah'], 0, ',', '.') ?></td>
                        <td>
                            <?php if($payment['status'] == 'diterima'): ?>
                                <span class="badge bg-success">Diterima</span>
                            <?php elseif($payment['status'] == 'pending'): ?>
                                <span class="badge bg-warning">Pending</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><?= ucfirst($payment['status']) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Informasi Bank -->
        <div class="section">
            <div class="section-title">INFORMASI PEMBAYARAN</div>
            <?php 
            $bankInfo = array_filter($pengaturan, function($item) {
                return in_array($item['key_name'], ['bank_name', 'bank_account', 'account_name']);
            });
            ?>
            <?php foreach($bankInfo as $info): ?>
                <div><?= $info['label'] ?>: <?= $info['value'] ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-area">
            <table>
                <tr>
                    <td width="45%" class="text-center">
                        <div style="height: 40px;"></div>
                        <div>Pelanggan,</div>
                        <div style="height: 30px;"></div>
                        <div>( <?= $pesanan['nama_lengkap'] ?> )</div>
                    </td>
                    <td width="10%"></td>
                    <td width="45%" class="text-center">
                        <div style="height: 40px;"></div>
                        <div>Hormat Kami,</div>
                        <div style="height: 30px;"></div>
                        <div>( Maulia Wedding )</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            Invoice ini sah dan dapat digunakan sebagai bukti pembayaran.<br>
            Terima kasih telah mempercayakan kebutuhan pernikahan Anda kepada Maulia Wedding.<br>
            Dicetak pada: <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>
</body>
</html>