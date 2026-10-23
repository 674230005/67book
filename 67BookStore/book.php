<?php

require 'includes/bootstrap.php';

auth();

$b = $books->find((int) ($_GET['id'] ?? 0));

if (!$b) {
    exit('ไม่พบหนังสือ');
}

$title = $b['title'];

require 'includes/header.php';
?>

<section class="detail">
    <img src="assets/images/<?= e($b['cover']) ?>">

    <div>
        <small>
            <?= e($b['category']) ?> / BOOK DETAILS
        </small>

        <h1><?= e($b['title']) ?></h1>

        <p class="muted">
            <?= e($b['author']) ?>
        </p>

        <h2>฿<?= money($b['price']) ?></h2>

        <p><?= e($b['description']) ?></p>

        <p>
            คงเหลือ <?= $b['stock'] ?> เล่ม
        </p>

        <form method="post" action="cart.php">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?= $b['id'] ?>">

            <label>
                จำนวน
                <input
                    type="number"
                    name="qty"
                    value="1"
                    min="1"
                    max="<?= $b['stock'] ?>"
                >
            </label>

            <button class="btn">
                เพิ่มลงตะกร้า ↗
            </button>
        </form>

        <p>
            <a href="index.php">← กลับหน้าร้าน</a>
        </p>
    </div>
</section>

<?php require 'includes/footer.php'; ?>