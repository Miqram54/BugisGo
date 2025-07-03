<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - BugisGo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --background-color: #f4f6f9;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--background-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            padding: 15px;
        }

        .login-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            transition: all 0.3s ease;
        }

        .login-container:hover {
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .login-header p {
            color: var(--secondary-color);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <h2>Registrasi Akun BugisGo</h2>
                <p>Buat akun baru Anda</p>
            </div>

            <!-- Tampilkan pesan error atau sukses -->
            <?php
            session_start();
            // Tampilkan pesan error jika ada
            if (isset($_SESSION['error'])) {
                echo '<div class="error-message">' . 
                     htmlspecialchars($_SESSION['error']) . 
                     '</div>';
                // Hapus pesan error setelah ditampilkan
                unset($_SESSION['error']);
            }

            // Tampilkan pesan sukses jika ada
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success text-center">' . 
                     htmlspecialchars($_SESSION['success']) . 
                     '</div>';
                // Hapus pesan sukses setelah ditampilkan
                unset($_SESSION['success']);
            }
            ?>

            <form action="proses/proses_registrasi.php" method="post">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="nama" name="nama" required 
                               placeholder="Masukkan nama lengkap">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="Masukkan email">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Masukkan password" minlength="6">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required 
                               placeholder="Ulangi password" minlength="6">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="pengunjung">Pengunjung</option>
                    </select>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS dan JavaScript Validasi -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
