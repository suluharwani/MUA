<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <h6 class="text-muted">Informasi Pelanggan</h6>
            <div class="mb-3">
                <strong>Nama:</strong> <?= $pesanan['nama_lengkap'] ?>
            </div>
            <div class="mb-3">
                <strong>WhatsApp:</strong> 
                <a href="https://wa.me/<?= $pesanan['no_whatsapp'] ?>" class="text-success">
                    <?= $pesanan['no_whatsapp'] ?>
                </a>
            </div>
            <div class="mb-3">
                <strong>Email:</strong> <?= $pesanan['email'] ?? '-' ?>
            </div>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted">Detail Acara</h6>
            <div class="mb-3">
                <strong>Tanggal:</strong> <?= date('d F Y', strtotime($pesanan['tanggal_acara'])) ?>
            </div>
            <div class="mb-3">
                <strong>Lokasi:</strong> <?= $pesanan['lokasi_acara'] ?>
            </div>
            <div class="mb-3">
                <strong>Status:</strong>
                <?php
                $badgeClass = [
                    'pending' => 'bg-warning',
                    'dikonfirmasi' => 'bg-info',
                    'diproses' => 'bg-primary',
                    'selesai' => 'bg-success',
                    'dibatalkan' => 'bg-danger'
                ][$pesanan['status']] ?? 'bg-secondary';
                ?>
                <span class="badge <?= $badgeClass ?>">
                    <?= ucfirst($pesanan['status']) ?>
                </span>
            </div>
        </div>
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-12">
            <h6 class="text-muted">Detail Layanan</h6>
            <div class="row">
                <?php if($pesanan['paket_id']): ?>
                <div class="col-md-6 mb-3">
                    <div class="card border">
                        <div class="card-body">
                            <h6>Paket Makeup</h6>
                            <p class="mb-1"><?= $pesanan['paket_nama'] ?? '-' ?></p>
                            <p class="mb-1 text-muted">Rp <?= number_format($pesanan['paket_harga'] ?? 0, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($pesanan['kostum_id']): ?>
                <div class="col-md-6 mb-3">
                    <div class="card border">
                        <div class="card-body">
                            <h6>Kostum</h6>
                            <p class="mb-1"><?= $pesanan['kostum_nama'] ?? '-' ?></p>
                            <p class="mb-1 text-muted">Rp <?= number_format($pesanan['kostum_harga'] ?? 0, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if(!empty($pesanan['informasi_tambahan'])): ?>
    <hr>
    <div class="row">
        <div class="col-12">
            <h6 class="text-muted">Informasi Tambahan</h6>
            <div class="card border">
                <div class="card-body">
                    <?= nl2br($pesanan['informasi_tambahan']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>