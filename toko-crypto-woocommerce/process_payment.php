<?php
session_start();

// Konfigurasi Telegram
$telegram_bot_token = '8547597830:AAH4JVmHQ5RQXxU4IgYOiZ34qWtIHHbYB_c';
$telegram_chat_id = '1918776376';

// Debug mode
$debug_mode = false;

if ($debug_mode) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Fungsi untuk mengirim pesan ke Telegram
function sendTelegramMessage($message, $bot_token, $chat_id) {
    if (empty($bot_token) || empty($chat_id)) {
        return ['success' => false, 'error' => 'Token atau Chat ID Telegram belum dikonfigurasi'];
    }
    
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ];
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            return ['success' => false, 'error' => 'Gagal terhubung ke Telegram'];
        }
        
        $response_data = json_decode($response, true);
        
        if ($response_data && isset($response_data['ok']) && $response_data['ok']) {
            return ['success' => true, 'message_id' => $response_data['result']['message_id']];
        } else {
            return ['success' => false, 'error' => 'Telegram API error: ' . ($response_data['description'] ?? 'Unknown error')];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
    }
}

// Fungsi untuk mengirim foto ke Telegram (tanpa cURL - menggunakan file_get_contents)
function sendTelegramPhoto($photo_path, $caption, $bot_token, $chat_id) {
    if (empty($bot_token) || empty($chat_id)) {
        return ['success' => false, 'error' => 'Token atau Chat ID Telegram belum dikonfigurasi'];
    }
    
    if (!file_exists($photo_path)) {
        return ['success' => false, 'error' => 'File tidak ditemukan: ' . $photo_path];
    }
    
    $url = "https://api.telegram.org/bot{$bot_token}/sendPhoto";
    
    // Persiapan data untuk multipart/form-data
    $boundary = '----WebKitFormBoundary' . md5(time());
    
    // Baca file
    $file_content = file_get_contents($photo_path);
    $file_name = basename($photo_path);
    
    // Deteksi MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $photo_path);
    finfo_close($finfo);
    
    // Build multipart data
    $data = "--{$boundary}\r\n";
    $data .= "Content-Disposition: form-data; name=\"chat_id\"\r\n\r\n";
    $data .= $chat_id . "\r\n";
    
    $data .= "--{$boundary}\r\n";
    $data .= "Content-Disposition: form-data; name=\"caption\"\r\n\r\n";
    $data .= $caption . "\r\n";
    
    $data .= "--{$boundary}\r\n";
    $data .= "Content-Disposition: form-data; name=\"photo\"; filename=\"{$file_name}\"\r\n";
    $data .= "Content-Type: {$mime_type}\r\n\r\n";
    $data .= $file_content . "\r\n";
    $data .= "--{$boundary}--\r\n";
    
    $options = [
        'http' => [
            'header'  => "Content-Type: multipart/form-data; boundary={$boundary}",
            'method'  => 'POST',
            'content' => $data,
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        if ($response === FALSE) {
            $error = error_get_last();
            return ['success' => false, 'error' => 'Gagal mengirim foto: ' . ($error['message'] ?? 'Unknown error')];
        }
        
        $response_data = json_decode($response, true);
        
        if ($response_data && isset($response_data['ok']) && $response_data['ok']) {
            return ['success' => true, 'message_id' => $response_data['result']['message_id']];
        } else {
            return ['success' => false, 'error' => 'Telegram API error: ' . ($response_data['description'] ?? 'Unknown error')];
        }
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
    }
}

// Fungsi alternatif untuk mengirim foto (lebih sederhana)
function sendTelegramPhotoSimple($photo_path, $caption, $bot_token, $chat_id) {
    if (empty($bot_token) || empty($chat_id)) {
        return ['success' => false, 'error' => 'Token atau Chat ID Telegram belum dikonfigurasi'];
    }
    
    if (!file_exists($photo_path)) {
        return ['success' => false, 'error' => 'File tidak ditemukan: ' . $photo_path];
    }
    
    // Coba metode dengan CURL jika tersedia (lebih reliable)
    if (function_exists('curl_init')) {
        $url = "https://api.telegram.org/bot{$bot_token}/sendPhoto";
        
        $post_fields = [
            'chat_id' => $chat_id,
            'caption' => $caption,
            'photo' => new CURLFile($photo_path)
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code == 200) {
            $response_data = json_decode($response, true);
            if ($response_data && $response_data['ok']) {
                return ['success' => true, 'message_id' => $response_data['result']['message_id']];
            }
        }
        
        return ['success' => false, 'error' => 'CURL method failed'];
    }
    
    // Fallback ke metode pertama
    return sendTelegramPhoto($photo_path, $caption, $bot_token, $chat_id);
}

// Fungsi untuk upload file bukti pembayaran
function uploadPaymentProof($file) {
    $upload_dir = 'payment_proofs/';
    
    // Buat folder jika belum ada
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Validasi file
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $max_size = 10 * 1024 * 1024; // 10MB
    
    $file_name = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];
    
    // Cek error upload
    if ($file_error !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize di php.ini)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE di form)',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
        ];
        
        $error_msg = isset($upload_errors[$file_error]) ? $upload_errors[$file_error] : 'Error upload tidak diketahui (kode: ' . $file_error . ')';
        return ['success' => false, 'error' => $error_msg];
    }
    
    // Cek ukuran file
    if ($file_size > $max_size) {
        return ['success' => false, 'error' => 'Ukuran file terlalu besar. Maksimal 10MB'];
    }
    
    // Cek ekstensi file
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Format file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP'];
    }
    
    // Generate nama file unik
    $new_file_name = 'proof_' . date('Ymd_His') . '_' . uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $new_file_name;
    
    // Upload file
    if (move_uploaded_file($file_tmp, $file_path)) {
        return [
            'success' => true, 
            'file_path' => $file_path,
            'file_name' => $new_file_name,
            'file_ext' => $file_ext
        ];
    } else {
        return ['success' => false, 'error' => 'Gagal menyimpan file'];
    }
}

// Fungsi untuk generate order ID
function generateOrderId($payment_method) {
    $prefix = '';
    
    switch ($payment_method) {
        case 'crypto':
            $prefix = 'CRYPTO';
            break;
        case 'bank_transfer':
            $prefix = 'BANK';
            break;
        case 'shopee':
            $prefix = 'SHOP';
            break;
        case 'tokopedia':
            $prefix = 'TOKO';
            break;
        case 'whatsapp':
            $prefix = 'WA';
            break;
        default:
            $prefix = 'ORD';
    }
    
    $date = date('Ymd');
    $time = date('His');
    $random = strtoupper(substr(md5(microtime()), 0, 4));
    
    return $prefix . $date . $time . $random;
}

// Fungsi untuk format mata uang
function formatRupiah($number) {
    return 'Rp ' . number_format($number, 0, ',', '.');
}

// Cek apakah form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validasi input wajib
    $required_fields = ['full_name', 'phone_number', 'email', 'address'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error_message = "Field berikut harus diisi: " . implode(', ', $missing_fields);
        header('Location: payment_error.php?error=' . urlencode($error_message));
        exit();
    }
    
    // Ambil data dari form
    $payment_method = $_POST['payment_method'] ?? 'unknown';
    $product_id = intval($_POST['product_id'] ?? 0);
    $product_name = htmlspecialchars(trim($_POST['product_name'] ?? 'Produk Tidak Diketahui'));
    $product_price = floatval($_POST['product_price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    
    // Data pribadi
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $address = htmlspecialchars(trim($_POST['address']));
    $province = htmlspecialchars(trim($_POST['province'] ?? ''));
    $city = htmlspecialchars(trim($_POST['city'] ?? ''));
    $postal_code = htmlspecialchars(trim($_POST['postal_code'] ?? ''));
    
    // Generate Order ID
    $order_id = generateOrderId($payment_method);
    $total_amount = $product_price * $quantity;
    $timestamp = date('d/m/Y H:i:s');
    
    // Proses upload file bukti pembayaran (jika ada)
    $payment_proof_info = null;
    $has_payment_proof = false;
    
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadPaymentProof($_FILES['payment_proof']);
        
        if ($upload_result['success']) {
            $payment_proof_info = $upload_result;
            $has_payment_proof = true;
        }
    }
    
    // Siapkan pesan untuk Telegram
    $telegram_message = "🛒 *PEMESANAN BARU - SECURITY77 STORE* 🛒\n\n";
    
    $telegram_message .= "📋 *INFORMASI PEMESANAN*\n";
    $telegram_message .= "────────────────────\n";
    $telegram_message .= "🆔 *Order ID:* `{$order_id}`\n";
    $telegram_message .= "📅 *Tanggal:* {$timestamp}\n";
    $telegram_message .= "💳 *Metode:* " . strtoupper($payment_method) . "\n\n";
    
    $telegram_message .= "👤 *DATA PELANGGAN*\n";
    $telegram_message .= "────────────────────\n";
    $telegram_message .= "• *Nama:* {$full_name}\n";
    $telegram_message .= "• *Telepon:* {$phone_number}\n";
    $telegram_message .= "• *Email:* {$email}\n";
    $telegram_message .= "• *Alamat:* {$address}\n";
    
    if (!empty($province)) {
        $telegram_message .= "• *Provinsi:* {$province}\n";
    }
    if (!empty($city)) {
        $telegram_message .= "• *Kota:* {$city}\n";
    }
    if (!empty($postal_code)) {
        $telegram_message .= "• *Kode Pos:* {$postal_code}\n";
    }
    
    $telegram_message .= "\n🛍️ *DETAIL PRODUK*\n";
    $telegram_message .= "────────────────────\n";
    $telegram_message .= "• *Produk:* {$product_name}\n";
    $telegram_message .= "• *Harga Satuan:* " . formatRupiah($product_price) . "\n";
    $telegram_message .= "• *Jumlah:* {$quantity}\n";
    $telegram_message .= "• *Total:* *" . formatRupiah($total_amount) . "*\n\n";
    
    // Tambahkan detail pembayaran khusus
    if ($payment_method === 'crypto') {
        $crypto_wallet = htmlspecialchars(trim($_POST['crypto_wallet'] ?? ''));
        $crypto_amount = htmlspecialchars(trim($_POST['crypto_amount'] ?? ''));
        $crypto_address = htmlspecialchars(trim($_POST['crypto_address'] ?? ''));
        
        $telegram_message .= "💰 *DETAIL CRYPTO*\n";
        $telegram_message .= "────────────────────\n";
        $telegram_message .= "• *Wallet:* {$crypto_wallet}\n";
        $telegram_message .= "• *Jumlah:* {$crypto_amount}\n";
        $telegram_message .= "• *Alamat Pengirim:* `{$crypto_address}`\n\n";
        
        $telegram_message .= "📍 *ALAMAT WALLET KAMI:*\n";
        $telegram_message .= "`bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh`\n\n";
        
    } elseif ($payment_method === 'bank_transfer') {
        $bank_name = htmlspecialchars(trim($_POST['bank_name'] ?? ''));
        $bank_account = htmlspecialchars(trim($_POST['bank_account'] ?? ''));
        
        $telegram_message .= "🏦 *DETAIL BANK*\n";
        $telegram_message .= "────────────────────\n";
        $telegram_message .= "• *Bank:* {$bank_name}\n";
        $telegram_message .= "• *Rekening Pengirim:* {$bank_account}\n\n";
        
        $telegram_message .= "📍 *REKENING KAMI:*\n";
        $telegram_message .= "BCA: 1234567890 (SECURITY77 STORE)\n";
        $telegram_message .= "Mandiri: 0987654321 (SECURITY77 STORE)\n\n";
    }
    
    $telegram_message .= "📊 *STATUS:* MENUNGGU VERIFIKASI\n";
    $telegram_message .= "⏰ *ESTIMASI:* 1-2 JAM KERJA\n\n";
    
    $telegram_message .= "📞 *HUBUNGI PELANGGAN:*\n";
    $telegram_message .= "WhatsApp: https://wa.me/62" . ltrim($phone_number, '0') . "\n";
    $telegram_message .= "Email: {$email}\n\n";
    
    $telegram_message .= "🔔 *TINDAKAN:*\n";
    $telegram_message .= "1. Verifikasi pembayaran\n";
    $telegram_message .= "2. Konfirmasi ke pelanggan\n";
    $telegram_message .= "3. Proses pengiriman\n";
    
    // Kirim pesan teks ke Telegram
    $telegram_result = sendTelegramMessage($telegram_message, $telegram_bot_token, $telegram_chat_id);
    
    // Kirim foto bukti pembayaran ke Telegram (jika ada)
    if ($has_payment_proof) {
        $photo_caption = "📎 *BUKTI PEMBAYARAN*\n";
        $photo_caption .= "Order ID: `{$order_id}`\n";
        $photo_caption .= "Nama: {$full_name}\n";
        $photo_caption .= "Total: " . formatRupiah($total_amount) . "\n";
        $photo_caption .= "Metode: " . strtoupper($payment_method);
        
        // Coba kirim foto
        $photo_result = sendTelegramPhotoSimple($payment_proof_info['file_path'], $photo_caption, $telegram_bot_token, $telegram_chat_id);
        
        // Jika gagal kirim foto, kirim pesan dengan info file
        if (!$photo_result['success']) {
            $file_message = "📎 *INFORMASI BUKTI PEMBAYARAN*\n";
            $file_message .= "File: {$payment_proof_info['file_name']}\n";
            $file_message .= "Ukuran: " . round(filesize($payment_proof_info['file_path']) / 1024, 2) . " KB\n";
            $file_message .= "Format: " . strtoupper($payment_proof_info['file_ext']) . "\n";
            $file_message .= "Path: `{$payment_proof_info['file_path']}`";
            
            sendTelegramMessage($file_message, $telegram_bot_token, $telegram_chat_id);
        }
    } else {
        // Kirim pesan bahwa tidak ada bukti pembayaran
        $no_proof_message = "⚠️ *TIDAK ADA BUKTI PEMBAYARAN*\n";
        $no_proof_message .= "Pelanggan belum mengupload bukti pembayaran.\n";
        $no_proof_message .= "Harap konfirmasi ke pelanggan via WhatsApp.";
        
        sendTelegramMessage($no_proof_message, $telegram_bot_token, $telegram_chat_id);
    }
    
    // Backup data ke file JSON
    $backup_data = [
        'order_id' => $order_id,
        'timestamp' => date('Y-m-d H:i:s'),
        'payment_method' => $payment_method,
        'customer' => [
            'name' => $full_name,
            'phone' => $phone_number,
            'email' => $email,
            'address' => $address,
            'province' => $province,
            'city' => $city,
            'postal_code' => $postal_code
        ],
        'product' => [
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => $quantity,
            'total' => $total_amount
        ],
        'payment_proof' => $has_payment_proof ? $payment_proof_info : null,
        'telegram_sent' => $telegram_result['success'] ?? false,
        'photo_sent' => $photo_result['success'] ?? false
    ];
    
    // Simpan ke file backup
    $backup_dir = 'order_backups/';
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    
    $backup_file = $backup_dir . $order_id . '.json';
    file_put_contents($backup_file, json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Redirect ke halaman sukses
    header('Location: payment_success.php?order_id=' . urlencode($order_id) . 
           '&name=' . urlencode($full_name) . 
           '&total=' . urlencode($total_amount));
    exit();
    
} else {
    // Jika bukan POST request, tampilkan error
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Akses Ditolak - Security77 Store</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 class="mb-3">Akses Ditolak</h2>
            <p class="mb-4">Halaman ini hanya dapat diakses melalui form pembayaran.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Kembali ke Beranda
            </a>
        </div>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}
?>