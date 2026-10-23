<?php

// สมาชิกคนที่ 2: บันทึกคำสั่งซื้อและเรียกดูประวัติ
class Order
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function checkout(int $uid, array $cart): int
    {
        if (!$cart) {
            throw new RuntimeException('ตะกร้าว่าง');
        }

        $this->db->beginTransaction();

        try {
            $items = [];
            $total = 0;

            $q = $this->db->prepare(
                'SELECT * FROM books WHERE id = ? FOR UPDATE'
            );

            foreach ($cart as $id => $qty) {
                $q->execute([(int) $id]);
                $b = $q->fetch();
                $qty = (int) $qty;

                if (!$b || $qty < 1 || (int) $b['stock'] < $qty) {
                    throw new RuntimeException('สินค้าไม่พอหรือไม่มีสินค้า');
                }

                $items[] = [$b, $qty];
                $total += (float) $b['price'] * $qty;
            }

            $s = $this->db->prepare(
                "INSERT INTO orders (user_id, total, status, created_at)
                 VALUES (?, ?, 'รอดำเนินการ', NOW())"
            );

            $s->execute([$uid, $total]);
            $oid = (int) $this->db->lastInsertId();

            $i = $this->db->prepare(
                'INSERT INTO order_items
                (order_id, book_id, book_title, price, quantity)
                VALUES (?, ?, ?, ?, ?)'
            );

            $u = $this->db->prepare(
                'UPDATE books
                 SET stock = stock - ?
                 WHERE id = ? AND stock >= ?'
            );

            foreach ($items as [$b, $n]) {
                $i->execute([
                    $oid,
                    $b['id'],
                    $b['title'],
                    $b['price'],
                    $n
                ]);

                $u->execute([$n, $b['id'], $n]);

                if (!$u->rowCount()) {
                    throw new RuntimeException(
                        'สต็อกเปลี่ยน กรุณาลองใหม่'
                    );
                }
            }

            $this->db->commit();

            return $oid;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function history(int $uid): array
    {
        $s = $this->db->prepare(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC'
        );

        $s->execute([$uid]);

        return $s->fetchAll();
    }

    public function find(int $id, int $uid): ?array
    {
        $s = $this->db->prepare(
            'SELECT * FROM orders WHERE id = ? AND user_id = ?'
        );

        $s->execute([$id, $uid]);

        return $s->fetch() ?: null;
    }

    public function items(int $id): array
    {
        $s = $this->db->prepare(
            'SELECT * FROM order_items WHERE order_id = ?'
        );

        $s->execute([$id]);

        return $s->fetchAll();
    }
}
