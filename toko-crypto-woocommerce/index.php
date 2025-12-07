<?php
session_start();

// Load Produk dari JSON
$data = file_get_contents("product.json");
$produk = json_decode($data, true);

// Search
$keyword = isset($_GET["search"]) ? strtolower($_GET["search"]) : "";
$filterKategori = isset($_GET["kategori"]) ? $_GET["kategori"] : "";

// Filter by search
if ($keyword !== "") {
    $hasil = [];
    foreach ($produk as $p) {
        if (
            strpos(strtolower($p["nama"]), $keyword) !== false ||
            strpos(strtolower($p["kategori"]), $keyword) !== false
        ) {
            $hasil[] = $p;
        }
    }
    $produk = $hasil;
}

// Filter kategori
if ($filterKategori !== "") {
    $filtered = [];
    foreach ($produk as $p) {
        if (strtolower($p["kategori"]) === strtolower($filterKategori)) {
            $filtered[] = $p;
        }
    }
    $produk = $filtered;
}

// Count cart
$cart_count = isset($_SESSION["cart"]) ? array_sum(array_column($_SESSION["cart"], "qty")) : 0;

// Ambil kategori unik untuk filter
$kategori_unik = [];
foreach (json_decode($data, true) as $p) {
    if (!in_array($p["kategori"], $kategori_unik)) {
        $kategori_unik[] = $p["kategori"];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security77 Store - E-Commerce Modern</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
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
                <form class="d-flex mx-auto my-2 my-lg-0 w-100" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control nav-search" placeholder="Cari produk terbaik..." name="search" value="<?= htmlspecialchars($keyword) ?>">
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

    <!-- BANNER SLIDER -->
    <div class="container mt-4">
        <div id="mainBanner" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#mainBanner" data-bs-slide-to="2"></button>
            </div>
            
            <div class="carousel-inner rounded-4">
                <div class="carousel-item active">
                    <img src="img/diskon.png" class="banner-img" alt="Diskon Spesial" onerror="this.src='https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'">
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="banner-img" alt="Sepatu Pria">
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1523381210434-271e8be1f52b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="banner-img" alt="Fashion Terbaru">
                </div>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#mainBanner" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainBanner" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>

    <!-- CATEGORY FILTER -->
    <div class="container mt-5">
        <div class="category-filter">
            <h5 class="mb-3">Filter Kategori</h5>
            <div class="d-flex flex-wrap">
                <a href="index.php" class="btn category-btn <?= $filterKategori === '' ? 'active' : '' ?>">
                    Semua Produk
                </a>
                <?php foreach ($kategori_unik as $kategori): ?>
                    <a href="?kategori=<?= urlencode($kategori) ?>" class="btn category-btn <?= strtolower($filterKategori) === strtolower($kategori) ? 'active' : '' ?>">
                        <?= htmlspecialchars($kategori) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- PRODUCTS SECTION -->
    <div class="container py-5">
        <!-- HEADER -->
        <div class="section-header">
            <h2>Produk Terbaru</h2>
            <p class="text-muted mt-2">Temukan produk terbaik dengan harga spesial hanya untuk Anda</p>
        </div>

        <!-- PRODUCT GRID -->
        <div class="row">
            <?php if (empty($produk)): ?>
                <div class="col-12">
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <h4>Produk tidak ditemukan</h4>
                        <p class="text-muted">Coba gunakan kata kunci lain atau lihat kategori produk lainnya</p>
                        <a href="index.php" class="btn btn-primary mt-3">Lihat Semua Produk</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($produk as $index => $p): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 animate-fade-in-up delay-<?= ($index % 3) + 1 ?>">
                    <div class="product-card">
                        <div class="product-image">
                            <?php if($p['harga_diskon'] < $p['harga_asli']): ?>
                                <div class="product-badge">DISKON</div>
                            <?php endif; ?>
                            <img src="<?= $p['gambar'] ?>" alt="<?= $p['nama'] ?>" onerror="this.src='https://images.unsplash.com/photo-1559056199-641a0ac8b55e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'">
                        </div>
                        
                        <div class="product-body">
                            <h5 class="product-title"><?= htmlspecialchars($p['nama']) ?></h5>
                            <p class="product-cat"><?= htmlspecialchars($p['kategori']) ?></p>
                            
                            <div class="price-wrap">
                                <span class="price-new">Rp <?= number_format($p['harga_diskon']) ?></span>
                                <?php if($p['harga_diskon'] < $p['harga_asli']): ?>
                                    <span class="price-old">Rp <?= number_format($p['harga_asli']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <a href="product.php?id=<?= $p['id'] ?>" class="btn product-btn">
                                <i class="fas fa-eye me-2"></i>Detail Produk
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($produk)): ?>
            <div class="text-center mt-5">
                <a href="#" class="btn btn-outline-primary px-5 py-3">
                    <i class="fas fa-arrow-down me-2"></i>Lihat Lebih Banyak
                </a>
            </div>
        <?php endif; ?>
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
                        <li><a href="#" class="footer-link">Tentang Kami</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Kategori</h5>
                    <ul class="list-unstyled">
                        <?php foreach (array_slice($kategori_unik, 0, 5) as $kategori): ?>
                            <li><a href="?kategori=<?= urlencode($kategori) ?>" class="footer-link"><?= htmlspecialchars($kategori) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i>0811-3317-077</li>
                        <li><i class="fas fa-envelope me-2"></i>info@security77store.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Surabaya, Jawa Timur</li>
                    </ul>
                    <div class="mt-4">
                        <h6>Metode Pembayaran</h6>
                        <div class="d-flex mt-2">
                            <div class="payment-method me-2"></div>
                            <div class="payment-method me-2"></div>
                            <div class="payment-method"></div>
                        </div>
                    </div>
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
        // Auto slide banner
        document.addEventListener('DOMContentLoaded', function() {
            const myCarousel = document.getElementById('mainBanner');
            const carousel = new bootstrap.Carousel(myCarousel, {
                interval: 3000,
                wrap: true
            });
            
            // Add animation to product cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in-up');
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.product-card').forEach(card => {
                observer.observe(card);
            });
            
            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
        
        // Cart count animation
        function animateCartCount() {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    cartCount.style.transform = 'scale(1)';
                }, 300);
            }
        }
        
        // Search form submission
        document.querySelector('form')?.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput.value.trim() === '') {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>