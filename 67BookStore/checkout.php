<?php
// โหลดการเชื่อมต่อฐานข้อมูลและคลาสของระบบร้านหนังสือ
require 'includes/bootstrap.php';

// บังคับให้ผู้ใช้เข้าสู่ระบบก่อนยืนยันคำสั่งซื้อ
auth();

// หากไม่มีสินค้าในตะกร้า ให้กลับไปหน้าตะกร้า
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$err = '';
$cartBooks = [];
$total = 0.0;

// ดึงรายละเอียดหนังสือจากฐานข้อมูลเพื่อแสดงชื่อ ปก ผู้แต่ง จำนวน และราคา
foreach ($_SESSION['cart'] as $bookId => $quantity) {
    $book = $books->find((int) $bookId);
    if (!$book) {
        continue;
    }

    $quantity = max(1, (int) $quantity);
    $book['cart_quantity'] = $quantity;
    $book['cart_subtotal'] = (float) $book['price'] * $quantity;
    $cartBooks[] = $book;
    $total += $book['cart_subtotal'];
}

// รับวิธีชำระเงินที่เลือก โดยค่าเริ่มต้นเป็นเก็บเงินปลายทาง
$paymentMethod = $_POST['payment_method'] ?? ($_SESSION['checkout_payment_method'] ?? 'cod');
if (!in_array($paymentMethod, ['qr', 'cod'], true)) {
    $paymentMethod = 'cod';
}

// เมื่อกดยืนยัน จะสร้างคำสั่งซื้อด้วยระบบเดิมของโปรเจกต์
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$cartBooks) {
            throw new RuntimeException('ไม่พบหนังสือในตะกร้า กรุณากลับไปเลือกหนังสืออีกครั้ง');
        }

        // เก็บวิธีชำระเงินใน Session ระหว่างการใช้งาน
        // หมายเหตุ: ยังไม่ได้บันทึกวิธีชำระเงินลงตาราง orders
        $_SESSION['checkout_payment_method'] = $paymentMethod;

        $id = $orders->checkout((int) $_SESSION['uid'], $_SESSION['cart']);

        // ล้างตะกร้าหลังสร้างคำสั่งซื้อสำเร็จ
        unset($_SESSION['cart']);
        header('Location: order_detail.php?id=' . $id);
        exit;
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$title = 'ยืนยันคำสั่งซื้อ';
require 'includes/header.php';
?>

<!-- ใช้คลาสและสีจาก assets/style.css ของเว็บไซต์ เพื่อให้หน้า Checkout กลมกลืนกับหน้าอื่น -->
<div class="wrap">
    <div class="heading">
        <div>
            <small>FINAL STEP</small>
            <h2>ยืนยันคำสั่งซื้อ</h2>
        </div>
    </div>

    <?php if ($err !== ''): ?>
        <div class="error"><?= e($err) ?></div>
    <?php endif; ?>

    <?php if (!$cartBooks): ?>
        <div class="empty">
            <h3>ไม่พบหนังสือในตะกร้า</h3>
            <p class="muted">กลับไปเลือกหนังสือที่ต้องการก่อนยืนยันคำสั่งซื้อ</p>
            <a class="btn" href="index.php">เลือกซื้อหนังสือ</a>
        </div>
    <?php else: ?>
        <form method="post">
            <div class="checkout-columns">
                <section>
                    <div class="panel">
                        <h3>รายละเอียดหนังสือ</h3>
                        <?php foreach ($cartBooks as $book): ?>
                            <div class="cartrow">
                                <img src="assets/images/<?= e($book['cover']) ?>" alt="ปก <?= e($book['title']) ?>">
                                <div>
                                    <strong><?= e($book['title']) ?></strong>
                                    <p>ผู้แต่ง: <?= e($book['author']) ?></p>
                                    <p>จำนวน <?= (int) $book['cart_quantity'] ?> เล่ม</p>
                                    <p>เล่มละ ฿<?= money($book['price']) ?></p>
                                </div>
                                <strong>฿<?= money($book['cart_subtotal']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                        <div class="total">
                            <span>ยอดรวมสุทธิ</span>
                            <strong>฿<?= money($total) ?></strong>
                        </div>
                    </div>

                    <div class="panel">
                        <h3>เลือกวิธีชำระเงิน</h3>
                        <label class="checkout-payment-option">
                            <span class="checkout-payment-title">
                                <input type="radio" name="payment_method" value="qr" <?= $paymentMethod === 'qr' ? 'checked' : '' ?>>
                                <strong>สแกน QR Code เพื่อชำระเงิน</strong>
                            </span>
                            <span class="muted checkout-payment-description">ชำระผ่านแอปธนาคารหรือแอปที่รองรับ QR Payment</span>
                            <span class="checkout-qr">
                                <?php if (is_file(__DIR__ . '/assets/images/Thai QR Payment Poster.png')): ?>
                                    <img src="assets/images/Thai QR Payment Poster.png" alt="QR Code สำหรับชำระเงิน">
                                    <span class="muted">โปรดตรวจสอบชื่อบัญชีและยอดเงินก่อนชำระ</span>
                                <?php else: ?>
                                    <span class="checkout-qr-placeholder">QR CODE<br>ใส่รูป QR รับเงินจริงของร้านที่นี่</span>
                                    <span class="muted">วางรูป QR รับเงินจริงชื่อ <strong>payment-qr.png</strong> ในโฟลเดอร์ assets/images</span>
                                <?php endif; ?>
                            </span>
                        </label>

                        <label class="checkout-payment-option">
                            <span class="checkout-payment-title">
                                <input type="radio" name="payment_method" value="cod" <?= $paymentMethod === 'cod' ? 'checked' : '' ?>>
                                <strong>เก็บเงินปลายทาง (COD)</strong>
                            </span>
                            <span class="muted checkout-payment-description">ชำระเงินเมื่อได้รับหนังสือ</span>
                        </label>
                        <p class="hint">หมายเหตุ: ระบบตัวอย่างนี้ยังไม่ตรวจสอบยอดโอนอัตโนมัติ และยังไม่ได้เชื่อมต่อบริการขนส่งจริง</p>
                    </div>
                </section>

                <aside class="panel checkout-summary">
                    <h3>สรุปคำสั่งซื้อ</h3>
                    <div class="summary">
                        <span>บัญชีผู้สั่งซื้อ</span>
                        <strong><?= e($_SESSION['username'] ?? 'ผู้ใช้') ?></strong>
                    </div>
                    <div class="summary">
                        <span>จำนวนรายการ</span>
                        <strong><?= count($cartBooks) ?> รายการ</strong>
                    </div>
                    <div class="summary">
                        <span>วิธีชำระเงิน</span>
                        <strong><?= $paymentMethod === 'qr' ? 'QR Code' : 'เก็บเงินปลายทาง' ?></strong>
                    </div>
                    <div class="total">
                        <span>ยอดที่ต้องชำระ</span>
                        <strong>฿<?= money($total) ?></strong>
                    </div>
                    <div class="checkout-actions">
                        <button class="btn" type="submit">ยืนยันสั่งซื้อ ↗</button>
                        <a class="btn outline" href="cart.php">กลับไปแก้ไขตะกร้า</a>
                    </div>
                    <p class="muted">กรุณาตรวจสอบรายการหนังสือ จำนวน และยอดเงินก่อนยืนยัน</p>
                </aside>
            </div>
        </form>
    <?php endif; ?>
</div>

<style>
/* ใช้สีและระยะห่างจาก style.css เดิม โดยเพิ่มเฉพาะการจัดวางของหน้า Checkout */
.checkout-columns{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(260px,.8fr);gap:22px;align-items:start}
.checkout-columns h3{margin-top:0}
.checkout-payment-option{display:block;border:1px solid var(--line);background:#fffdf9;padding:15px;margin:12px 0;cursor:pointer}
.checkout-payment-option:has(input:checked){border:2px solid var(--ink);padding:14px;background:#f7f0e4}
.checkout-payment-title{display:flex;align-items:center;gap:9px}
.checkout-payment-description{display:block;margin:7px 0 0 25px}
.checkout-qr{display:block;text-align:center;background:white;border:1px dashed var(--line);padding:14px;margin:14px 0 0 25px}
.checkout-qr img{display:block;width:min(100%,220px);max-height:240px;object-fit:contain;margin:0 auto 10px}
.checkout-qr-placeholder{display:grid;place-items:center;min-height:120px;max-width:220px;margin:0 auto 10px;background:#f1e8d7;color:var(--ink);font-size:12px}
.checkout-actions{display:grid;gap:10px;margin-top:18px}
.checkout-actions .btn{width:100%;text-align:center}
.checkout-summary{position:sticky;top:20px}
@media(max-width:800px){.checkout-columns{grid-template-columns:1fr}.checkout-summary{position:static}}
@media(max-width:560px){.checkout-columns .cartrow{align-items:flex-start}.checkout-columns .cartrow>strong{font-size:12px}.checkout-qr{margin-left:0}}
</style>

<?php require 'includes/footer.php'; ?>
