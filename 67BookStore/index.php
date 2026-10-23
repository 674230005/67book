<?php

require 'includes/bootstrap.php';

auth();

$q = trim($_GET['q'] ?? '');
$cat = $_GET['category'] ?? '';

$list = $books->all($q, $cat);

$title = 'หน้าร้าน';

require 'includes/header.php';
?>

<section class="hero">
    <small>THE COLLECTION / 67 BOOKSTORE</small>

    <h1>
        เปิดหน้าถัดไป<br>
        <em>ของเรื่องราวที่คุณรัก</em>
    </h1>

    <p>
        เลือกนิยายและมังงะเล่มโปรด
        เติมชั้นหนังสือของคุณให้เต็มไปด้วยเรื่องราว
    </p>
</section>

<div class="heading">
    <div>
        <small>BOOKSHELF</small>
        <h2>เลือกเล่มที่ใช่</h2>
    </div>

    <span><?= count($list) ?> รายการ</span>
</div>

<form class="filter">
    <input
        name="q"
        value="<?= e($q) ?>"
        placeholder="ค้นหาชื่อหนังสือ..."
    >

    <select name="category">
        <option value="">ทุกหมวด</option>

        <option value="นิยาย" <?= $cat === 'นิยาย' ? 'selected' : '' ?>>
            นิยาย
        </option>

        <option value="มังงะ" <?= $cat === 'มังงะ' ? 'selected' : '' ?>>
            มังงะ
        </option>
    </select>

    <button class="btn">ค้นหา</button>
</form>

<div class="grid">

    <?php foreach ($list as $b): ?>
        <article class="card">

            <a class="cover" href="book.php?id=<?= $b['id'] ?>">
                <img
                    src="assets/images/<?= e($b['cover']) ?>"
                    alt="<?= e($b['title']) ?>"
                >
            </a>

            <div class="cardbody">
                <small class="tag">
                    <?= e($b['category']) ?>
                </small>

                <h3>
                    <a href="book.php?id=<?= $b['id'] ?>">
                        <?= e($b['title']) ?>
                    </a>
                </h3>

                <p class="muted">
                    <?= e($b['author']) ?>
                </p>

                <div class="price">
                    ฿<?= money($b['price']) ?>
                </div>

                <p class="muted">
                    คงเหลือ <?= $b['stock'] ?> เล่ม
                </p>

                <form method="post" action="cart.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?= $b['id'] ?>">

                    <button
                        class="btn outline"
                        <?= $b['stock'] < 1 ? 'disabled' : '' ?>
                    >
                        + เพิ่มลงตะกร้า
                    </button>
                </form>
            </div>

        </article>
    <?php endforeach; ?>

</div>

<?php require 'includes/footer.php'; ?>
