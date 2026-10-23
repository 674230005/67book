<?php

$title = $title ?? '67BookStore';

?>

<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($title) ?> | 67BookStore</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <header>
        <a class="logo" href="index.php">
            67 <span>BOOKSTORE</span>
        </a>

        <nav>
            <a href="index.php">หน้าร้าน</a>

            <a href="cart.php">
                ตะกร้า (<?= array_sum($_SESSION['cart'] ?? []) ?>)
            </a>

            <a href="orders.php">ประวัติสั่งซื้อ</a>

            <?php if (($_SESSION['role'] ?? 'user') === 'admin'): ?>
                <!-- แสดงเมนูจัดการหนังสือเฉพาะบัญชี Admin -->
                <a href="manage_books.php">จัดการหนังสือ</a>
            <?php endif; ?>

            <span class="muted">
                <?= e($_SESSION['username'] ?? '') ?>
                (<?= ($_SESSION['role'] ?? 'user') === 'admin' ? 'Admin' : 'ผู้ใช้' ?>)
            </span>
            <a href="logout.php">ออกจากระบบ</a>
        </nav>
    </header>

    <main class="wrap">