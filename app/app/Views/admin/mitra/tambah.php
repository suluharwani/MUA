<?= $this->extend('admin/layout/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?= $title ?></h1>
        <a href="<?= base_url('admin/mitra') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> 
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/mitra/simpan') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <!-- Informasi Dasar -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Dasar</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nama_mitra" class="form-label">Nama Mitra <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= (validation_show_error('nama_mitra')) ? 'is-invalid' : '' ?>" 
                                           id="nama_mitra" name="nama_mitra" 
                                           value="<?= old('nama_mitra') ?>" 
                                           placeholder="Masukkan nama mitra" required>
                                    <div class="invalid-feedback">
                                        <?= validation_show_error('nama_mitra') ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control <?= (validation_show_error('deskripsi')) ? 'is-invalid' : '' ?>" 
                                              id="deskripsi" name="deskripsi" rows="4" 
                                              placeholder="Deskripsi tentang mitra"><?= old('deskripsi') ?></textarea>
                                    <div class="invalid-feedback">
                                        <?= validation_show_error('deskripsi') ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telepon" class="form-label">Telepon</label>
                                            <input type="text" class="form-control <?= (validation_show_error('telepon')) ? 'is-invalid' : '' ?>" 
                                                   id="telepon" name="telepon" 
                                                   value="<?= old('telepon') ?>" 
                                                   placeholder="Contoh: 081234567890">
                                            <div class="invalid-feedback">
                                                <?= validation_show_error('telepon') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="whatsapp" class="form-label">WhatsApp <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?= (validation_show_error('whatsapp')) ? 'is-invalid' : '' ?>" 
                                                   id="whatsapp" name="whatsapp" 
                                                   value="<?= old('whatsapp') ?>" 
                                                   placeholder="Contoh: 081234567890" required>
                                            <div class="invalid-feedback">
                                                <?= validation_show_error('whatsapp') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control <?= (validation_show_error('email')) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" 
                                           value="<?= old('email') ?>" 
                                           placeholder="email@example.com">
                                    <div class="invalid-feedback">
                                        <?= validation_show_error('email') ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control <?= (validation_show_error('alamat')) ? 'is-invalid' : '' ?>" 
                                              id="alamat" name="alamat" rows="3" 
                                              placeholder="Alamat lengkap mitra"><?= old('alamat') ?></textarea>
                                    <div class="invalid-feedback">
                                        <?= validation_show_error('alamat') ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-control <?= (validation_show_error('website')) ? 'is-invalid' : '' ?>" 
                                                   id="website" name="website" 
                                                   value="<?= old('website') ?>" 
                                                   placeholder="https://example.com">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                            <select class="form-select <?= (validation_show_error('kategori')) ? 'is-invalid' : '' ?>" 
                                                    id="kategori" name="kategori" required>
                                                <option value="">Pilih Kategori</option>
                                                <?php foreach (model('App\Models\MitraModel')->getKategoriOptions() as $value => $label): ?>
                                                    <option value="<?= $value ?>" <?= old('kategori') == $value ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                <?= validation_show_error('kategori') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media Sosial -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-share"></i> Media Sosial</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="instagram" class="form-label">Instagram</label>
                                            <div class="input-group">
                                                <span class="input-group-text">@</span>
                                                <input type="text" class="form-control" 
                                                       id="instagram" name="instagram" 
                                                       value="<?= old('instagram') ?>" 
                                                       placeholder="username">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="facebook" class="form-label">Facebook</label>
                                            <input type="text" class="form-control" 
                                                   id="facebook" name="facebook" 
                                                   value="<?= old('facebook') ?>" 
                                                   placeholder="Nama halaman Facebook">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tiktok" class="form-label">TikTok</label>
                                            <div class="input-group">
                                                <span class="input-group-text">@</span>
                                                <input type="text" class="form-control" 
                                                       id="tiktok" name="tiktok" 
                                                       value="<?= old('tiktok') ?>" 
                                                       placeholder="username">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Layanan & Portofolio -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-briefcase"></i> Layanan & Portofolio</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="spesialisasi" class="form-label">Spesialisasi</label>
                                    <input type="text" class="form-control" 
                                           id="spesialisasi" name="spesialisasi" 
                                           value="<?= old('spesialisasi') ?>" 
                                           placeholder="Contoh: Fotografi Prewedding, Videografi Cinematic">
                                    <small class="text-muted">Pisahkan dengan koma jika lebih dari satu</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Layanan</label>
                                    <div id="layanan-container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="layanan[]" placeholder="Nama layanan">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeLayanan(this)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLayanan()">
                                        <i class="bi bi-plus"></i> Tambah Layanan
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="harga_mulai" class="form-label">Harga Mulai (Rp)</label>
                                            <input type="number" class="form-control" 
                                                   id="harga_mulai" name="harga_mulai" 
                                                   value="<?= old('harga_mulai') ?>" 
                                                   placeholder="Contoh: 5000000" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="pengalaman" class="form-label">Pengalaman</label>
                                            <input type="text" class="form-control" 
                                                   id="pengalaman" name="pengalaman" 
                                                   value="<?= old('pengalaman', '1 tahun') ?>" 
                                                   placeholder="Contoh: 5 tahun">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="keahlian" class="form-label">Keahlian</label>
                                    <textarea class="form-control" 
                                              id="keahlian" name="keahlian" rows="3" 
                                              placeholder="Keahlian khusus mitra"><?= old('keahlian') ?></textarea>
                                    <small class="text-muted">Pisahkan dengan koma jika lebih dari satu</small>
                                </div>

                                <div class="mb-3">
                                    <label for="portofolio_files" class="form-label">Portofolio (Gambar)</label>
                                    <input type="file" class="form-control" 
                                           id="portofolio_files" name="portofolio_files[]" 
                                           accept="image/*" multiple>
                                    <small class="text-muted">Upload gambar portofolio mitra (maks 5MB per file)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Kanan -->
                    <div class="col-lg-4">
                        <!-- Foto Profil -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-image"></i> Foto Profil</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <img id="foto-preview" src="<?= base_url('assets/img/default-avatar.png') ?>" 
                                         alt="Preview Foto" class="img-fluid rounded" 
                                         style="max-height: 200px;">
                                </div>
                                <div class="mb-3">
                                    <label for="foto" class="form-label">Upload Foto</label>
                                    <input type="file" class="form-control" 
                                           id="foto" name="foto" accept="image/*"
                                           onchange="previewImage(this, 'foto-preview')">
                                </div>
                            </div>
                        </div>

                        <!-- Gambar Tambahan -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="bi bi-images"></i> Gambar Tambahan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar Utama</label>
                                    <input type="file" class="form-control" 
                                           id="gambar" name="gambar" accept="image/*">
                                </div>
                                
                                <label for="gambar_tambahan" class="form-label">Gambar Lainnya</label>
                                <input type="file" class="form-control mb-2" 
                                       id="gambar_tambahan" name="gambar_tambahan[]" 
                                       accept="image/*" multiple>
                                <small class="text-muted">Upload gambar tambahan untuk galeri</small>
                            </div>
                        </div>

                        <!-- Pengaturan -->
                        <div class="card mb-4">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0"><i class="bi bi-gear"></i> Pengaturan</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="urutan" class="form-label">Urutan Tampil</label>
                                    <input type="number" class="form-control" 
                                           id="urutan" name="urutan" 
                                           value="<?= old('urutan', 0) ?>" 
                                           min="0">
                                    <small class="text-muted">Angka lebih kecil = tampil lebih awal</small>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_active" name="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">Aktif</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_featured" name="is_featured" value="1">
                                        <label class="form-check-label" for="is_featured">Featured</label>
                                        <small class="text-muted d-block">Tampilkan di halaman utama</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="rating" class="form-label">Rating</label>
                                    <input type="number" class="form-control" 
                                           id="rating" name="rating" 
                                           value="<?= old('rating', 5.0) ?>" 
                                           min="0" max="5" step="0.1">
                                    <small class="text-muted">Skala 0-5</small>
                                </div>
                            </div>
                        </div>

                        <!-- SEO -->
                        <div class="card mb-4">
                            <div class="card-header bg-purple text-white">
                                <h5 class="mb-0"><i class="bi bi-search"></i> SEO</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <textarea class="form-control" 
                                              id="meta_keywords" name="meta_keywords" rows="2"><?= old('meta_keywords') ?></textarea>
                                    <small class="text-muted">Pisahkan dengan koma</small>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" 
                                              id="meta_description" name="meta_description" rows="3"><?= old('meta_description') ?></textarea>
                                    <small class="text-muted">Deskripsi untuk SEO (maks 160 karakter)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Mitra
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview image function
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = '<?= base_url("assets/img/default-avatar.png") ?>';
    }
}

// Layanan management
function addLayanan() {
    const container = document.getElementById('layanan-container');
    const newInput = document.createElement('div');
    newInput.className = 'input-group mb-2';
    newInput.innerHTML = `
        <input type="text" class="form-control" name="layanan[]" placeholder="Nama layanan">
        <button type="button" class="btn btn-outline-danger" onclick="removeLayanan(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(newInput);
}

function removeLayanan(button) {
    const container = document.getElementById('layanan-container');
    const inputs = container.querySelectorAll('.input-group');
    
    if (inputs.length > 1) {
        button.closest('.input-group').remove();
    } else {
        // Reset the only input instead of removing it
        const input = inputs[0].querySelector('input');
        input.value = '';
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        // Simple validation
        const whatsapp = document.getElementById('whatsapp');
        const email = document.getElementById('email');
        
        if (whatsapp.value && !/^[0-9]{10,20}$/.test(whatsapp.value.replace(/\D/g, ''))) {
            alert('Format WhatsApp tidak valid. Harus 10-20 digit angka.');
            whatsapp.focus();
            e.preventDefault();
            return false;
        }
        
        if (email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            alert('Format email tidak valid.');
            email.focus();
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}

.card-header {
    border-radius: 0.375rem 0.375rem 0 0 !important;
}

.input-group .btn-outline-danger {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>

<?= $this->endSection() ?>