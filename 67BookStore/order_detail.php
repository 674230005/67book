<?php

require 'includes/bootstrap.php';

auth();

$o = $orders->find(
    (int) ($_GET['id'] ?? 0),
    (int) $_SESSION['uid']
);

if (!$o) {
    exit('ไม่พบคำสั่งซื้อ');
}

$items = $orders->items((int) $o['id']);

$title = 'รายละเอียดคำสั่งซื้อ';

require 'includes/header.php';
?>

<small>ORDER #<?= $o['id'] ?></small>

<h1>รายละเอียดคำสั่งซื้อ</h1>

<p>
    วันที่ <?= e($o['created_at']) ?>
    · <?= e($o['status']) ?>
</p>

<div class="panel">

    <?php foreach ($items as $i): ?>
        <div class="summary">
            <span>
                <?= e($i['book_title']) ?>
                × <?= $i['quantity'] ?>
            </span>

            <b>
                ฿<?= money($i['price'] * $i['quantity']) ?>
            </b>
        </div>
    <?php endforeach; ?>

    <div class="summary">
        <b>ยอดรวม</b>

        <b>
            ฿<?= money($o['total']) ?>
        </b>
    </div>

</div>

<a href="orders.php">
    ← ประวัติคำสั่งซื้อ
</a>

<?php require 'includes/footer.php'; ?>
