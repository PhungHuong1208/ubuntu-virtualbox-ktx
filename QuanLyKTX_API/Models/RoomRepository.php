<?php
namespace Models;

use Core\Repository;

class RoomRepository extends Repository {
    protected $table = 'phong';
    protected $primaryKey = 'maphong';

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} 
                ORDER BY 
                    CASE trangthai 
                        WHEN 'Trống' THEN 1 
                        WHEN 'Đầy' THEN 2 
                        WHEN 'Bảo Trì' THEN 3 
                        ELSE 4 
                    END ASC,
                    (succhua - phonghientai) DESC,
                    LENGTH(maphong) ASC, maphong ASC";
        return $this->fetchAll($sql);
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (maphong, sophong, toa, succhua, phonghientai, gia, trangthai) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssiids', 
            $data['maphong'], $data['sophong'], $data['toa'], 
            $data['succhua'], $data['phonghientai'], $data['gia'], $data['trangthai']
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update($maphong, $data) {
        $sql = "UPDATE {$this->table} SET 
                sophong = ?, toa = ?, succhua = ?, phonghientai = ?, gia = ?, trangthai = ? 
                WHERE maphong = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssiidss', 
            $data['sophong'], $data['toa'], 
            $data['succhua'], $data['phonghientai'], $data['gia'], $data['trangthai'], 
            $maphong
        );
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE maphong LIKE ? OR sophong LIKE ? OR toa LIKE ?
                ORDER BY 
                    CASE trangthai 
                        WHEN 'Trống' THEN 1 
                        WHEN 'Đầy' THEN 2 
                        WHEN 'Bảo Trì' THEN 3 
                        ELSE 4 
                    END ASC,
                    LENGTH(maphong) ASC, maphong ASC";
        $search = '%' . $keyword . '%';
        return $this->fetchAll($sql, 'sss', [$search, $search, $search]);
    }

    public function svinroom($maphong) {
        $sql = "SELECT p.*, s.masv, s.hoten, s.gioitinh, s.lop, s.cccd, s.sodienthoai, s.email,
                       h.mahopdong, h.batdau, h.hethan, h.trangthai as trangthai_hd
                FROM phong p
                LEFT JOIN hopdong h ON p.maphong = h.maphong AND h.trangthai = 'Đang Hoạt Động'
                LEFT JOIN sinhvien s ON h.masv = s.masv
                WHERE p.maphong = ?
                ORDER BY s.masv ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $maphong);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $roomData = null;
        $students = [];
        
        while ($row = $result->fetch_assoc()) {
            if (!$roomData) {
                $roomData = [
                    'maphong' => $row['maphong'],
                    'sophong' => $row['sophong'],
                    'toa' => $row['toa'],
                    'succhua' => $row['succhua'],
                    'phonghientai' => $row['phonghientai'],
                    'trangthai' => $row['trangthai'],
                    'gia' => $row['gia']
                ];
            }
            if ($row['masv']) {
                $students[] = [
                    'masv' => $row['masv'],
                    'hoten' => $row['hoten'],
                    'gioitinh' => $row['gioitinh'],
                    'lop' => $row['lop'],
                    'cccd' => $row['cccd'],
                    'sodienthoai' => $row['sodienthoai'],
                    'email' => $row['email'],
                    'mahopdong' => $row['mahopdong'],
                    'batdau' => $row['batdau'],
                    'hethan' => $row['hethan'],
                    'trangthai_hd' => $row['trangthai_hd']
                ];
            }
        }
        $stmt->close();
        if ($roomData) {
            $roomData['students'] = $students;
        }
        return $roomData;
    }

    /**
     * Tìm phòng đang ở của một sinh viên (theo masv)
     * Dùng cho API người dùng: getRoom()
     */
    public function findByStudent($masv) {
        $sql = "SELECT p.maphong, p.sophong, p.toa, p.succhua, p.phonghientai, p.gia, p.trangthai,
                       h.mahopdong, h.batdau, h.hethan, h.trangthai AS trangthai_hd
                FROM hopdong h
                INNER JOIN phong p ON h.maphong = p.maphong
                WHERE h.masv = ? AND h.trangthai = 'Đang Hoạt Động'
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $masv);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /**
     * Lấy toàn bộ lịch sử hợp đồng của một sinh viên (theo masv)
     * Dùng cho API người dùng: getContracts()
     */
    public function getContract($masv) {
        $sql = "SELECT h.mahopdong, h.masv, h.maphong, h.batdau, h.hethan, h.trangthai,
                       p.sophong, p.toa, p.gia
                FROM hopdong h
                LEFT JOIN phong p ON h.maphong = p.maphong
                WHERE h.masv = ?
                ORDER BY h.batdau DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $masv);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }
}