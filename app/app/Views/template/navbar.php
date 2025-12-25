<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
    <?= get_logo_html('logo-img', 'width: 80px;', false) ?>
</a>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == '' || uri_string() == 'home') ? 'active' : '' ?>" href="<?= base_url() ?>">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'gallery') ? 'active' : '' ?>" href="<?= base_url('gallery') ?>">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'paket-makeup') ? 'active' : '' ?>" href="<?= base_url('paket-makeup') ?>">Paket Makeup</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'sewa-kostum') ? 'active' : '' ?>" href="<?= base_url('sewa-kostum') ?>">Sewa Kostum</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'lokasi') ? 'active' : '' ?>" href="<?= base_url('lokasi') ?>">Lokasi & Area</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (uri_string() == 'mitra') ? 'active' : '' ?>" href="<?= base_url('mitra') ?>">Mitra Kami</a>
                </li>
            </ul>
        </div>
    </div>
</nav>