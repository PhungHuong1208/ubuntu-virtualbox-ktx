<?php
namespace Models;

use Core\Repository;

class ContractRepository extends Repository {
    protected $table = 'hopdong';
    protected $primaryKey = 'mahopdong';

    public function generateNextMaHopDong() {
        $sql = "SELECT mahopdong FROM {$this->table} ORDER BY LENGTH(mahopdong) DESC, mahopdong DESC LIMIT 1";
        $result = $this->db->query($sql);
        if ($row = $result->fetch_assoc()) {
            $lastMa = $row['mahopdong'];
            if (preg_match('/^([a-zA-Z]+)(\d+)$/', $lastMa, $matches)) {
                $prefix = $matches[1];
                $number = intval($matches[2]) + 1;
                $length = strlen($matches[2]);
                return $prefix . str_pad($number, $length, '0', STR_PAD_LEFT);
            }
        }
        return 'HD001';
    }

    public function getAll() {
        $sql = "SELECT h.mahopdong, h.masv, h.maphong, h.batdau, h.hethan, h.trangthai, s.hoten, p.sophong, p.toa 
                FROM {$this->table} h 
                LEFT JOIN sinhvien s ON h.masv = s.masv 
                LEFT JOIN phong p ON h.maphong = p.maphong
                ORDER BY LENGTH(h.mahopdong) ASC, h.mahopdong ASC";
        return $this->fetchAll($sql);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (mahopdong, masv, maphong, batdau, hethan, trangthai) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssss', 
            $data['mahopdong'], $data['masv'], $data['maphong'], 
            $data['batdau'], $data['hethan'], $data['trangthai']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update($mahopdong, $data) {
        $sql = "UPDATE {$this->table} SET 
                masv = ?, maphong = ?, batdau = ?, hethan = ?, trangthai = ? 
                WHERE mahopdong = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssss', 
            $data['masv'], $data['maphong'], $data['batdau'], 
            $data['hethan'], $data['trangthai'], $mahopdong
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function search($keyword) {
        $sql = "SELECT h.mahopdong, h.masv, h.maphong, h.batdau, h.hethan, h.trangthai, s.hoten, p.sophong, p.toa 
                FROM {$this->table} h 
                LEFT JOIN sinhvien s ON h.masv = s.masv 
                LEFT JOIN phong p ON h.maphong = p.maphong
                WHERE h.mahopdong LIKE ? OR s.hoten LIKE ? OR p.sophong LIKE ? OR h.masv LIKE ?
                ORDER BY LENGTH(h.mahopdong) ASC, h.mahopdong ASC";
        $search = '%' . $keyword . '%';
        return $this->fetchAll($sql, 'ssss', [$search, $search, $search, $search]);
    }
}