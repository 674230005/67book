# 67BookStore — PHP OOP + MySQL
ร้านขายนิยายและมังงะ ธีมดำ–ครีม รองรับมือถือ มีหน้า Login, CRUD หนังสือ, ตะกร้า, สั่งซื้อ และประวัติคำสั่งซื้อ

## ฐานข้อมูล
ชื่อใหม่: `bookstore_novel_manga` ไฟล์ SQL: `database/bookstore_novel_manga.sql`

## ติดตั้งด้วย XAMPP
1. แตก ZIP ไปไว้ `C:\xampp\htdocs\67BookStore`
2. เปิด XAMPP แล้ว Start Apache และ MySQL
3. เข้า `http://localhost/phpmyadmin` > Import > เลือก `database/bookstore_novel_manga.sql` > Go (SQL จะสร้างฐานข้อมูลให้เอง)
4. ตรวจ `classes/Database.php` ค่าเริ่มต้น host `127.0.0.1`, user `root`, password ว่าง
5. เปิด `http://localhost/67BookStore/` ระบบจะพาไปหน้า Login

## บัญชีทดสอบ
- **Admin:** Username `admin` / Password `Admin1234!`
- **ผู้ใช้ทั่วไป:** Username `booklover` / Password `Book1234!`

รหัสผ่านเก็บเป็น bcrypt hash และตรวจด้วย `password_verify()` ไม่ได้เก็บเป็นข้อความธรรมดา

## สิทธิ์การใช้งาน
- **Admin:** เพิ่ม แก้ไข และลบหนังสือได้
- **ผู้ใช้ทั่วไป:** เลือกดูหนังสือ เพิ่มลงตะกร้า สั่งซื้อ และดูประวัติคำสั่งซื้อได้ แต่ไม่มีสิทธิ์จัดการหนังสือ
- เมนูจัดการหนังสือจะแสดงเฉพาะ Admin และ `manage_books.php` ตรวจสิทธิ์ฝั่งเซิร์ฟเวอร์ด้วย `require_admin()` ด้วย จึงเข้า URL โดยตรงในฐานะผู้ใช้ทั่วไปไม่ได้

## ถ้า Import ฐานข้อมูลไปแล้ว
1. เปิด `http://localhost/phpmyadmin`
2. เลือกฐานข้อมูล `bookstore_novel_manga`
3. ไปที่แท็บ **Import** แล้วเลือก `database/upgrade_admin.sql`
4. กด **Import/Go** เพื่อเพิ่ม role และสร้างบัญชี Admin โดยไม่ลบหนังสือหรือประวัติคำสั่งซื้อ
5. หากขึ้นข้อผิดพลาดว่ามีคอลัมน์ `role` อยู่แล้ว แสดงว่าได้เพิ่มคอลัมน์ไปก่อนแล้ว ให้ข้ามคำสั่ง `ALTER TABLE` ในไฟล์และรันคำสั่งส่วนที่เหลือ

**อย่า Import `bookstore_novel_manga.sql` ซ้ำ** เพราะไฟล์ติดตั้งหลักมีคำสั่งลบและสร้างตารางใหม่ อาจทำให้ข้อมูลเดิมหายได้.

## แบ่งงาน 2 คน / 3 Class
- สมาชิก 1: `classes/Book.php` และหน้า `index.php`, `book.php`, `manage_books.php`
- สมาชิก 2: `classes/Order.php` และหน้า `cart.php`, `checkout.php`, `orders.php`, `order_detail.php`
- ใช้ร่วมกัน: `classes/Database.php`

## ฟังก์ชัน
- Login/logout พร้อม role แยก Admin และผู้ใช้ทั่วไป
- ค้นหาและกรองหมวดหมู่หนังสือ พร้อมรูปปก 8 ภาพ
- CRUD หนังสือ เพิ่ม แก้ไข ลบ
- ตะกร้า ปรับจำนวนและลบรายการ
- สั่งซื้อบันทึกฐานข้อมูลและตัดสต็อกด้วย transaction
- ประวัติและรายละเอียดคำสั่งซื้อ

หมายเหตุ: ระบบนี้เป็นงานตัวอย่างเพื่อการเรียน ยังไม่เชื่อมต่อชำระเงินจริง ควรเพิ่มหน้าเปลี่ยนรหัสผ่านก่อนใช้งานจริง
