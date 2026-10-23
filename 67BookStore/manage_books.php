<?php

require 'includes/bootstrap.php';

// หน้าจัดการหนังสือเป็นสิทธิ์เฉพาะผู้ดูแลระบบเท่านั้น
require_admin();

$covers = [
    'thara-vol1.png',
    'thara-vol2.png',
    'dang-sila-vol1.png',
    'dang-sila-vol2.png',
    'dang-sila-vol3.png',
    'witcher-vol1.png',
    'gokurakugai-vol1.png',
    'gokurakugai-vol2.png',
    'ดั่งศิราซ้อนหยก4.jpg',
    'The Devil In Me.jpg',
];

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $a = $_POST['action'];

        // ลบหนังสือได้เฉพาะ Admin เพราะผ่าน require_admin() แล้ว
        if ($a === 'delete') {
            $books->delete((int) $_POST['id']);
        } else {
            $d = [
                'title' => trim($_POST['title']),
                'author' => trim($_POST['author']),
                'category' => $_POST['category'],
                'price' => (float) $_POST['price'],
                'cover' => in_array($_POST['cover'], $covers)
                    ? $_POST['cover']
                    : $covers[0],
                'description' => trim($_POST['description']),
                'stock' => max(0, (int) $_POST['stock'])
            ];

            if (!$d['title'] || $d['price'] <= 0) {
                throw new RuntimeException(
                    'กรอกชื่อและราคาที่ถูกต้อง'
                );
            }

            // เลือกเพิ่มหนังสือใหม่หรือแก้ไขข้อมูลเดิมตาม action ที่ส่งมา
            if ($a === 'create') {
                $books->create($d);
            } else {
                $books->update((int) $_POST['id'], $d);
            }
        }

        header('Location: manage_books.php');
        exit;

    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$edit = isset($_GET['edit'])
    ? $books->find((int) $_GET['edit'])
    : null;

$list = $books->all();

$title = 'จัดการหนังสือ';

require 'includes/header.php';
?>

<small>BOOK INVENTORY</small>

<h1>จัดการหนังสือ (CRUD)</h1>

<?php if ($err): ?>
    <p class="error">
        <?= e($err) ?>
    </p>
<?php endif; ?>

<section class="panel">
    <h2>
        <?= $edit ? 'แก้ไขหนังสือ' : 'เพิ่มหนังสือใหม่' ?>
    </h2>

    <form method="post" class="formgrid">
        <input
            type="hidden"
            name="action"
            value="<?= $edit ? 'update' : 'create' ?>"
        >

        <?php if ($edit): ?>
            <input
                type="hidden"
                name="id"
                value="<?= $edit['id'] ?>"
            >
        <?php endif; ?>

        <label>
            ชื่อหนังสือ
            <input
                name="title"
                required
                value="<?= e($edit['title'] ?? '') ?>"
            >
        </label>

        <label>
            ผู้แต่ง
            <input
                name="author"
                value="<?= e($edit['author'] ?? 'ไม่ระบุ') ?>"
            >
        </label>

        <label>
            หมวด
            <select name="category">
                <option
                    <?= ($edit['category'] ?? 'นิยาย') === 'นิยาย'
                        ? 'selected'
                        : '' ?>
                >นิยาย</option>

                <option
                    <?= ($edit['category'] ?? '') === 'มังงะ'
                        ? 'selected'
                        : '' ?>
                >มังงะ</option>
            </select>
        </label>

        <label>
            ราคา
            <input
                type="number"
                min="1"
                step=".01"
                name="price"
                required
                value="<?= e($edit['price'] ?? '') ?>"
            >
        </label>

        <label>
            สต็อก
            <input
                type="number"
                min="0"
                name="stock"
                value="<?= e($edit['stock'] ?? 10) ?>"
            >
        </label>

        <label>
            รูปปก
            <select name="cover">
                <?php foreach ($covers as $c): ?>
                    <option
                        <?= ($edit['cover'] ?? '') === $c
                            ? 'selected'
                            : '' ?>
                    >
                        <?= e($c) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="full">
            รายละเอียด
            <textarea name="description"><?= e($edit['description'] ?? '') ?></textarea>
        </label>

        <div class="full">
            <button class="btn">บันทึก</button>

            <?php if ($edit): ?>
                <a class="btn outline" href="manage_books.php">
                    ยกเลิก
                </a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="panel">
    <h2>หนังสือทั้งหมด (<?= count($list) ?>)</h2>

    <?php foreach ($list as $b): ?>
        <div class="manage">
            <img src="assets/images/<?= e($b['cover']) ?>">

            <span>
                <?= e($b['title']) ?>

                <small>
                    ฿<?= money($b['price']) ?>
                    · เหลือ <?= $b['stock'] ?>
                </small>
            </span>

            <a class="btn outline" href="?edit=<?= $b['id'] ?>">
                แก้ไข
            </a>

            <form
                method="post"
                onsubmit="return confirm('ยืนยันลบ?')"
            >
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">

                <button class="btn danger">ลบ</button>
            </form>
        </div>
    <?php endforeach; ?>
</section>

<?php require 'includes/footer.php'; ?>
