<?php
// Ambil data dari URL
$order_id = $_GET['order_id'] ?? '';
$customer_name = $_GET['name'] ?? '';
$total_amount = $_GET['total'] ?? 0;

// Jika tidak ada order ID, redirect ke homepage
if (empty($order_id)) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - Security77 Store</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #4361ee;
            --success: #28a745;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .success-header {
            background: linear-gradient(135deg, var(--primary), #3a0ca3);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
        }
        
        .success-icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            color: var(--success);
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        
        .success-body {
            padding: 2.5rem;
        }
        
        .order-id-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin: 1.5rem 0;
            border: 2px dashed #dee2e6;
        }
        
        .order-id-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .order-id-value {
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 1px;
        }
        
        .customer-info {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
            font-weight: 500;
            text-align: right;
        }
        
        .next-steps {
            background: #d4edda;
            border-left: 4px solid var(--success);
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        
        .next-steps h5 {
            color: #155724;
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .next-steps h5 i {
            margin-right: 10px;
        }
        
        .step-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .step-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: flex-start;
        }
        
        .step-list li i {
            color: var(--success);
            margin-right: 10px;
            margin-top: 2px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 2rem;
        }
        
        .btn-action {
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }
        
        .btn-home {
            background: linear-gradient(135deg, var(--primary), #3a0ca3);
            color: white;
        }
        
        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
        }
        
        .btn-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            color: white;
        }
        
        .btn-action i {
            margin-right: 8px;
            font-size: 1.2rem;
        }
        
        .note {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .note i {
            color: #dc3545;
            margin-right: 5px;
        }
        
        @media (max-width: 576px) {
            .success-body {
                padding: 1.5rem;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .order-id-value {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="mb-3">Pembayaran Berhasil!</h1>
            <p class="lead mb-0">Terima kasih telah berbelanja di Security77 Store</p>
        </div>
        
        <div class="success-body">
            <div class="text-center mb-4">
                <p class="mb-3">Pesanan Anda telah berhasil dikirim ke admin kami melalui Telegram.</p>
                <p class="text-muted">Admin akan menghubungi Anda untuk konfirmasi pembayaran.</p>
            </div>
            
            <div class="order-id-box">
                <div class="order-id-label">ORDER ID ANDA</div>
                <div class="order-id-value" id="orderId"><?= htmlspecialchars($order_id) ?></div>
                <button class="btn btn-sm btn-outline-primary mt-3" onclick="copyOrderId()">
                    <i class="fas fa-copy me-1"></i>Salin Order ID
                </button>
            </div>
            
            <div class="customer-info">
                <div class="info-item">
                    <span class="info-label">Nama Pelanggan</span>
                    <span class="info-value"><?= htmlspecialchars($customer_name) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Pembayaran</span>
                    <span class="info-value">Rp <?= number_format($total_amount, 0, ',', '.') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Waktu Pemesanan</span>
                    <span class="info-value"><?= date('d/m/Y H:i:s') ?></span>
                </div>
            </div>
            
            <div class="next-steps">
                <h5><i class="fas fa-list-check"></i>Proses Selanjutnya</h5>
                <ul class="step-list">
                    <li><i class="fas fa-check-circle"></i> Admin memverifikasi pembayaran (1-2 jam)</li>
                    <li><i class="fas fa-check-circle"></i> Anda akan mendapat konfirmasi via WhatsApp</li>
                    <li><i class="fas fa-check-circle"></i> Pesanan diproses untuk pengiriman</li>
                    <li><i class="fas fa-check-circle"></i> Anda mendapat nomor resi pengiriman</li>
                </ul>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="btn-action btn-home">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                
                <?php
                $whatsapp_number = "628113317077";
                $whatsapp_message = urlencode("Halo admin, saya baru saja melakukan pembayaran dengan Order ID: *$order_id*\n\nNama: $customer_name\n\nBisa dibantu cek status pembayaran saya?");
                $whatsapp_url = "https://wa.me/$whatsapp_number?text=$whatsapp_message";
                ?>
                <a href="<?= $whatsapp_url ?>" target="_blank" class="btn-action btn-whatsapp">
                    <i class="fab fa-whatsapp"></i> Chat Admin
                </a>
            </div>
            
            <div class="note">
                <i class="fas fa-exclamation-circle"></i>
                Jika dalam 2 jam belum ada konfirmasi, silakan hubungi admin via WhatsApp
            </div>
        </div>
    </div>
    
    <script>
        // Fungsi untuk copy Order ID
        function copyOrderId() {
            const orderId = document.getElementById('orderId').textContent;
            navigator.clipboard.writeText(orderId).then(() => {
                // Tampilkan toast
                showToast('Order ID berhasil disalin!', 'success');
            }).catch(err => {
                console.error('Gagal menyalin: ', err);
                showToast('Gagal menyalin Order ID', 'error');
            });
        }
        
        // Fungsi untuk tampilkan toast
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }
        
        // Tambahkan style untuk toast
        const style = document.createElement('style');
        style.textContent = `
            .toast-notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                color: white;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 10px;
                z-index: 9999;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
            }
            
            .toast-notification.success {
                background: #28a745;
            }
            
            .toast-notification.error {
                background: #dc3545;
            }
            
            .toast-notification.show {
                opacity: 1;
                transform: translateX(0);
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>