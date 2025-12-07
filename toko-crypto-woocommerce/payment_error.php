<?php
$error_message = $_GET['error'] ?? 'Terjadi kesalahan yang tidak diketahui';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Pembayaran - Security77 Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="fas fa-times-circle"></i>
        </div>
        <h2 class="mb-3">Terjadi Kesalahan</h2>
        <div class="alert alert-danger mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($error_message) ?>
        </div>
        <p class="mb-4">Silakan periksa kembali data yang Anda masukkan dan coba lagi.</p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="javascript:history.back()" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Beranda
            </a>
        </div>
    </div>
</body>
</html>