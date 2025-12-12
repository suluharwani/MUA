
<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/pesanan') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Pesanan -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Detail Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kode Pesanan</label>
                            <p class="fw-bold"><?= $pesanan['kode_pesanan'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Pesanan</label>
                            <p><?= date('d F Y H:i', strtotime($pesanan['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nama Lengkap</label>
                            <p class="fw-bold"><?= $pesanan['nama_lengkap'] ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nomor WhatsApp</label>
                            <p>
                                <a href="https://wa.me/<?= $pesanan['no_whatsapp'] ?>" target="_blank" class="text-success">
                                    <i class="bi bi-whatsapp"></i> <?= $pesanan['no_whatsapp'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p><?= $pesanan['email'] ?? '-' ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tanggal Acara</label>
                            <p class="fw-bold"><?= date('d F Y', strtotime($pesanan['tanggal_acara'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Lokasi Acara</label>
                            <p><?= $pesanan['lokasi_acara'] ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jenis Layanan</label>
                            <p>
                                <span class="badge bg-light text-dark">
                                    <?= ucfirst($pesanan['jenis_layanan']) ?>
                                </span>
                            </p>
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
                    
                    <?php if ($pesanan['paket_id']): ?>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Paket Makeup</label>
                            <div class="card border">
                                <div class="card-body">
                                    <h6><?= $pesanan['paket_nama'] ?></h6>
                                    <p class="mb-1">Harga: Rp <?= number_format($pesanan['paket_harga'], 0, ',', '.') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($pesanan['kostum_id']): ?>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Kostum</label>
                            <div class="card border">
                                <div class="card-body">
                                    <h6><?= $pesanan['kostum_nama'] ?></h6>
                                    <p class="mb-1">Harga: Rp <?= number_format($pesanan['kostum_harga'], 0, ',', '.') ?></p>
                                    <p class="mb-0">Kategori: <?= $pesanan['kostum_kategori'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($pesanan['informasi_tambahan'])): ?>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label text-muted">Informasi Tambahan</label>
                            <div class="card border">
                                <div class="card-body">
                                    <p class="mb-0"><?= nl2br($pesanan['informasi_tambahan']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Pembayaran -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi Pembayaran</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('admin/pesanan/update-pembayaran/' . $pesanan['id']) ?>" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Harga</label>
                                <input type="number" name="total_harga" class="form-control" value="<?= $pesanan['total_harga'] ?? 0 ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">DP Dibayar</label>
                                <input type="number" name="dp_dibayar" class="form-control" value="<?= $pesanan['dp_dibayar'] ?? 0 ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select name="metode_pembayaran" class="form-select" required>
                                    <option value="">Pilih metode</option>
                                    <option value="transfer" <?= ($pesanan['metode_pembayaran'] == 'transfer') ? 'selected' : '' ?>>Transfer Bank</option>
                                    <option value="cash" <?= ($pesanan['metode_pembayaran'] == 'cash') ? 'selected' : '' ?>>Cash</option>
                                    <option value="qris" <?= ($pesanan['metode_pembayaran'] == 'qris') ? 'selected' : '' ?>>QRIS</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bukti Pembayaran</label>
                                <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf">
                                <?php if ($pesanan['bukti_pembayaran']): ?>
                                    <small class="text-muted">
                                        <a href="<?= base_url('uploads/bukti-pembayaran/' . $pesanan['bukti_pembayaran']) ?>" target="_blank">
                                            Lihat bukti saat ini
                                        </a>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Pembayaran
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Catatan Admin -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Catatan Admin</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('admin/pesanan/tambah-catatan/' . $pesanan['id']) ?>">
                        <div class="mb-3">
                            <textarea name="catatan_admin" class="form-control" rows="4" placeholder="Tambah catatan..."><?= $pesanan['catatan_admin'] ?? '' ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Catatan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Aksi dan Status -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Ubah Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form method="post" action="<?= base_url('admin/pesanan/ubah-status/' . $pesanan['id'] . '/pending') ?>">
                            <button type="submit" class="btn btn-warning w-100 mb-2 <?= ($pesanan['status'] == 'pending') ? 'active' : '' ?>">
                                <i class="bi bi-clock"></i> Pending
                            </button>
                        </form>
                        
                        <form method="post" action="<?= base_url('admin/pesanan/ubah-status/' . $pesanan['id'] . '/dikonfirmasi') ?>">
                            <button type="submit" class="btn btn-info w-100 mb-2 <?= ($pesanan['status'] == 'dikonfirmasi') ? 'active' : '' ?>">
                                <i class="bi bi-check-circle"></i> Dikonfirmasi
                            </button>
                        </form>
                        
                        <form method="post" action="<?= base_url('admin/pesanan/ubah-status/' . $pesanan['id'] . '/diproses') ?>">
                            <button type="submit" class="btn btn-primary w-100 mb-2 <?= ($pesanan['status'] == 'diproses') ? 'active' : '' ?>">
                                <i class="bi bi-gear"></i> Diproses
                            </button>
                        </form>
                        
                        <form method="post" action="<?= base_url('admin/pesanan/ubah-status/' . $pesanan['id'] . '/selesai') ?>">
                            <button type="submit" class="btn btn-success w-100 mb-2 <?= ($pesanan['status'] == 'selesai') ? 'active' : '' ?>">
                                <i class="bi bi-check-circle-fill"></i> Selesai
                            </button>
                        </form>
                        
                        <form method="post" action="<?= base_url('admin/pesanan/ubah-status/' . $pesanan['id'] . '/dibatalkan') ?>">
                            <button type="submit" class="btn btn-danger w-100 mb-2 <?= ($pesanan['status'] == 'dibatalkan') ? 'active' : '' ?>">
                                <i class="bi bi-x-circle"></i> Dibatalkan
                            </button>
                        </form>
                    </div>
                    
                    <hr>
                    
                    <form method="post" action="<?= base_url('admin/pesanan/ubah-status/' . $pesanan['id'] . '/' . $pesanan['status']) ?>">
                        <div class="mb-3">
                            <label class="form-label">Catatan Perubahan Status</label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="bi bi-save"></i> Simpan dengan Catatan
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Informasi Kontak -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tindakan Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="https://wa.me/<?= $pesanan['no_whatsapp'] ?>?text=Halo%20<?= urlencode($pesanan['nama_lengkap']) ?>%2C%20saya%20dari%20Maulia%20Wedding.%20Mengenai%20pesanan%20Anda%20dengan%20kode%20<?= $pesanan['kode_pesanan'] ?>%20..." target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp"></i> Chat WhatsApp
                        </a>
                        
                        <a href="tel:<?= $pesanan['no_whatsapp'] ?>" class="btn btn-outline-primary">
                            <i class="bi bi-telephone"></i> Telepon
                        </a>
                        
                        <a href="mailto:<?= $pesanan['email'] ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-envelope"></i> Email
                        </a>
                    </div>
                    
                    <hr>
                    
                    <div>
                        <h6>Ringkasan</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Total:</strong> 
                                <span class="float-end">Rp <?= number_format($pesanan['total_harga'] ?? 0, 0, ',', '.') ?></span>
                            </li>
                            <li class="mb-2">
                                <strong>DP Dibayar:</strong> 
                                <span class="float-end">Rp <?= number_format($pesanan['dp_dibayar'] ?? 0, 0, ',', '.') ?></span>
                            </li>
                            <li class="mb-2">
                                <strong>Sisa:</strong> 
                                <span class="float-end">
                                    Rp <?= number_format(($pesanan['total_harga'] ?? 0) - ($pesanan['dp_dibayar'] ?? 0), 0, ',', '.') ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>