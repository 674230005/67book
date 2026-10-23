-- =========================================================
-- อัปเกรดฐานข้อมูล 67BookStore ที่ Import ไปแล้ว (ไม่ลบตาราง/คำสั่งซื้อ)
-- ใช้ไฟล์นี้เพียงครั้งเดียวกับฐานข้อมูล bookstore_novel_manga เดิม
-- =========================================================

USE bookstore_novel_manga;

-- เพิ่มคอลัมน์ role เพื่อแยกผู้ดูแลระบบกับผู้ใช้ทั่วไป
-- หมายเหตุ: ถ้ามีคอลัมน์ role อยู่แล้ว ให้ข้ามคำสั่ง ALTER TABLE นี้
ALTER TABLE users
    ADD COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
    AFTER password_hash;

-- กำหนดบัญชีเดิมให้เป็นผู้ใช้ทั่วไป เพื่อไม่ให้ได้รับสิทธิ์ Admin โดยอัตโนมัติ
UPDATE users
SET role = 'user'
WHERE username <> 'admin';

-- สร้างบัญชี Admin ถ้ายังไม่มีบัญชีชื่อนี้
-- Username: admin
-- Password: Admin1234!
-- password_hash เป็น bcrypt hash สำหรับใช้กับ password_verify()
INSERT INTO users (username, password_hash, role)
SELECT
    'admin',
    '$2y$12$89UQkdbe.QagqV9dfqS2YuoqH7edVRmvTzYhZ0hZGCzXf2SUoOQfW',
    'admin'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE username = 'admin'
);

-- หากมีบัญชี admin อยู่แล้ว จะกำหนดให้บัญชีนั้นเป็น Admin
UPDATE users
SET role = 'admin'
WHERE username = 'admin';
