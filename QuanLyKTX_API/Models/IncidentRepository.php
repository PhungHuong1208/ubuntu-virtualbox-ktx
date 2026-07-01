<?php
namespace Models;

use Core\Repository;

class IncidentRepository extends Repository {
    protected $table = 'suco';
    protected $primaryKey = 'masuco';

    public function generateNextMaSuCo() {
        $sql = "SELECT masuco FROM {$this->table} ORDER BY masuco DESC LIMIT 1";
        $result = $this->db->query($sql);
        if ($row = $result->fetch_assoc()) {
            return intval($row['masuco']) + 1;
        }
        return 1;
    }

    public function getAll() {
        $sql = "SELECT s.*, p.sophong, p.toa, sv.hoten 
                FROM {$this->table} s 
                LEFT JOIN phong p ON s.maphong = p.maphong 
                LEFT JOIN sinhvien sv ON s.masv = sv.masv
                ORDER BY s.masuco DESC";
        return $this->fetchAll($sql);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (maphong, masv, mota, ngaybao, trangthai) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        // Lưu ý: data có thể không có masv nếu admin tạo giùm, ta dùng null
        $masv = isset($data['masv']) ? $data['masv'] : null;
        $stmt->bind_param('sssss', 
            $data['maphong'], $masv, $data['mota'], $data['ngaybao'], $data['trangthai']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update($masuco, $data) {
        $sql = "UPDATE {$this->table} SET 
                maphong = ?, mota = ?, ngaybao = ?, trangthai = ? 
                WHERE masuco = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssi', 
            $data['maphong'], $data['mota'], $data['ngaybao'], 
            $data['trangthai'], $masuco
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function search($keyword) {
        $sql = "SELECT s.*, p.sophong, p.toa, sv.hoten 
                FROM {$this->table} s 
                LEFT JOIN phong p ON s.maphong = p.maphong 
                LEFT JOIN sinhvien sv ON s.masv = sv.masv
                WHERE s.masuco LIKE ? OR s.maphong LIKE ? OR s.mota LIKE ? OR p.sophong LIKE ? OR sv.hoten LIKE ?
                ORDER BY s.masuco DESC";
        $search = '%' . $keyword . '%';
        return $this->fetchAll($sql, 'sssss', [$search, $search, $search, $search, $search]);
    }

    /**
     * Dành cho User App: Lấy các sự cố do sinh viên báo cáo
     */
    public function findByStudent($masv) {
        $sql = "SELECT s.*, p.sophong, p.toa 
                FROM {$this->table} s 
                LEFT JOIN phong p ON s.maphong = p.maphong 
                WHERE s.masv = ?
                ORDER BY s.masuco DESC";
        return $this->fetchAll($sql, 's', [$masv]);
    }

    /**
     * Dành cho User App: Gửi báo cáo sự cố mới
     */
    public function insertRequest($data) {
        $sql = "INSERT INTO {$this->table} (maphong, masv, mota, ngaybao, trangthai) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $trangthai = $data['trangthai'] ?? 'Mới gửi';
        $stmt->bind_param('sssss', 
            $data['maphong'], $data['masv'], $data['mota'], $data['ngaybao'], $trangthai
        );
        $result = $stmt->execute();
        if (!$result) {
            file_put_contents(__DIR__ . '/../Public/db_error.log', "DB Error in insertRequest: " . $stmt->error . "\nData: " . json_encode($data) . "\n", FILE_APPEND);
        }
        $stmt->close();
        return $result;
    }
}