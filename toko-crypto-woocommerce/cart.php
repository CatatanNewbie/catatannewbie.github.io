<?php
session_start();

// Load produk
$data = file_get_contents("product.json");
$produk = json_decode($data, true);

// Index produk by id
$produk_by_id = [];
foreach ($produk as $p) {
    $produk_by_id[$p['id']] = $p;
}

// Inisialisasi cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle aksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $id = intval($_POST['id']);
        if (isset($produk_by_id[$id])) {
            // Kalau sudah ada di cart -> +1
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] += 1;
            } else {
                $_SESSION['cart'][$id] = [
                    'id' => $id,
                    'qty' => 1
                ];
            }
        }
    } elseif ($action === 'remove') {
        $id = intval($_POST['id']);
        unset($_SESSION['cart'][$id]);
    }
}

// Hitung total
$total = 0;
$cart_items = [];

foreach ($_SESSION['cart'] as $item) {
    $id = $item['id'];
    if (isset($produk_by_id[$id])) {
        $prod = $produk_by_id[$id];
        $subtotal = $prod['harga_diskon'] * $item['qty'];
        $total += $subtotal;
        $cart_items[] = [
            'id' => $id,
            'nama' => $prod['nama'],
            'harga' => $prod['harga_diskon'],
            'qty' => $item['qty'],
            'subtotal' => $subtotal
        ];
    }
}

// Link Checkout WA semua isi cart
$wa_nomor = "628113317077"; // ganti nomor WA kamu
$text = "Halo, saya ingin checkout pesanan berikut:%0A%0A";
foreach ($cart_items as $ci) {
    $text .= "- " . $ci['nama'] . " x " . $ci['qty'] . " @ Rp " . number_format($ci['harga'],0,',','.') .
             " = Rp " . number_format($ci['subtotal'],0,',','.') . "%0A";
}
$text .= "%0ATotal: Rp " . number_format($total,0,',','.');
$wa_link = "https://wa.me/$wa_nomor?text=$text";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keranjang Belanja</title>
<link rel="stylesheet" href="style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4 section" style="background:#12151c; border-radius:12px; padding:20px;">
    <h3>Keranjang Belanja</h3>
    <a href="index.php" class="btn btn-sm btn-secondary mb-3">← Kembali ke Toko</a>

    <?php if (empty($cart_items)): ?>
        <p>Keranjang masih kosong.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $ci): ?>
                    <tr>
                        <td><?= $ci['nama'] ?></td>
                        <td><?= $ci['qty'] ?></td>
                        <td>Rp <?= number_format($ci['harga'],0,',','.') ?></td>
                        <td>Rp <?= number_format($ci['subtotal'],0,',','.') ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="id" value="<?= $ci['id'] ?>">
                                <button class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th colspan="2">Rp <?= number_format($total,0,',','.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <a href="<?= $wa_link ?>" target="_blank" class="btn btn-success w-100 mt-3">
            Checkout via WhatsApp
        </a>
    <?php endif; ?>
</div>

</body>
</html>
