<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= get_logo('favicon', 'assets/img/favicon.ico') ?>" type="image/x-icon">
    <title><?= $title ?? ($pengaturan['nama_toko'] ?? 'Maulia - Professional Wedding Make Up Artist & Kostum Sewa - Grobogan') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Custom CSS dengan warna dinamis -->
    <style>
        :root {
            --primary-color: <?= $colors['primary'] ?? '#f9f7f4' ?>;
            --secondary-color: <?= $colors['secondary'] ?? '#f4f0eb' ?>;
            --accent-color: <?= $colors['accent'] ?? '#e8d7c9' ?>;
            --accent-dark: <?= $colors['accent_dark'] ?? '#d4b8a3' ?>;
            --costume-color: <?= $colors['costume'] ?? '#b8a7c8' ?>;
            --text-color: <?= $colors['text'] ?? '#5a5a5a' ?>;
            --heading-color: <?= $colors['heading'] ?? '#333333' ?>;
            --white: <?= $colors['white'] ?? '#ffffff' ?>;
            --shadow-color: <?= $colors['shadow'] ?? 'rgba(149, 157, 165, 0.1)' ?>;
            --success-color: <?= $colors['success'] ?? '#a8c8b8' ?>;
        }
        
        <?php 
            // Load custom CSS yang bergantung pada warna dinamis
            $customCssPath = FCPATH . 'assets/css/custom.css';
            if (file_exists($customCssPath)) {
                $customCss = file_get_contents($customCssPath);
                // Anda bisa memproses CSS di sini jika perlu
                echo $customCss;
            }
        ?>
        
        /* CSS tambahan yang menggunakan variabel warna */
        body {
            background-color: var(--primary-color);
            color: var(--text-color);
        }
        
        h1, h2, h3, h4, h5, h6 {
            color: var(--heading-color);
        }
        
        .btn-primary {
            background-color: var(--accent-dark);
            border-color: var(--accent-dark);
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .navbar {
            background-color: var(--white);
            box-shadow: 0 2px 4px var(--shadow-color);
        }
        
        .nav-link.active {
            color: var(--accent-dark) !important;
        }
        
        .card {
            border-color: var(--secondary-color);
        }
        
        .card-header {
            background-color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
        }
        
        footer {
            background-color: var(--heading-color);
            color: var(--white);
        }
        
        .social-icons a {
            color: var(--accent-dark);
        }
        
        .social-icons a:hover {
            color: var(--accent-color);
        }
        
        .back-to-top {
            background-color: var(--accent-dark);
            color: var(--white);
        }
        
        .back-to-top:hover {
            background-color: var(--accent-color);
        }
    </style>
    
    <?= $this->renderSection('css') ?>
</head>
<body>