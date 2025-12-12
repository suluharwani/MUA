<?= $this->extend('admin/layout/modal_layout_create') ?>

<?= $this->section('modal_content') ?>
<form id="pesanan-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" id="pesanan-id" value="">
    <input type="hidden" name="kode_pesanan" id="kode-pesanan-hidden" value="">
    
    <div class="row">
        <!-- Kolom Kiri: Data Pelanggan -->
        <div class="col-md-6">
            <!-- Informasi Pesanan -->
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Informasi Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Pesanan <span class="text-danger">*</span></label>
                        <input type="text" id="kode-pesanan-display" class="form-control form-control-sm" readonly>
                        <small class="text-muted">Kode otomatis dibuat sistem</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Jenis Layanan <span class="text-danger">*</span></label>
                        <select name="jenis_layanan" class="form-select form-select-sm" id="jenis-layanan" required>
                            <option value="">Pilih Layanan</option>
                            <option value="makeup">Makeup Only</option>
                            <option value="kostum">Kostum Only</option>
                            <option value="keduanya">Makeup & Kostum</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Acara <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_acara" class="form-control form-control-sm" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lama Sewa (Hari)</label>
                            <input type="number" name="lama_sewa" class="form-control form-control-sm" 
                                   value="1" min="1" max="30" id="lama-sewa" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Data Pelanggan -->
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Data Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control form-control-sm" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="tel" name="no_whatsapp" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lokasi & Area -->
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Lokasi & Area</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Lokasi Acara <span class="text-danger">*</span></label>
                        <textarea name="lokasi_acara" class="form-control form-control-sm" rows="2" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Area Layanan</label>
                            <select name="area_id" class="form-select form-select-sm" id="area-layanan">
                                <option value="">Pilih Area</option>
                                <?php foreach($area_layanan as $area): ?>
                                    <option value="<?= $area['id'] ?>" 
                                            data-biaya="<?= $area['biaya_tambahan'] ?>"
                                            data-jenis="<?= $area['jenis_area'] ?>">
                                        <?= $area['nama_area'] ?>
                                        <?php if($area['biaya_tambahan'] > 0): ?>
                                            (+Rp <?= number_format($area['biaya_tambahan'], 0, ',', '.') ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Biaya Transport Tambahan</label>
                            <input type="number" name="biaya_transport" class="form-control form-control-sm" 
                                   value="0" min="0" id="biaya-transport">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Detail Layanan & Harga -->
        <div class="col-md-6">
            <!-- Pilihan Paket Makeup -->
            <div class="card mb-3" id="paket-section">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Pilihan Paket Makeup</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <select name="paket_id" class="form-select form-select-sm" id="paket-makeup">
                            <option value="">Pilih Paket Makeup</option>
                            <?php foreach($paket_makeup as $paket): ?>
                                <option value="<?= $paket['id'] ?>" 
                                        data-harga="<?= $paket['harga'] ?>"
                                        data-nama="<?= htmlspecialchars($paket['nama_paket']) ?>">
                                    <?= $paket['nama_paket'] ?> - Rp <?= number_format($paket['harga'], 0, ',', '.') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="paket-info" class="small text-muted" style="display: none;">
                        <strong>Paket Terpilih:</strong> <span id="paket-nama"></span><br>
                        <strong>Harga:</strong> Rp <span id="paket-harga"></span><br>
                        <span id="paket-deskripsi"></span>
                    </div>
                </div>
            </div>
            
            <!-- Pilihan Kostum -->
            <div class="card mb-3" id="kostum-section">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Pilihan Kostum</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <select name="kostum_id" class="form-select form-select-sm" id="kostum-select">
                            <option value="">Pilih Kostum</option>
                            <?php foreach($kostum as $item): ?>
                                <?php if($item['stok_tersedia'] > 0): ?>
                                    <option value="<?= $item['id'] ?>" 
                                            data-harga="<?= $item['harga_sewa'] ?>"
                                            data-nama="<?= htmlspecialchars($item['nama_kostum']) ?>"
                                            data-stok="<?= $item['stok_tersedia'] ?>"
                                            data-deskripsi="<?= htmlspecialchars($item['deskripsi'] ?? '') ?>">
                                        <?= $item['nama_kostum'] ?> 
                                        - Rp <?= number_format($item['harga_sewa'], 0, ',', '.') ?> 
                                        (Stok: <?= $item['stok_tersedia'] ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="kostum-info" class="small text-muted" style="display: none;">
                        <strong>Kostum Terpilih:</strong> <span id="kostum-nama"></span><br>
                        <strong>Harga Sewa:</strong> Rp <span id="kostum-harga"></span>/hari<br>
                        <strong>Stok Tersedia:</strong> <span id="kostum-stok"></span><br>
                        <span id="kostum-deskripsi"></span>
                    </div>
                </div>
            </div>
            
            <!-- Diskon & Pajak -->
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Diskon & Pajak</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diskon</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="diskon_type" 
                                           id="diskon-persen" value="persen" checked>
                                    <label class="form-check-label" for="diskon-persen">%</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="diskon_type" 
                                           id="diskon-nominal" value="nominal">
                                    <label class="form-check-label" for="diskon-nominal">Rp</label>
                                </div>
                            </div>
                            <input type="number" name="diskon_persen" class="form-control form-control-sm" 
                                   value="0" min="0" max="100" step="0.1" id="diskon-persen-input">
                            <input type="number" name="diskon_nominal" class="form-control form-control-sm mt-2" 
                                   value="0" min="0" id="diskon-nominal-input" style="display: none;">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pajak</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pajak_type" 
                                           id="pajak-persen" value="persen" checked>
                                    <label class="form-check-label" for="pajak-persen">%</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="pajak_type" 
                                           id="pajak-nominal" value="nominal">
                                    <label class="form-check-label" for="pajak-nominal">Rp</label>
                                </div>
                            </div>
                            <input type="number" name="pajak_persen" class="form-control form-control-sm" 
                                   value="0" min="0" max="100" step="0.1" id="pajak-persen-input">
                            <input type="number" name="pajak_nominal" class="form-control form-control-sm mt-2" 
                                   value="0" min="0" id="pajak-nominal-input" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ringkasan Harga -->
            <div class="card mb-3">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Ringkasan Harga</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6">Subtotal:</div>
                        <div class="col-6 text-end"><span id="subtotal-display">Rp 0</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">Diskon:</div>
                        <div class="col-6 text-end text-danger"><span id="diskon-display">Rp 0</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">Subtotal setelah diskon:</div>
                        <div class="col-6 text-end"><span id="subtotal-setelah-diskon-display">Rp 0</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">Pajak:</div>
                        <div class="col-6 text-end"><span id="pajak-display">Rp 0</span></div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-2">
                        <div class="col-6"><strong>Total Akhir:</strong></div>
                        <div class="col-6 text-end"><strong><span id="total-akhir-display">Rp 0</span></strong></div>
                    </div>
                    <div class="row">
                        <div class="col-6">DP Minimal (50%):</div>
                        <div class="col-6 text-end"><span id="dp-minimal-display">Rp 0</span></div>
                    </div>
                    
                    <!-- Hidden fields untuk perhitungan -->
                    <input type="hidden" name="subtotal" id="subtotal-hidden" value="0">
                    <input type="hidden" name="diskon_nominal" id="diskon-nominal-hidden" value="0">
                    <input type="hidden" name="pajak_nominal" id="pajak-nominal-hidden" value="0">
                    <input type="hidden" name="total_akhir" id="total-akhir-hidden" value="0">
                </div>
            </div>
            
            <!-- Pembayaran & Status -->
            <div class="card">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Pembayaran & Status</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-select form-select-sm">
                                <option value="">Pilih Metode</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="cash">Cash</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending" selected>Pending</option>
                                <option value="dikonfirmasi">Dikonfirmasi</option>
                                <option value="diproses">Diproses</option>
                                <option value="selesai">Selesai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">DP Dibayar</label>
                        <input type="number" name="dp_dibayar" class="form-control form-control-sm" value="0" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Admin</label>
                        <textarea name="catatan_admin" class="form-control form-control-sm" rows="2" 
                                  placeholder="Catatan internal..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Informasi Tambahan (dari Pelanggan)</label>
                        <textarea name="informasi_tambahan" class="form-control form-control-sm" rows="2" 
                                  placeholder="Informasi tambahan dari pelanggan..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-end mt-3">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm" id="submit-btn">
            <i class="bi bi-save"></i> Simpan Pesanan
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Generate kode pesanan otomatis
    generateKodePesanan();
    
    // Tampilkan/sembunyikan section berdasarkan jenis layanan
    $('#jenis-layanan').on('change', function() {
        const jenis = $(this).val();
        
        if (jenis === 'makeup') {
            $('#paket-section').show();
            $('#kostum-section').hide();
            $('#lama-sewa').hide();
        } else if (jenis === 'kostum') {
            $('#paket-section').hide();
            $('#kostum-section').show();
            $('#lama-sewa').show();
        } else if (jenis === 'keduanya') {
            $('#paket-section').show();
            $('#kostum-section').show();
            $('#lama-sewa').show();
        } else {
            $('#paket-section').hide();
            $('#kostum-section').hide();
            $('#lama-sewa').hide();
        }
        
        calculatePrice();
    });
    
    // Toggle diskon type
    $('input[name="diskon_type"]').on('change', function() {
        if ($(this).val() === 'persen') {
            $('#diskon-nominal-input').hide().val(0);
            $('#diskon-persen-input').show();
        } else {
            $('#diskon-persen-input').hide().val(0);
            $('#diskon-nominal-input').show();
        }
        calculatePrice();
    });
    
    // Toggle pajak type
    $('input[name="pajak_type"]').on('change', function() {
        if ($(this).val() === 'persen') {
            $('#pajak-nominal-input').hide().val(0);
            $('#pajak-persen-input').show();
        } else {
            $('#pajak-persen-input').hide().val(0);
            $('#pajak-nominal-input').show();
        }
        calculatePrice();
    });
    
    // Event listeners untuk perhitungan harga
    $('#paket-makeup, #kostum-select, #area-layanan, #lama-sewa').on('change', function() {
        updateItemInfo();
        calculatePrice();
    });
    
    $('#biaya-transport, #diskon-persen-input, #diskon-nominal-input, #pajak-persen-input, #pajak-nominal-input').on('keyup', function() {
        calculatePrice();
    });
    
    // Area layanan - auto set biaya transport
    $('#area-layanan').on('change', function() {
        const selected = $(this).find(':selected');
        const biaya = selected.data('biaya') || 0;
        const jenis = selected.data('jenis');
        
        if (jenis === 'sekunder' && biaya > 0) {
            $('#biaya-transport').val(biaya);
        } else {
            $('#biaya-transport').val(0);
        }
        
        calculatePrice();
    });
    
    // Form submission
    $('#pesanan-form').on('submit', function(e) {
        e.preventDefault();
        savePesanan();
    });
});

// Fungsi generate kode pesanan
function generateKodePesanan() {
    const prefix = 'INV';
    const date = new Date();
    const year = date.getFullYear().toString().slice(-2);
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const day = ('0' + date.getDate()).slice(-2);
    const random = Math.floor(1000 + Math.random() * 9000);
    
    const kode = `${prefix}${year}${month}${day}${random}`;
    $('#kode-pesanan-display').val(kode);
    $('#kode-pesanan-hidden').val(kode);
}

// Update info item terpilih
function updateItemInfo() {
    // Paket makeup info
    const paketSelect = $('#paket-makeup');
    const paketSelected = paketSelect.find(':selected');
    
    if (paketSelected.val()) {
        $('#paket-info').show();
        $('#paket-nama').text(paketSelected.data('nama'));
        $('#paket-harga').text(formatNumber(paketSelected.data('harga')));
        $('#paket-deskripsi').text(paketSelected.data('deskripsi') || '');
    } else {
        $('#paket-info').hide();
    }
    
    // Kostum info
    const kostumSelect = $('#kostum-select');
    const kostumSelected = kostumSelect.find(':selected');
    
    if (kostumSelected.val()) {
        $('#kostum-info').show();
        $('#kostum-nama').text(kostumSelected.data('nama'));
        $('#kostum-harga').text(formatNumber(kostumSelected.data('harga')));
        $('#kostum-stok').text(kostumSelected.data('stok'));
        $('#kostum-deskripsi').text(kostumSelected.data('deskripsi') || '');
    } else {
        $('#kostum-info').hide();
    }
}

// Hitung harga
function calculatePrice() {
    let subtotal = 0;
    
    // Hitung harga paket
    const paketHarga = $('#paket-makeup').find(':selected').data('harga') || 0;
    subtotal += parseFloat(paketHarga);
    
    // Hitung harga kostum
    const kostumHarga = $('#kostum-select').find(':selected').data('harga') || 0;
    const lamaSewa = $('#lama-sewa').val() || 1;
    subtotal += parseFloat(kostumHarga) * parseInt(lamaSewa);
    
    // Hitung biaya transport
    const biayaTransport = $('#biaya-transport').val() || 0;
    subtotal += parseFloat(biayaTransport);
    
    // Hitung diskon
    let diskon = 0;
    const diskonType = $('input[name="diskon_type"]:checked').val();
    
    if (diskonType === 'persen') {
        const diskonPersen = $('#diskon-persen-input').val() || 0;
        diskon = subtotal * (parseFloat(diskonPersen) / 100);
    } else {
        diskon = $('#diskon-nominal-input').val() || 0;
    }
    
    const subtotalSetelahDiskon = subtotal - parseFloat(diskon);
    
    // Hitung pajak
    let pajak = 0;
    const pajakType = $('input[name="pajak_type"]:checked').val();
    
    if (pajakType === 'persen') {
        const pajakPersen = $('#pajak-persen-input').val() || 0;
        pajak = subtotalSetelahDiskon * (parseFloat(pajakPersen) / 100);
    } else {
        pajak = $('#pajak-nominal-input').val() || 0;
    }
    
    const totalAkhir = subtotalSetelahDiskon + parseFloat(pajak);
    const dpMinimal = totalAkhir * 0.5; // 50%
    
    // Update display
    $('#subtotal-display').text('Rp ' + formatNumber(subtotal));
    $('#diskon-display').text('Rp ' + formatNumber(diskon));
    $('#subtotal-setelah-diskon-display').text('Rp ' + formatNumber(subtotalSetelahDiskon));
    $('#pajak-display').text('Rp ' + formatNumber(pajak));
    $('#total-akhir-display').text('Rp ' + formatNumber(totalAkhir));
    $('#dp-minimal-display').text('Rp ' + formatNumber(dpMinimal));
    
    // Update hidden fields
    $('#subtotal-hidden').val(subtotal);
    $('#diskon-nominal-hidden').val(diskon);
    $('#pajak-nominal-hidden').val(pajak);
    $('#total-akhir-hidden').val(totalAkhir);
}

// Format number dengan separator
function formatNumber(num) {
    return parseFloat(num).toLocaleString('id-ID');
}

// Simpan pesanan
function savePesanan() {
    const formData = new FormData(document.getElementById('pesanan-form'));
    const isEdit = $('#pesanan-id').val();
    
    // Validasi required fields
    const requiredFields = ['nama_lengkap', 'no_whatsapp', 'jenis_layanan', 'tanggal_acara', 'lokasi_acara'];
    let isValid = true;
    let errorMessages = [];
    
    requiredFields.forEach(field => {
        const value = $(`[name="${field}"]`).val();
        if (!value || value.trim() === '') {
            isValid = false;
            errorMessages.push(`${field.replace('_', ' ')} harus diisi`);
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Data Belum Lengkap',
            text: errorMessages.join('\n'),
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show loading
    $('#submit-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');
    
    $.ajax({
        url: isEdit ? '<?= base_url("admin/pesanan/update") ?>/' + isEdit : '<?= base_url("admin/pesanan/store") ?>',
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
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Tutup modal
                    $('#createModal').modal('hide');
                    
                    // Refresh tabel
                    if (typeof table !== 'undefined') {
                        table.ajax.reload();
                    }
                    
                    // Refresh stats
                    if (typeof loadStats !== 'undefined') {
                        loadStats();
                    }
                    
                    // Reset form jika bukan edit
                    if (!isEdit) {
                        resetForm();
                    }
                });
            } else {
                let errorMsg = response.message;
                if (response.errors) {
                    errorMsg += '\n' + Object.values(response.errors).join('\n');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: errorMsg.replace(/\n/g, '<br>'),
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menyimpan: ' + error,
                confirmButtonText: 'OK'
            });
        },
        complete: function() {
            $('#submit-btn').prop('disabled', false).html('<i class="bi bi-save"></i> Simpan Pesanan');
        }
    });
}

// Reset form
function resetForm() {
    $('#pesanan-form')[0].reset();
    $('#pesanan-id').val('');
    generateKodePesanan();
    $('#paket-info, #kostum-info').hide();
    calculatePrice();
    $('input[name="diskon_type"][value="persen"]').prop('checked', true);
    $('input[name="pajak_type"][value="persen"]').prop('checked', true);
    $('#diskon-nominal-input, #pajak-nominal-input').hide();
    $('#diskon-persen-input, #pajak-persen-input').show();
    $('#jenis-layanan').trigger('change');
}
function populateEditForm(data) {
    // Set ID
    $('#pesanan-id').val(data.id);
    
    // Basic information
    $('#kode-pesanan-display').val(data.kode_pesanan);
    $('#kode-pesanan-hidden').val(data.kode_pesanan);
    $('input[name="nama_lengkap"]').val(data.nama_lengkap);
    $('input[name="no_whatsapp"]').val(data.no_whatsapp);
    $('input[name="email"]').val(data.email || '');
    $('select[name="jenis_layanan"]').val(data.jenis_layanan).trigger('change');
    $('input[name="tanggal_acara"]').val(data.tanggal_acara);
    $('textarea[name="lokasi_acara"]').val(data.lokasi_acara);
    $('textarea[name="informasi_tambahan"]').val(data.informasi_tambahan || '');
    $('select[name="status"]').val(data.status);
    $('select[name="metode_pembayaran"]').val(data.metode_pembayaran || '');
    $('textarea[name="catatan_admin"]').val(data.catatan_admin || '');
    $('input[name="dp_dibayar"]').val(data.dp_dibayar || 0);
    
    // Optional fields
    if (data.paket_id) {
        $('#paket-makeup').val(data.paket_id).trigger('change');
    }
    
    if (data.kostum_id) {
        $('#kostum-select').val(data.kostum_id).trigger('change');
        $('#lama-sewa').val(data.lama_sewa || 1);
    }
    
    if (data.area_id) {
        $('#area-layanan').val(data.area_id).trigger('change');
    }
    
    $('input[name="biaya_transport"]').val(data.biaya_transport || 0);
    
    // Diskon
    if (data.diskon_persen > 0) {
        $('input[name="diskon_type"][value="persen"]').prop('checked', true);
        $('#diskon-persen-input').val(data.diskon_persen).show();
        $('#diskon-nominal-input').val(0).hide();
    } else if (data.diskon_nominal > 0) {
        $('input[name="diskon_type"][value="nominal"]').prop('checked', true);
        $('#diskon-nominal-input').val(data.diskon_nominal).show();
        $('#diskon-persen-input').val(0).hide();
    }
    
    // Pajak
    if (data.pajak_persen > 0) {
        $('input[name="pajak_type"][value="persen"]').prop('checked', true);
        $('#pajak-persen-input').val(data.pajak_persen).show();
        $('#pajak-nominal-input').val(0).hide();
    } else if (data.pajak_nominal > 0) {
        $('input[name="pajak_type"][value="nominal"]').prop('checked', true);
        $('#pajak-nominal-input').val(data.pajak_nominal).show();
        $('#pajak-persen-input').val(0).hide();
    }
    
    // Trigger calculate price
    setTimeout(() => {
        calculatePrice();
    }, 500);
}
</script>
<?= $this->endSection() ?>