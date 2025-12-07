<?php
session_start();

// Load data JSON
$data = file_get_contents("product.json");
$produk = json_decode($data, true);

// Ambil ID produk
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cari produk
$detail = null;
foreach ($produk as $p) {
    if ($p['id'] == $id) {
        $detail = $p;
        break;
    }
}
if (!$detail) {
    header("Location: index.php");
    exit();
}

// Hitung diskon %
$diskon_persen = 0;
if (!empty($detail['harga_asli']) && !empty($detail['harga_diskon']) && $detail['harga_asli'] > 0) {
    $diskon_persen = round((1 - ($detail['harga_diskon'] / $detail['harga_asli'])) * 100);
}

// Count cart untuk navbar
$cart_count = isset($_SESSION["cart"]) ? array_sum(array_column($_SESSION["cart"], "qty")) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($detail['nama']) ?> - Security77 Store</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/product-style.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shield-alt me-2"></i>Security77Store
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <form class="d-flex mx-auto my-2 my-lg-0 w-100" method="GET" action="index.php">
                    <div class="input-group">
                        <input type="text" class="form-control nav-search" placeholder="Cari produk..." name="search">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <div class="d-flex ms-lg-3 mt-3 mt-lg-0">
                    <a href="cart.php" class="btn cart-btn position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?= $cart_count ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- BREADCRUMB -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                <li class="breadcrumb-item"><a href="index.php?kategori=<?= urlencode($detail['kategori']) ?>"><?= htmlspecialchars($detail['kategori']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($detail['nama']) ?></li>
            </ol>
        </nav>
    </div>

    <!-- PRODUCT DETAIL -->
    <div class="container py-4 product-detail-container">
        <div class="row">
            <!-- Gambar Produk -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <div class="main-image-container">
                        <img id="mainProductImage" src="<?= $detail['gambar'] ?>" 
                             alt="<?= htmlspecialchars($detail['nama']) ?>"
                             class="main-product-image"
                             onerror="this.src='https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'">
                        
                        <?php if ($diskon_persen > 0): ?>
                            <div class="discount-badge-large">
                                <span>-<?= $diskon_persen ?>%</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($detail['stok']) && $detail['stok'] <= 0): ?>
                            <div class="out-of-stock-badge">
                                <span>HABIS</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Thumbnails -->
                    <?php
                    $thumbs = [];
                    $thumbs[] = $detail['gambar'];
                    if (!empty($detail['gambar_lain']) && is_array($detail['gambar_lain'])) {
                        $thumbs = array_merge($thumbs, $detail['gambar_lain']);
                    }
                    
                    if (count($thumbs) > 1):
                    ?>
                    <div class="thumbnail-container mt-4">
                        <h6 class="mb-3">Lihat foto lainnya:</h6>
                        <div class="thumbnails">
                            <?php foreach ($thumbs as $index => $g): ?>
                                <div class="thumbnail-item <?= $index === 0 ? 'active' : '' ?>" 
                                     onclick="changeMainImage('<?= $g ?>', this)">
                                    <img src="<?= $g ?>" 
                                         alt="Thumbnail <?= $index + 1 ?>"
                                         onerror="this.src='https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Info Produk -->
            <div class="col-lg-6">
                <div class="product-info-wrapper">
                    <!-- Nama dan Rating -->
                    <h1 class="product-title-detail"><?= htmlspecialchars($detail['nama']) ?></h1>
                    
                    <div class="product-meta mb-4">
                        <span class="product-category-detail">
                            <i class="fas fa-tag me-1"></i>
                            <?= htmlspecialchars($detail['kategori']) ?>
                        </span>
                        <span class="product-sku">
                            <i class="fas fa-hashtag me-1"></i>
                            SKU: <?= $detail['id'] ?>
                        </span>
                    </div>
                    
                    <!-- Rating -->
                    <?php if (!empty($detail['rating'])): ?>
                        <div class="rating-section mb-4">
                            <div class="stars">
                                <?php
                                $stars = floor($detail['rating']);
                                $hasHalf = ($detail['rating'] - $stars) >= 0.5;
                                for ($i = 1; $i <= 5; $i++):
                                    if ($i <= $stars):
                                ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif ($i == $stars + 1 && $hasHalf): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; endfor; ?>
                            </div>
                            <span class="rating-value"><?= number_format($detail['rating'], 1) ?></span>
                            <span class="rating-count">(<?= $detail['rating_count'] ?> ulasan)</span>
                            <a href="#reviews" class="view-reviews">Lihat ulasan</a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Harga -->
                    <div class="price-section mb-4">
                        <div class="current-price">
                            <span class="price-amount">Rp <?= number_format($detail['harga_diskon'], 0, ',', '.') ?></span>
                            <?php if ($diskon_persen > 0): ?>
                                <span class="discount-percentage">-<?= $diskon_persen ?>%</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($detail['harga_asli'] > $detail['harga_diskon']): ?>
                            <div class="original-price">
                                <span class="price-old-text">Rp <?= number_format($detail['harga_asli'], 0, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Stok -->
                    <div class="stock-info mb-4">
                        <?php if (isset($detail['stok']) && $detail['stok'] > 0): ?>
                            <div class="stock-available">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span class="stock-text">Stok Tersedia: <?= $detail['stok'] ?> unit</span>
                            </div>
                        <?php else: ?>
                            <div class="stock-unavailable">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                <span class="stock-text">Stok Habis</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Varian -->
                    <?php if (!empty($detail['varian']) && is_array($detail['varian'])): ?>
                        <div class="variants-section mb-4">
                            <?php if (!empty($detail['varian']['ukuran'])): ?>
                                <div class="variant-group mb-3">
                                    <h6 class="variant-title">Pilih Ukuran:</h6>
                                    <div class="size-options">
                                        <?php foreach ($detail['varian']['ukuran'] as $uk): ?>
                                            <label class="size-option">
                                                <input type="radio" name="size" value="<?= $uk ?>">
                                                <span class="size-label"><?= $uk ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($detail['varian']['warna'])): ?>
                                <div class="variant-group mb-3">
                                    <h6 class="variant-title">Pilih Warna:</h6>
                                    <div class="color-options">
                                        <?php foreach ($detail['varian']['warna'] as $wr): ?>
                                            <label class="color-option">
                                                <input type="radio" name="color" value="<?= $wr ?>">
                                                <span class="color-label" style="background-color: <?= getColorCode($wr) ?>"></span>
                                                <span class="color-name"><?= $wr ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Kuantitas -->
                    <div class="quantity-section mb-4">
                        <h6 class="quantity-title">Jumlah:</h6>
                        <div class="quantity-selector">
                            <button type="button" class="quantity-btn minus" onclick="decreaseQuantity()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="productQuantity" class="quantity-input" value="1" min="1" 
                                   max="<?= isset($detail['stok']) ? $detail['stok'] : 10 ?>">
                            <button type="button" class="quantity-btn plus" onclick="increaseQuantity()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                   <!-- ... Kode sebelumnya tetap sama sampai bagian Action Buttons ... -->

<!-- Tombol Aksi -->
<div class="action-buttons mb-5">
    <?php if (isset($detail['stok']) && $detail['stok'] > 0): ?>
        <form action="cart.php" method="POST" class="d-inline-block me-3">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?= $detail['id'] ?>">
            <input type="hidden" name="quantity" id="cartQuantity" value="1">
            <button type="submit" class="btn btn-cart">
                <i class="fas fa-shopping-cart me-2"></i>Tambah ke Keranjang
            </button>
        </form>
        
        <!-- Dropdown Metode Pembayaran untuk Beli Sekarang -->
        <div class="btn-group d-inline-block me-3">
            <button type="button" class="btn btn-buy-now dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bolt me-2"></i>Beli Sekarang
            </button>
            <ul class="dropdown-menu payment-methods-dropdown">
                <li>
                    <h6 class="dropdown-header">Pilih Metode Pembayaran:</h6>
                </li>
                
                <!-- Shopee -->
                <?php if (!empty($detail['shopee_url'])): ?>
                    <li>
                        <a class="dropdown-item payment-option" href="<?= $detail['shopee_url'] ?>" target="_blank">
                            <i class="fas fa-store me-2"></i>
                            <div class="payment-option-text">
                                <strong>Shopee</strong>
                                <small>Beli melalui Shopee</small>
                            </div>
                        </a>
                    </li>
                <?php endif; ?>
                
                <!-- Tokopedia -->
                <?php if (!empty($detail['tokopedia_url'])): ?>
                    <li>
                        <a class="dropdown-item payment-option" href="<?= $detail['tokopedia_url'] ?>" target="_blank">
                            <i class="fas fa-shopping-bag me-2"></i>
                            <div class="payment-option-text">
                                <strong>Tokopedia</strong>
                                <small>Beli melalui Tokopedia</small>
                            </div>
                        </a>
                    </li>
                <?php endif; ?>
                
                <!-- Crypto -->
                <li>
                    <a class="dropdown-item payment-option" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#cryptoModal">
                        <i class="fab fa-bitcoin me-2"></i>
                        <div class="payment-option-text">
                            <strong>Crypto</strong>
                            <small>Bayar dengan Crypto</small>
                        </div>
                    </a>
                </li>
                
                <!-- Transfer Bank -->
                <li>
                    <a class="dropdown-item payment-option" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#bankModal">
                        <i class="fas fa-university me-2"></i>
                        <div class="payment-option-text">
                            <strong>Transfer Bank</strong>
                            <small>Transfer via Bank</small>
                        </div>
                    </a>
                </li>
                
                <!-- WhatsApp -->
                <li>
                    <?php
                    $wa_nomor = "628113317077";
                    $product_name = urlencode($detail['nama']);
                    $product_price = number_format($detail['harga_diskon'], 0, ',', '.');
                    $wa_text = urlencode("Halo, saya ingin membeli produk:\n\n*$product_name*\nHarga: Rp $product_price\n\nBisa dibantu untuk pemesanan?");
                    $wa_link = "https://wa.me/$wa_nomor?text=$wa_text";
                    ?>
                    <a class="dropdown-item payment-option whatsapp" href="<?= $wa_link ?>" target="_blank">
                        <i class="fab fa-whatsapp me-2"></i>
                        <div class="payment-option-text">
                            <strong>WhatsApp</strong>
                            <small>Chat langsung via WA</small>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Tombol Wishlist -->
        <button class="btn btn-wishlist" onclick="toggleWishlist()">
            <i class="far fa-heart"></i>
        </button>
        <?php else: ?>
            <button class="btn btn-out-of-stock" disabled>
                <i class="fas fa-bell me-2"></i>Notifikasi Saat Stok Tersedia
        </button>
    <?php endif; ?>
</div>

                    </div>
                </div>
            </div>
        </div>        
        <!-- Deskripsi dan Spesifikasi -->
        <div class="row mt-5">
            <div class="col-lg-8">
                <div class="product-tabs">
                    <ul class="nav nav-tabs" id="productTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button">
                                <i class="fas fa-file-alt me-2"></i>Deskripsi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs" type="button">
                                <i class="fas fa-list-alt me-2"></i>Spesifikasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button">
                                <i class="fas fa-star me-2"></i>Ulasan
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-4" id="productTabContent">
                        <!-- Deskripsi -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <h4 class="mb-3">Deskripsi Produk</h4>
                            <?php if (!empty($detail['deskripsi'])): ?>
                                <p class="product-description"><?= nl2br(htmlspecialchars($detail['deskripsi'])) ?></p>
                            <?php else: ?>
                                <p class="text-muted">Deskripsi tidak tersedia untuk produk ini.</p>
                            <?php endif; ?>
                            
                            <?php if (!empty($detail['spesifikasi']) && is_array($detail['spesifikasi'])): ?>
                                <div class="features mt-4">
                                    <h5 class="mb-3">Fitur Utama:</h5>
                                    <ul class="feature-list">
                                        <?php foreach ($detail['spesifikasi'] as $feature): ?>
                                            <li><i class="fas fa-check text-success me-2"></i><?= $feature ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Spesifikasi -->
                        <div class="tab-pane fade" id="specs" role="tabpanel">
                            <h4 class="mb-3">Spesifikasi Teknis</h4>
                            <div class="specs-table">
                                <?php if (!empty($detail['spesifikasi_teknis']) && is_array($detail['spesifikasi_teknis'])): ?>
                                    <?php foreach ($detail['spesifikasi_teknis'] as $key => $value): ?>
                                        <div class="spec-row">
                                            <div class="spec-key"><?= $key ?></div>
                                            <div class="spec-value"><?= $value ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Spesifikasi teknis tidak tersedia.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Ulasan -->
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <h4 class="mb-3">Ulasan Pelanggan</h4>
                            <div class="reviews-summary mb-4">
                                <div class="average-rating">
                                    <div class="avg-rating-number"><?= number_format($detail['rating'], 1) ?></div>
                                    <div class="avg-rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= floor($detail['rating'])): ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($i == ceil($detail['rating']) && ($detail['rating'] - floor($detail['rating'])) >= 0.5): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="avg-rating-text">dari <?= $detail['rating_count'] ?> ulasan</div>
                                </div>
                            </div>
                            
                            <!-- Review Sample -->
                            <div class="review-sample">
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="reviewer-name">Budi Santoso</div>
                                            <div class="review-date">12 April 2024</div>
                                        </div>
                                        <div class="review-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <p>Produknya sangat bagus, kualitasnya sesuai dengan harga. Pengiriman cepat dan packing aman. Recommended!</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Info Pengiriman -->
            <div class="col-lg-4">
                <div class="delivery-info-card">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-truck me-2"></i>Info Pengiriman
                    </h5>
                    
                    <div class="delivery-option mb-3">
                        <div class="delivery-header">
                            <i class="fas fa-shipping-fast text-primary me-2"></i>
                            <span class="delivery-name">Reguler</span>
                        </div>
                        <div class="delivery-details">
                            <span class="delivery-time">3-5 hari kerja</span>
                            <span class="delivery-price">Rp 15.000</span>
                        </div>
                    </div>
                    
                    <div class="delivery-option mb-3">
                        <div class="delivery-header">
                            <i class="fas fa-rocket text-success me-2"></i>
                            <span class="delivery-name">Express</span>
                        </div>
                        <div class="delivery-details">
                            <span class="delivery-time">1-2 hari kerja</span>
                            <span class="delivery-price">Rp 25.000</span>
                        </div>
                    </div>
                    
                    <div class="delivery-note">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Gratis ongkir untuk pembelian minimal Rp 300.000</small>
                    </div>
                </div>
                
                <!-- Produk terkait (placeholder) -->
                <div class="related-products mt-4">
                    <h5 class="mb-3">Produk Terkait</h5>
                    <div class="related-product-item">
                        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                             alt="Produk terkait">
                        <div class="related-product-info">
                            <h6>Sepatu Sneakers Pria</h6>
                            <p class="price">Rp 299.000</p>
                        </div>
                    </div>
                    <div class="related-product-item">
                        <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                             alt="Produk terkait">
                        <div class="related-product-info">
                            <h6>Kaos Pria Cotton</h6>
                            <p class="price">Rp 89.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL PEMBAYARAN CRYPTO -->
    <div class="modal fade" id="cryptoModal" tabindex="-1" aria-labelledby="cryptoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cryptoModalLabel">
                        <i class="fab fa-bitcoin me-2"></i>Pembayaran Crypto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="cryptoPaymentForm" action="process_payment.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="payment_method" value="crypto">
                        <input type="hidden" name="product_id" value="<?= $detail['id'] ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($detail['nama']) ?>">
                        <input type="hidden" name="product_price" value="<?= $detail['harga_diskon'] ?>">
                        <input type="hidden" name="quantity" id="cryptoQuantity" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cryptoWallet" class="form-label">Jenis Wallet Crypto</label>
                                    <select class="form-select" id="cryptoWallet" name="crypto_wallet" required>
                                        <option value="">Pilih wallet</option>
                                        <option value="bitcoin">Bitcoin (BTC)</option>
                                        <option value="ethereum">Ethereum (ETH)</option>
                                        <option value="binance">Binance Coin (BNB)</option>
                                        <option value="tron">Tron (TRX)</option>
                                        <option value="solana">Solana (SOL)</option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cryptoAmount" class="form-label">Jumlah Crypto</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="cryptoAmount" name="crypto_amount" step="0.000001" required>
                                        <span class="input-group-text" id="cryptoCurrency">BTC</span>
                                    </div>
                                    <small class="text-muted">Konversi otomatis berdasarkan harga saat ini</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="cryptoAddress" class="form-label">Alamat Wallet Anda</label>
                                    <input type="text" class="form-control" id="cryptoAddress" name="crypto_address" 
                                           placeholder="Masukkan alamat wallet Anda" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="payment-info-box">
                                    <h6 class="mb-3">Informasi Pembayaran</h6>
                                    <p><strong>Produk:</strong> <?= htmlspecialchars($detail['nama']) ?></p>
                                    <p><strong>Harga:</strong> Rp <?= number_format($detail['harga_diskon'], 0, ',', '.') ?></p>
                                    <p><strong>Total:</strong> <span id="cryptoTotal">Rp <?= number_format($detail['harga_diskon'], 0, ',', '.') ?></span></p>
                                    
                                    <div class="crypto-address-box mt-3">
                                        <h6 class="mb-2">Kirim ke Alamat:</h6>
                                        <code class="crypto-address">bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh</code>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="copyCryptoAddress()">
                                            <i class="fas fa-copy me-1"></i>Salin
                                        </button>
                                    </div>
                                    
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Konfirmasi pembayaran maksimal 1x24 jam setelah transfer
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">Data Pengiriman</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fullName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phoneNumber" class="form-label">Nomor HP</label>
                                <input type="tel" class="form-control" id="phoneNumber" name="phone_number" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="province" class="form-label">Provinsi</label>
                                <input type="text" class="form-control" id="province" name="province" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Kota/Kabupaten</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="postalCode" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" id="postalCode" name="postal_code" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="paymentProof" class="form-label">Upload Bukti Pembayaran</label>
                            <input type="file" class="form-control" id="paymentProof" name="payment_proof" accept="image/*,.pdf" required>
                            <small class="text-muted">Format: JPG, PNG, PDF (maks. 5MB)</small>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsCrypto" required>
                            <label class="form-check-label" for="termsCrypto">
                                Saya menyetujui <a href="#">syarat dan ketentuan</a> yang berlaku
                            </label>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-lock me-2"></i>Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TRANSFER BANK -->
    <div class="modal fade" id="bankModal" tabindex="-1" aria-labelledby="bankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bankModalLabel">
                        <i class="fas fa-university me-2"></i>Transfer Bank
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bankPaymentForm" action="process_payment.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="payment_method" value="bank_transfer">
                        <input type="hidden" name="product_id" value="<?= $detail['id'] ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($detail['nama']) ?>">
                        <input type="hidden" name="product_price" value="<?= $detail['harga_diskon'] ?>">
                        <input type="hidden" name="quantity" id="bankQuantity" value="1">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bankName" class="form-label">Pilih Bank</label>
                                    <select class="form-select" id="bankName" name="bank_name" required>
                                        <option value="">Pilih bank</option>
                                        <option value="bca">BCA</option>
                                        <option value="mandiri">Mandiri</option>
                                        <option value="bni">BNI</option>
                                        <option value="bri">BRI</option>
                                        <option value="cimb">CIMB Niaga</option>
                                        <option value="permata">Permata</option>
                                        <option value="other">Bank Lainnya</option>
                                    </select>
                                </div>
                                
                                <div class="bank-info-box mb-4">
                                    <h6 class="mb-2">Informasi Rekening</h6>
                                    <p><strong>Bank:</strong> <span id="selectedBank">BCA</span></p>
                                    <p><strong>No. Rekening:</strong> <span id="bankAccount">1234567890</span></p>
                                    <p><strong>Atas Nama:</strong> <span id="accountName">SECURITY77 STORE</span></p>
                                    
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="copyBankInfo()">
                                        <i class="fas fa-copy me-1"></i>Salin Info Rekening
                                    </button>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Harap transfer sesuai nominal total pembayaran
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="payment-info-box">
                                    <h6 class="mb-3">Rincian Pembayaran</h6>
                                    <div class="payment-details">
                                        <div class="detail-row">
                                            <span>Produk:</span>
                                            <span><?= htmlspecialchars($detail['nama']) ?></span>
                                        </div>
                                        <div class="detail-row">
                                            <span>Harga:</span>
                                            <span>Rp <?= number_format($detail['harga_diskon'], 0, ',', '.') ?></span>
                                        </div>
                                        <div class="detail-row">
                                            <span>Jumlah:</span>
                                            <span id="bankQtyDisplay">1</span>
                                        </div>
                                        <div class="detail-row">
                                            <span>Ongkir:</span>
                                            <span>Rp 15.000</span>
                                        </div>
                                        <hr>
                                        <div class="detail-row total">
                                            <strong>Total:</strong>
                                            <strong id="bankTotal">Rp <?= number_format($detail['harga_diskon'] + 15000, 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6>Kode Unik: <span class="text-primary"><?= rand(100, 999) ?></span></h6>
                                        <small class="text-muted">Tambahkan kode unik untuk mempermudah verifikasi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Data Pengiriman (sama seperti crypto) -->
                        <h5 class="mb-3">Data Pengiriman</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bankFullName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="bankFullName" name="full_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bankPhoneNumber" class="form-label">Nomor HP</label>
                                <input type="tel" class="form-control" id="bankPhoneNumber" name="phone_number" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bankEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="bankEmail" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bankAddress" class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" id="bankAddress" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="bankProvince" class="form-label">Provinsi</label>
                                <input type="text" class="form-control" id="bankProvince" name="province" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bankCity" class="form-label">Kota/Kabupaten</label>
                                <input type="text" class="form-control" id="bankCity" name="city" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="bankPostalCode" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" id="bankPostalCode" name="postal_code" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bankPaymentProof" class="form-label">Upload Bukti Transfer</label>
                            <input type="file" class="form-control" id="bankPaymentProof" name="payment_proof" accept="image/*,.pdf" required>
                            <small class="text-muted">Format: JPG, PNG, PDF (maks. 5MB)</small>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsBank" required>
                            <label class="form-check-label" for="termsBank">
                                Saya menyetujui <a href="#">syarat dan ketentuan</a> yang berlaku
                            </label>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Security77Store</h5>
                    <p>E-commerce terpercaya dengan produk fashion berkualitas tinggi dan harga terbaik.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="footer-link">Home</a></li>
                        <li><a href="#" class="footer-link">Produk</a></li>
                        <li><a href="#" class="footer-link">Kategori</a></li>
                        <li><a href="#" class="footer-link">Promo</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Bantuan</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Cara Belanja</a></li>
                        <li><a href="#" class="footer-link">Pengiriman</a></li>
                        <li><a href="#" class="footer-link">Pembayaran</a></li>
                        <li><a href="#" class="footer-link">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i>0811-3317-077</li>
                        <li><i class="fas fa-envelope me-2"></i>info@security77store.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Surabaya, Jawa Timur</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; <?= date("Y") ?> Security77 Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Fungsi untuk helper
        <?php
        function getColorCode($colorName) {
            $colors = [
                'Merah' => '#dc3545',
                'Biru' => '#007bff',
                'Hijau' => '#28a745',
                'Kuning' => '#ffc107',
                'Hitam' => '#212529',
                'Putih' => '#ffffff',
                'Abu-abu' => '#6c757d',
                'Coklat' => '#8b4513',
                'Ungu' => '#6f42c1',
                'Pink' => '#e83e8c'
            ];
            return isset($colors[$colorName]) ? $colors[$colorName] : '#e83e8c';
        }
        ?>
        
        // Ubah gambar utama
        function changeMainImage(src, element) {
            document.getElementById('mainProductImage').src = src;
            
            // Hapus class active dari semua thumbnail
            document.querySelectorAll('.thumbnail-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Tambah class active ke thumbnail yang diklik
            element.classList.add('active');
        }
        
        // Kontrol kuantitas
        let quantity = 1;
        const maxQuantity = <?= isset($detail['stok']) ? $detail['stok'] : 10 ?>;
        
        function increaseQuantity() {
            if (quantity < maxQuantity) {
                quantity++;
                updateQuantity();
            }
        }
        
        function decreaseQuantity() {
            if (quantity > 1) {
                quantity--;
                updateQuantity();
            }
        }
        
        function updateQuantity() {
            const quantityInput = document.getElementById('productQuantity');
            const cartQuantity = document.getElementById('cartQuantity');
            const cryptoQuantity = document.getElementById('cryptoQuantity');
            const bankQuantity = document.getElementById('bankQuantity');
            const bankQtyDisplay = document.getElementById('bankQtyDisplay');
            
            quantityInput.value = quantity;
            if (cartQuantity) cartQuantity.value = quantity;
            if (cryptoQuantity) cryptoQuantity.value = quantity;
            if (bankQuantity) bankQuantity.value = quantity;
            if (bankQtyDisplay) bankQtyDisplay.textContent = quantity;
            
            // Update total
            updateTotals();
        }
        
        // Update total harga
        function updateTotals() {
            const price = <?= $detail['harga_diskon'] ?>;
            const shipping = 15000;
            const total = (price * quantity) + shipping;
            
            // Update crypto total
            const cryptoTotal = document.getElementById('cryptoTotal');
            if (cryptoTotal) {
                cryptoTotal.textContent = 'Rp ' + formatNumber(total);
            }
            
            // Update bank total
            const bankTotal = document.getElementById('bankTotal');
            if (bankTotal) {
                bankTotal.textContent = 'Rp ' + formatNumber(total);
            }
        }
        
        // Format number dengan titik
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Salin alamat crypto
        function copyCryptoAddress() {
            const cryptoAddress = document.querySelector('.crypto-address').textContent;
            navigator.clipboard.writeText(cryptoAddress).then(() => {
                alert('Alamat crypto berhasil disalin!');
            });
        }
        
        // Salin info bank
        function copyBankInfo() {
            const bankInfo = `Bank: ${document.getElementById('selectedBank').textContent}\n` +
                           `No. Rekening: ${document.getElementById('bankAccount').textContent}\n` +
                           `Atas Nama: ${document.getElementById('accountName').textContent}`;
            
            navigator.clipboard.writeText(bankInfo).then(() => {
                alert('Info rekening berhasil disalin!');
            });
        }
        
        // Ubah currency crypto
        document.getElementById('cryptoWallet')?.addEventListener('change', function() {
            const currencyMap = {
                'bitcoin': 'BTC',
                'ethereum': 'ETH',
                'binance': 'BNB',
                'tron': 'TRX',
                'solana': 'SOL',
                'other': 'CRYPTO'
            };
            
            const currency = currencyMap[this.value] || 'BTC';
            document.getElementById('cryptoCurrency').textContent = currency;
        });
        
        // Ubah info bank berdasarkan pilihan
        document.getElementById('bankName')?.addEventListener('change', function() {
            const bankInfo = {
                'bca': { name: 'BCA', account: '1234567890', holder: 'SECURITY77 STORE' },
                'mandiri': { name: 'Mandiri', account: '0987654321', holder: 'SECURITY77 STORE' },
                'bni': { name: 'BNI', account: '5678901234', holder: 'SECURITY77 STORE' },
                'bri': { name: 'BRI', account: '4321098765', holder: 'SECURITY77 STORE' },
                'cimb': { name: 'CIMB Niaga', account: '6789012345', holder: 'SECURITY77 STORE' },
                'permata': { name: 'Permata', account: '5432109876', holder: 'SECURITY77 STORE' },
                'other': { name: 'Bank Lainnya', account: 'Konfirmasi via WA', holder: 'SECURITY77 STORE' }
            };
            
            const info = bankInfo[this.value] || bankInfo['bca'];
            document.getElementById('selectedBank').textContent = info.name;
            document.getElementById('bankAccount').textContent = info.account;
            document.getElementById('accountName').textContent = info.holder;
        });
        
        // Toggle wishlist
        function toggleWishlist() {
            const wishlistBtn = document.querySelector('.btn-wishlist');
            const icon = wishlistBtn.querySelector('i');
            
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                wishlistBtn.classList.add('active');
                showToast('Produk ditambahkan ke wishlist!');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                wishlistBtn.classList.remove('active');
                showToast('Produk dihapus dari wishlist!');
            }
        }
        
        // Toast notification
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.textContent = message;
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
        
        // Update totals on load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotals();
        });
    </script>
</body>
</html>