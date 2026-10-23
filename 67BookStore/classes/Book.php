<?php

/**
 * Book.php
 * สมาชิกคนที่ 1: จัดการข้อมูลหนังสือ (CRUD)
 * Project: 67BookStore
 */

class Book
{
    private PDO $db;

    /**
     * เชื่อมต่อฐานข้อมูล
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * แสดงรายการหนังสือทั้งหมด
     * สามารถค้นหาชื่อหนังสือ ผู้แต่ง และกรองหมวดหมู่ได้
     */
    public function all(string $q = '', string $cat = ''): array
    {
        $sql = 'SELECT * FROM books WHERE 1 = 1';
        $params = [];

        // ค้นหาชื่อหนังสือหรือชื่อผู้แต่ง
        if ($q !== '') {
            $sql .= ' AND (title LIKE ? OR author LIKE ?)';
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }

        // กรองตามหมวดหมู่
        if ($cat !== '') {
            $sql .= ' AND category = ?';
            $params[] = $cat;
        }

        $sql .= ' ORDER BY id ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ค้นหาหนังสือด้วย ID
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM books WHERE id = ?'
        );

        $stmt->execute([$id]);

        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        return $book ?: null;
    }

    /**
     * เพิ่มหนังสือใหม่ (Create)
     */
    public function create(array $data): bool
    {
        $sql = '
            INSERT INTO books (
                title,
                author,
                category,
                price,
                cover,
                description,
                stock
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['title'],
            $data['author'],
            $data['category'],
            $data['price'],
            $data['cover'],
            $data['description'],
            $data['stock']
        ]);
    }

    /**
     * แก้ไขข้อมูลหนังสือ (Update)
     */
    public function update(int $id, array $data): bool
    {
        $sql = '
            UPDATE books
            SET
                title = ?,
                author = ?,
                category = ?,
                price = ?,
                cover = ?,
                description = ?,
                stock = ?
            WHERE id = ?
        ';

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['title'],
            $data['author'],
            $data['category'],
            $data['price'],
            $data['cover'],
            $data['description'],
            $data['stock'],
            $id
        ]);
    }

    /**
     * ลบหนังสือ (Delete)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM books WHERE id = ?'
        );

        return $stmt->execute([$id]);
    }
}

