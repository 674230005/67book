<?php

require 'includes/bootstrap.php';

auth();

$_SESSION['cart'] = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($a === 'add') {
        $b = $books->find($id);

        if ($b && $b['stock'] > 0) {
            $_SESSION['cart'][$id] = min(
                (int) $b['stock'],
                ($_SESSION['cart'][$id] ?? 0) + max(1, (int) ($_POST['qty'] ?? 1))
            );
        }
    }

    if ($a === 'update') {
        foreach ($_POST['qty'] ?? [] as $i => $n) {
            $b = $books->find((int) $i);

            if (!$b || (int) $n < 1) {
                unset($_SESSION['cart'][$i]);
            } else {
                $_SESSION['cart'][$i] = min(
                    (int) $b['stock'],
                    (int) $n
                );
            }
        }
    }

    if ($a === 'remove') {
        unset($_SESSION['cart'][$id]);
    }

    header('Location: cart.php');
    exit;
}

$items = [];
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $b = $books->find((int) $id);

    if ($b) {
        $b['qty'] = $qty;
        $b['sub'] = $qty * $b['price'];

        $total += $b['sub'];
        $items[] = $b;
    }
}

$title = 'ตะกร้าสินค้า';

require 'includes/header.php';
?>

<small>YOUR BAG</small>

<h1>ตะกร้าสินค้า</h1>

<?php if ($items): ?>

    <form method="post">
        <input type="hidden" name="action" value="update">

        <?php foreach ($items as $b): ?>
            <div class="cartrow">
                <img src="assets/images/<?= e($b['cover']) ?>">

                <div>
                    <b><?= e($b['title']) ?></b>

                    <p>฿<?= money($b['price']) ?></p>
                </div>

                <input
                    type="number"
                    min="0"
                    max="<?= $b['stock'] ?>"
                    name="qty[<?= $b['id'] ?>]"
                    value="<?= $b['qty'] ?>"
                >

                <b>฿<?= money($b['sub']) ?></b>

                <button
                    type="submit"
                    formaction="cart.php"
                    formmethod="post"
                    name="action"
                    value="remove"
                    onclick="this.form.insertAdjacentHTML('beforeend', '<input type=hidden name=id value=<?= $b['id'] ?>>')"
                >
                    ×
                </button>
            </div>
        <?php endforeach; ?>

        <button class="btn outline">
            อัปเดตตะกร้า
        </button>

        <div class="total">
            ยอดรวม

            <b>฿<?= money($total) ?></b>

            <a class="btn" href="checkout.php">
                ดำเนินการสั่งซื้อ ↗
            </a>
        </div>
    </form>

<?php else: ?>

    <div class="empty">
        <h2>ตะกร้ายังว่างอยู่</h2>

        <a class="btn" href="index.php">
            เลือกซื้อหนังสือ
        </a>
    </div>

<?php endif; ?>

<?php require 'includes/footer.php'; ?>
