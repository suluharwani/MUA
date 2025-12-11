<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <div>
            <a href="<?= base_url('admin/kostum') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Import Kostum dari CSV</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Petunjuk Import</h6>
                        <ul class="mb-0">
                            <li>File harus dalam format CSV</li>
                            <li>Kolom yang diperlukan: "Nama Kostum", "Kategori", "Harga Sewa", "Stok"</li>
                            <li>Kategori harus sesuai dengan pilihan: 
                                <?= implode(', ', array_values($kategori_options)) ?>
                            </li>
                            <li>Status: "Aktif" atau "Nonaktif"</li>
                            <li>Featured: "Ya" atau "Tidak"</li>
                        </ul>
                    </div>
                    
                    <form method="post" action="<?= base_url('admin/kostum/process-import') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">File CSV</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="csv_file" 
                                   name="csv_file" 
                                   accept=".csv" 
                                   required>
                            <small class="text-muted">Download template: 
                                <a href="<?= base_url('admin/kostum/export') ?>">Format CSV</a>
                            </small>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>