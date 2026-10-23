<?php

// เริ่มต้น Session เพื่อเก็บข้อมูลผู้ใช้ที่เข้าสู่ระบบ
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// เรียกใช้งานคลาสที่จำเป็นของระบบร้านหนังสือ
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Book.php';
require_once __DIR__ . '/../classes/Order.php';

try {
    // เชื่อมต่อฐานข้อมูล MySQL
    $pdo = (new Database())->connect();
} catch (Throwable $e) {
    // แสดงข้อความเมื่อเชื่อมต่อฐานข้อมูลไม่สำเร็จ
    exit(
        'เชื่อมต่อฐานข้อมูลไม่ได้ กรุณาตรวจสอบการตั้งค่า Database'
    );
}

// สร้างออบเจ็กต์สำหรับจัดการหนังสือและคำสั่งซื้อ
$books = new Book($pdo);
$orders = new Order($pdo);

// ป้องกันการแสดงผล HTML ที่อาจเป็นอันตราย
function e($v): string
{
    return htmlspecialchars(
        (string) $v,
        ENT_QUOTES,
        'UTF-8'
    );
}

// แปลงตัวเลขราคาให้อยู่ในรูปแบบทศนิยม 2 ตำแหน่ง
function money($v): string
{
    return number_format((float) $v, 2);
}

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือยัง
// หากยังไม่ได้เข้าสู่ระบบ ให้กลับไปหน้า Login
function auth(): void
{
    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
}

// ตรวจสอบสิทธิ์ผู้ดูแลระบบ (Admin)
// ต้องเข้าสู่ระบบก่อน และต้องมี role เป็น admin เท่านั้น
function require_admin(): void
{
    // หากยังไม่ได้เข้าสู่ระบบ ให้ไปหน้า Login
    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }

    // ตรวจสอบสิทธิ์ Admin จาก Session
    // ผู้ใช้ทั่วไปจะไม่สามารถเข้าหน้าจัดการหนังสือได้
    if (($_SESSION['role'] ?? 'user') !== 'admin') {
        // ส่งสถานะ HTTP 403 เมื่อไม่มีสิทธิ์
        http_response_code(403);

        // หยุดการทำงานเพื่อป้องกันการจัดการหนังสือ
        exit('403 Forbidden: คุณไม่มีสิทธิ์จัดการหนังสือ');
    }
}
