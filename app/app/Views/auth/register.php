<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin - Makeup & Kostum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h2 {
            color: #333;
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .alert {
            border-radius: 10px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php
    $userModel = new \App\Models\UserModel();
    $userCount = $userModel->countUsers();
    ?>
    
    <?php if ($userCount > 0): ?>
        <script>
            alert('Registrasi sudah ditutup. Hanya untuk pembuatan admin pertama.');
            window.location.href = '<?= base_url("login") ?>';
        </script>
    <?php else: ?>
        <div class="register-card">
            <div class="register-header">
                <h2>Registrasi Admin Pertama</h2>
                <p class="text-muted">Buat akun admin pertama untuk sistem</p>
            </div>
            
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form action="<?= base_url('register/proses') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" 
                           class="form-control" 
                           id="nama" 
                           name="nama" 
                           value="<?= old('nama') ?>"
                           required 
                           placeholder="Masukkan nama lengkap">
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           value="<?= old('email') ?>"
                           required 
                           placeholder="Masukkan email">
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required 
                           placeholder="Minimal 6 karakter">
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required 
                           placeholder="Ulangi password">
                </div>
                
                <div class="mb-3">
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle"></i> Ini adalah akun admin pertama. 
                            Setelah registrasi ini, halaman registrasi akan ditutup.
                        </small>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register">Daftar sebagai Admin</button>
            </form>
            
            <div class="login-link">
                Sudah punya akun? <a href="<?= base_url('login') ?>">Login di sini</a>
            </div>
        </div>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>