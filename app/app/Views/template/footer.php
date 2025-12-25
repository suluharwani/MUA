<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-5">
                <a href="<?= base_url() ?>" class="footer-logo">
                    <i class="bi bi-flower1 me-2" style="color: var(--accent-dark);"></i>Maulia
                </a>
                <p class="mt-3">Profesional Wedding Make Up Artist & Penyewaan Kostum Pernikahan di Grobogan, Jawa Tengah. Berpengalaman melayani berbagai konsep pernikahan tradisional dan modern.</p>
                <div class="social-icons mt-4">
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="https://wa.me/6287731310979" target="_blank"><i class="bi bi-whatsapp"></i></a>
                    <a href="#"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-5">
                <h5 class="mb-4">Kontak & Lokasi</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="bi bi-whatsapp me-2" style="color: var(--accent-dark);"></i>
                        <span>0877-3131-0979 (WhatsApp)</span>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-telephone me-2" style="color: var(--accent-dark);"></i>
                        <span>0877-3131-0979 (Telepon)</span>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-geo-alt me-2" style="color: var(--accent-dark);"></i>
                        <span>Desa Klambu, Kec. Klambu, Grobogan, Jawa Tengah</span>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-clock me-2" style="color: var(--accent-dark);"></i>
                        <span>Senin - Minggu: 08:00 - 18:00 WIB</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-6 mb-5">
                <h5 class="mb-4">Layanan Kami</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">Make Up Pengantin</li>
                    <li class="mb-2">Make Up Keluarga & Bridal Party</li>
                    <li class="mb-2">Make Up Pre-Wedding</li>
                    <li class="mb-2">Sewa Gaun Pengantin</li>
                    <li class="mb-2">Sewa Setelan Pengantin Pria</li>
                    <li class="mb-2">Sewa Kostum Keluarga</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> Maulia Makeup & Sewa Kostum. All rights reserved. | Desa Klambu, Grobogan, Jawa Tengah</p>
        </div>
    </div>
</footer>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Syarat dan Ketentuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Proses Booking</h6>
                <p>Tanggal akan dianggap terbooking setelah pembayaran DP 50% diterima. DP tidak dapat dikembalikan (non-refundable) namun dapat dialihkan ke tanggal lain dengan pemberitahuan minimal 30 hari sebelum acara.</p>
                
                <h6 class="mt-4">2. Pembayaran</h6>
                <p>DP 50% dibayarkan saat konfirmasi booking. Pelunasan maksimal H-7 sebelum acara untuk makeup, dan saat pengambilan kostum. Keterlambatan pelunasan dapat mengakibatkan pembatalan booking dengan DP hangus.</p>
                
                <h6 class="mt-4">3. Sewa Kostum</h6>
                <p>Masa sewa standar 3 hari (H-1, H, H+1). Denda keterlambatan pengembalian Rp 50.000/hari. Kerusakan kostum akan dikenakan biaya perbaikan sesuai tingkat kerusakan.</p>
                
                <h6 class="mt-4">4. Transportasi</h6>
                <p>Harga sudah termasuk layanan di area Grobogan. Untuk daerah lain dikenakan biaya transportasi tambahan sesuai jarak. Biaya transport akan dikomunikasikan saat konsultasi.</p>
                
                <h6 class="mt-4">5. Perubahan Jadwal</h6>
                <p>Perubahan tanggal dapat dilakukan maksimal 14 hari sebelum acara dengan ketersediaan yang terbatas. Perubahan di bawah 14 hari dikenakan biaya administrasi 10%.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/6287731310979?text=Halo%20Maulia,%20saya%20ingin%20konsultasi%20tentang%20makeup%20atau%20sewa%20kostum%20pernikahan" 
   class="whatsapp-float" 
   target="_blank">
    <i class="bi bi-whatsapp"></i>
</a>
<div class="whatsapp-text">Chat via WhatsApp</div>

<!-- Back to Top Button -->
<a href="#" class="back-to-top" id="backToTop">
    <i class="bi bi-chevron-up"></i>
</a>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
    <?php 
        // Load custom JS
        $customJsPath = FCPATH . 'assets/js/custom.js';
        if (file_exists($customJsPath)) {
            echo file_get_contents($customJsPath);
        }
    ?>
</script>

<?= $this->renderSection('js') ?>
</body>
</html>