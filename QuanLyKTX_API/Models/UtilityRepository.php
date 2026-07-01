<?php
namespace Models;

use Core\Repository;

class UtilityRepository extends Repository {
    // Repository cho tiền điện
    protected $electricityTable = 'tiendien';
    protected $electricityPrimaryKey = 'matd';

    // Repository cho tiền nước
    protected $waterTable = 'tiennuoc';
    protected $waterPrimaryKey = 'matn';

    /**
     * Lấy tất cả tiền điện
     */
    public function getAllElectricity() {
        $sql = "SELECT * FROM {$this->electricityTable} ORDER BY (LOWER(trangthai) = 'chưa thanh toán') DESC, matd DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Lấy tất cả tiền nước
     */
    public function getAllWater() {
        $sql = "SELECT * FROM {$this->waterTable} ORDER BY (LOWER(trangthai) = 'chưa thanh toán') DESC, matn DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Tìm kiếm tiền điện
     */
    public function searchElectricity($keyword) {
        $sql = "SELECT * FROM {$this->electricityTable}
                WHERE matd LIKE ? OR maphong LIKE ?
                ORDER BY (LOWER(trangthai) = 'chưa thanh toán') DESC, matd DESC";
        $search = '%' . $keyword . '%';
        return $this->fetchAll($sql, 'ss', [$search, $search]);
    }

    /**
     * Tìm kiếm tiền nước
     */
    public function searchWater($keyword) {
        $sql = "SELECT * FROM {$this->waterTable}
                WHERE matn LIKE ? OR maphong LIKE ?
                ORDER BY (LOWER(trangthai) = 'chưa thanh toán') DESC, matn DESC";
        $search = '%' . $keyword . '%';
        return $this->fetchAll($sql, 'ss', [$search, $search]);
    }

    /**
     * Tìm tiền điện theo mã
     */
    public function findElectricityById($matd) {
        $codes = $this->normalizeUtilityId($matd, 'TD');
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));
        $types = str_repeat('s', count($codes));
        $sql = "SELECT * FROM {$this->electricityTable} WHERE matd IN ($placeholders) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$codes);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    /**
     * Tìm tiền nước theo mã
     */
    public function findWaterById($matn) {
        $codes = $this->normalizeUtilityId($matn, 'TN');
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));
        $types = str_repeat('s', count($codes));
        $sql = "SELECT * FROM {$this->waterTable} WHERE matn IN ($placeholders) LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$codes);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    /**
     * Tạo hóa đơn tiền điện mới
     */
    public function createElectricity($data) {
        $sql = "INSERT INTO {$this->electricityTable} (matd, maphong, giadien, ngay, trangthai)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss',
            $data['matd'],
            $data['maphong'],
            $data['giadien'],
            $data['ngay'],
            $data['trangthai']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Tạo hóa đơn tiền nước mới
     */
    public function createWater($data) {
        $sql = "INSERT INTO {$this->waterTable} (matn, maphong, gianuoc, ngay, trangthai)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss',
            $data['matn'],
            $data['maphong'],
            $data['gianuoc'],
            $data['ngay'],
            $data['trangthai']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Kiểm tra mã hóa đơn điện đã tồn tại
     */
    public function electricityExists($matd) {
        $sql = "SELECT matd FROM {$this->electricityTable} WHERE matd = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $matd);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Kiểm tra mã hóa đơn nước đã tồn tại
     */
    public function waterExists($matn) {
        $sql = "SELECT matn FROM {$this->waterTable} WHERE matn = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $matn);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Tạo mã hóa đơn điện tự động
     */
    public function getNextElectricityCode($prefix = 'TD', $length = 3) {
        $prefix = strtoupper($prefix);
        $sql = "SELECT matd FROM {$this->electricityTable} WHERE UPPER(matd) LIKE ?";
        $like = $prefix . '%';
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $maxNumber = 0;
        while ($row = $result->fetch_assoc()) {
            if (preg_match('/' . preg_quote($prefix, '/') . '(\d+)$/i', $row['matd'], $matches)) {
                $num = intval($matches[1]);
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $stmt->close();
        return $prefix . str_pad($maxNumber + 1, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Tạo mã hóa đơn nước tự động
     */
    public function getNextWaterCode($prefix = 'TN', $length = 3) {
        $prefix = strtoupper($prefix);
        $sql = "SELECT matn FROM {$this->waterTable} WHERE UPPER(matn) LIKE ?";
        $like = $prefix . '%';
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $maxNumber = 0;
        while ($row = $result->fetch_assoc()) {
            if (preg_match('/' . preg_quote($prefix, '/') . '(\d+)$/i', $row['matn'], $matches)) {
                $num = intval($matches[1]);
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $stmt->close();
        return $prefix . str_pad($maxNumber + 1, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Xóa hóa đơn tiền điện
     */
    private function normalizeUtilityId($id, $prefix) {
        $id = trim($id);
        $variants = [];

        if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/i', $id, $matches)) {
            $number = intval($matches[1]);
            $variants[] = strtoupper($id);
            $variants[] = (string)$number;
            $variants[] = str_pad($number, 3, '0', STR_PAD_LEFT);
        } elseif (preg_match('/^\d+$/', $id)) {
            $number = intval($id);
            $variants[] = (string)$number;
            $variants[] = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        } else {
            $variants[] = strtoupper($id);
        }

        return array_values(array_unique($variants));
    }

    public function deleteElectricity($matd) {
        $codes = $this->normalizeUtilityId($matd, 'TD');
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));
        $types = str_repeat('s', count($codes));
        $sql = "DELETE FROM {$this->electricityTable} WHERE matd IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$codes);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Đánh dấu hóa đơn điện đã thanh toán
     */
    public function markElectricityAsPaid($matd) {
        $codes = $this->normalizeUtilityId($matd, 'TD');
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));
        $types = str_repeat('s', count($codes));
        $sql = "UPDATE {$this->electricityTable} SET trangthai = 'Đã thanh toán' WHERE matd IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$codes);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Xóa hóa đơn tiền nước
     */
    public function deleteWater($matn) {
        $codes = $this->normalizeUtilityId($matn, 'TN');
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));
        $types = str_repeat('s', count($codes));
        $sql = "DELETE FROM {$this->waterTable} WHERE matn IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$codes);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Đánh dấu hóa đơn nước đã thanh toán
     */
    public function markWaterAsPaid($matn) {
        $codes = $this->normalizeUtilityId($matn, 'TN');
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));
        $types = str_repeat('s', count($codes));
        $sql = "UPDATE {$this->waterTable} SET trangthai = 'Đã thanh toán' WHERE matn IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$codes);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Lấy tất cả phòng (để dropdown)
     */
    public function getAllRooms() {
        $sql = "SELECT maphong FROM phong";
        $result = $this->db->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Cập nhật hóa đơn tiền điện
     */
    public function updateElectricity($data) {
        $sql = "UPDATE {$this->electricityTable} SET maphong = ?, giadien = ?, ngay = ?, trangthai = ? WHERE matd = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss',
            $data['maphong'],
            $data['giadien'],
            $data['ngay'],
            $data['trangthai'],
            $data['matd']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Cập nhật hóa đơn tiền nước
     */
    public function updateWater($data) {
        $sql = "UPDATE {$this->waterTable} SET maphong = ?, gianuoc = ?, ngay = ?, trangthai = ? WHERE matn = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss',
            $data['maphong'],
            $data['gianuoc'],
            $data['ngay'],
            $data['trangthai'],
            $data['matn']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}