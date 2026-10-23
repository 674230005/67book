CREATE DATABASE IF NOT EXISTS bookstore_novel_manga
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE bookstore_novel_manga;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    -- role ใช้แยกสิทธิ์: admin จัดการหนังสือได้, user ซื้อหนังสือได้
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(150) NOT NULL DEFAULT 'ไม่ระบุ',
    category ENUM('นิยาย', 'มังงะ') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    cover VARCHAR(255) NOT NULL,
    description TEXT,
    stock INT UNSIGNED NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'รอดำเนินการ',
    created_at DATETIME NOT NULL,

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    book_id INT UNSIGNED NULL,
    book_title VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,

    FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    FOREIGN KEY (book_id)
        REFERENCES books(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- บัญชีทดสอบสำหรับผู้ใช้ทั่วไป
-- Username: booklover | Password: Book1234!
-- role = user: ซื้อหนังสือและดูประวัติคำสั่งซื้อได้ แต่จัดการหนังสือไม่ได้
INSERT INTO users (username, password_hash, role)
VALUES (
    'booklover',
    '$2y$12$KUJcidHF3N6wCsmp9uAKsucOC4IUk/SX3Fbf5k75qQb9sn7Mrc6vq',
    'user'
);

-- บัญชีผู้ดูแลระบบ (Admin)
-- Username: admin | Password: Admin1234!
-- เก็บรหัสผ่านเป็น bcrypt hash ไม่ได้เก็บเป็นข้อความธรรมดา
INSERT INTO users (username, password_hash, role)
VALUES (
    'admin',
    '$2y$12$89UQkdbe.QagqV9dfqS2YuoqH7edVRmvTzYhZ0hZGCzXf2SUoOQfW',
    'admin'
);

INSERT INTO books (
    title,
    author,
    category,
    price,
    cover,
    description,
    stock
) VALUES
(
    'ธาราคั่งแค้น ฉันท์แสนหมื่นชัง เล่ม 1',
    'Eruiun',
    'นิยาย',
    480,
    'thara-vol1.png',
    'เรื่องราวความรัก ความแค้น และการชิงไหวชิงพริบ ท่ามกลางอำนาจและชะตากรรมที่ยากจะหลีกหนี',
    10
),
(
    'ธาราคั่งแค้น ฉันท์แสนหมื่นชัง เล่ม 2',
    'Eruiun',
    'นิยาย',
    480,
    'thara-vol2.png',
    'เมื่อความพยาบาทยังไม่มอดดับ การต่อสู้ด้วยสติปัญญาจึงดำเนินต่อ เพื่อฝ่าฟันอุปสรรคและช่วงชิงเส้นทางของตนเอง',
    10
),
(
    'ดั่งศิลาซ่อนหยก เล่ม 1',
    'Eruiun',
    'นิยาย',
    420,
    'dang-sila-vol1.png',
    'หลินหว่านเยว่ปลอมตัวเป็นบุรุษเข้าร่วมกองทัพ เพื่อทวงคืนความยุติธรรมและสะสางหนี้แค้นจากโศกนาฏกรรมในอดีต',
    10
),
(
    'ดั่งศิลาซ่อนหยก เล่ม 2',
    'Eruiun',
    'นิยาย',
    420,
    'dang-sila-vol2.png',
    'ท่ามกลางแผนลอบสังหารและอันตราย หลินหว่านเยว่ต้องเติบโตขึ้น พร้อมเผชิญชะตาที่พลิกผันและภารกิจครั้งใหม่',
    10
),
(
    'ดั่งศิลาซ่อนหยก เล่ม 3',
    'Eruiun',
    'นิยาย',
    420,
    'dang-sila-vol3.png',
    'เมื่อความรักและหน้าที่ขัดแย้งกัน หลินหว่านเยว่ต้องเผชิญความเจ็บปวด พร้อมคว้าแสงแห่งความหวังท่ามกลางมรสุมชีวิต',
    10
),
(
    'The Witcher เล่ม 1',
    'Andrzej Sapkowski',
    'นิยาย',
    480,
    'witcher-vol1.png',
    'เกรอลท์ต้องปกป้องซิริ ผู้มีพลังพิเศษและชะตาสำคัญ ท่ามกลางความขัดแย้งและสงครามระหว่างเผ่าพันธุ์ Thai Health Library',
    10
),
(
    'โกคุราคุไก เล่ม 1',
    'Yuto Sano',
    'มังงะ',
    175,
    'gokurakugai-vol1.png',
    'ทาโอะและอารุมะรับงานแก้ปัญหาในเมืองลึกลับ ก่อนเผชิญคดีคนหายและเหล่าสัตว์ประหลาดสุดอันตราย',
    10
),
(
    'โกคุราคุไก เล่ม 2',
    'Yuto Sano',
    'มังงะ',
    175,
    'gokurakugai-vol2.png',
    'การผจญภัยในเมืองโกคุราคุไกดำเนินต่อ ท่ามกลางความลับของมนุษย์อสูรและการต่อสู้ที่เข้มข้นยิ่งขึ้น',
    10
);