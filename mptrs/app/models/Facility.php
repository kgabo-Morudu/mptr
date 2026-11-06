<?php
class Facility extends Model {
    public function all(): array {
        return $this->fetchAll("SELECT * FROM facilities ORDER BY name");
    }
    public function find(int $id): ?array {
        return $this->fetchOne("SELECT * FROM facilities WHERE id=?", [$id]);
    }
    public function create(array $d): int {
        $this->execute("INSERT INTO facilities (code,name,type,location,contact_person,phone) VALUES (?,?,?,?,?,?)",
            [$d['code'],$d['name'],$d['type'],$d['location'],$d['contact_person'],$d['phone']]);
        return $this->lastInsertId();
    }
    public function update(int $id, array $d): void {
        $this->execute("UPDATE facilities SET code=?,name=?,type=?,location=?,contact_person=?,phone=?,is_active=? WHERE id=?",
            [$d['code'],$d['name'],$d['type'],$d['location'],$d['contact_person'],$d['phone'],$d['is_active']??1,$id]);
    }
    public function delete(int $id): void {
        $this->execute("DELETE FROM facilities WHERE id=?", [$id]);
    }
}