
<?php

require 'includes/bootstrap.php';

if (!empty($_SESSION['uid'])) {
    header('Location: index.php');
    exit;
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare(
        'SELECT * FROM users WHERE username = ?'
    );

    $s->execute([
        trim($_POST['username'] ?? '')
    ]);

    $u = $s->fetch();

    if ($u && password_verify(
        $_POST['password'] ?? '',
        $u['password_hash']
    )) {
        session_regenerate_id(true);

        $_SESSION['uid'] = (int) $u['id'];
        $_SESSION['username'] = $u['username'];
        // เก็บ role ไว้ใน session เพื่อใช้ควบคุมเมนูและสิทธิ์แต่ละหน้า
        $_SESSION['role'] = $u['role'] ?? 'user';
        $_SESSION['cart'] = [];

        header('Location: index.php');
        exit;
    }

    $err = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}

$title = 'เข้าสู่ระบบ';

require 'includes/header.php';
?>

<section class="login">

    <div class="login-art">
        <small>YOUR NEXT CHAPTER STARTS HERE</small>

        <h1>
            Every book<br>
            <em>opens a new world.</em>
        </h1>

        <p>
            พื้นที่เล็ก ๆ สำหรับเรื่องราวที่คุณอยากเก็บไว้
        </p>
    </div>

    <div class="login-form">
        <small>WELCOME BACK</small>

        <h2>เข้าสู่ระบบ</h2>

        <p>ลงชื่อเข้าใช้เพื่อเลือกซื้อหนังสือ</p>

        <?php if ($err): ?>
            <p class="error">
                <?= e($err) ?>
            </p>
        <?php endif; ?>

        <form method="post">
            <label>
                ชื่อผู้ใช้
                <input
                    name="username"
                    required
                    autofocus
                >
            </label>

            <label>
                รหัสผ่าน
                <input
                    type="password"
                    name="password"
                    required
                >
            </label>

            <button class="btn">
                เข้าสู่ระบบ ↗
            </button>
        </form>

        <p class="hint">
            บัญชีผู้ใช้: <b>booklover</b> / <b>Book1234!</b><br>
            บัญชีแอดมิน: <b>admin</b> / <b>Admin1234!</b>
        </p>
    </div>

</section>

<?php require 'includes/footer.php'; ?>
