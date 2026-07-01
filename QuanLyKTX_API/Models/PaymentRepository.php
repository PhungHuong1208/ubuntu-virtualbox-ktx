<?php
namespace Models;

use Core\Repository;

class PaymentRepository extends Repository {
    protected $table = 'thanhtoan';
    protected $primaryKey = 'mathanhtoan';

    public function getAll() {
        $sql = "SELECT t.*, p.sophong, p.maphong FROM {$this->table} t 
                LEFT JOIN phong p ON t.maphong = p.maphong
                ORDER BY CASE WHEN t.trangthai = 'Chưa Thanh Toán' THEN 0 ELSE 1 END ASC, t.mathanhtoan ASC";
        return $this->fetchAll($sql);
    }

    public function getNextPaymentCode() {
        $sql = "SELECT COALESCE(MAX(mathanhtoan), 0) + 1 AS next_id FROM {$this->table}";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        $nextId = $row['next_id'];
        return 'TT' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    public function getPriceByRoom($maphong) {
        $sql = "SELECT gia FROM phong WHERE maphong = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $maphong);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ? (float)$row['gia'] : 0;
    }

    public function create($data) {
        $sotien = $this->getPriceByRoom($data['maphong']);
        $trangthai = 'Chưa Thanh Toán';
        
        $sql = "INSERT INTO {$this->table} (maphong, sotien, ngaytra, trangthai) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sdss', 
            $data['maphong'], $sotien, $data['ngaytra'], $trangthai
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update($mathanhtoan, $data) {
        $sotien = $this->getPriceByRoom($data['maphong']);
        
        $sql = "UPDATE {$this->table} SET 
                maphong = ?, sotien = ?, ngaytra = ?, trangthai = ? 
                WHERE mathanhtoan = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sdssi', 
            $data['maphong'], $sotien, $data['ngaytra'], 
            $data['trangthai'], $mathanhtoan
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateStatus($mathanhtoan, $trangthai) {
        $sql = "UPDATE {$this->table} SET trangthai = ? WHERE mathanhtoan = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ss', $trangthai, $mathanhtoan);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function search($keyword) {
        $sql = "SELECT t.*, p.sophong, p.maphong FROM {$this->table} t 
                LEFT JOIN phong p ON t.maphong = p.maphong
                WHERE t.mathanhtoan LIKE ? OR p.sophong LIKE ? OR p.maphong LIKE ? OR t.trangthai LIKE ?
                ORDER BY CASE WHEN t.trangthai = 'Chưa Thanh Toán' THEN 0 ELSE 1 END ASC, t.mathanhtoan ASC";
        $search = '%' . $keyword . '%';
        return $this->fetchAll($sql, 'ssss', [$search, $search, $search, $search]);
    }
}