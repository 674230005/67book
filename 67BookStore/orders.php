<?php

require 'includes/bootstrap.php';

auth();

$list = $orders->history((int) $_SESSION['uid']);

$title = 'ประวัติคำสั่งซื้อ';

require 'includes/header.php';
?>

<small>ORDER HISTORY</small>

<h1>ประวัติคำสั่งซื้อ</h1>

<?php if (!$list): ?>
    <div class="empty">
        ยังไม่มีคำสั่งซื้อ

        <p>
            <a class="btn" href="index.php">
                เลือกซื้อหนังสือ
            </a>
        </p>
    </div>
<?php endif; ?>

<?php foreach ($list as $o): ?>
    <a class="order" href="order_detail.php?id=<?= $o['id'] ?>">
        <span>
            คำสั่งซื้อ #<?= $o['id'] ?>

            <small>
                <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                · <?= e($o['status']) ?>
            </small>
        </span>

        <b>
            ฿<?= money($o['total']) ?> ↗
        </b>
    </a>
<?php endforeach; ?>

<?php require 'includes/footer.php'; ?>
