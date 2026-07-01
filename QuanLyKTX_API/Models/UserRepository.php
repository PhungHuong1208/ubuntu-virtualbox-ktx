<?php
require_once __DIR__ . '/../Config/Database.php';

class AuthRepository {
    private $db;
    public function __construct() { $this->db = \Config\Database::getConnection(); }

    public function login($masv, $password) {
        $sql = "SELECT * FROM taikhoan_user WHERE masv=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s',$masv);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && $user['password'] === $password) {
            $sql2 = "SELECT hoten FROM sinhvien WHERE masv=?";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->bind_param('s',$masv);
            $stmt2->execute();
            $sv = $stmt2->get_result()->fetch_assoc();
            return ['status'=>'success','data'=>['masv'=>$user['masv'],'hoten'=>$sv['hoten']??'']];
        }
        return ['status'=>'error','message'=>'Tài khoản không chính xác'];
    }

    public function changePassword($masv,$old_pw,$new_pw) {
        $sql = "SELECT password FROM taikhoan_user WHERE masv=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s',$masv);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result || $result['password'] !== $old_pw) {
            return ['status'=>'error','message'=>'Mật khẩu cũ không chính xác'];
        }

        $sql2 = "UPDATE taikhoan_user SET password=? WHERE masv=?";
        $stmt2 = $this->db->prepare($sql2);
        $stmt2->bind_param('ss',$new_pw,$masv);
        if ($stmt2->execute()) return ['status'=>'success','message'=>'Đổi mật khẩu thành công'];
        return ['status'=>'error','message'=>'Lỗi hệ thống'];
    }
}

class StudentRepository {
    private $db;
    public function __construct() { $this->db = \Config\Database::getConnection(); }

    public function findByMasv($masv) {
        $sql = "SELECT * FROM sinhvien WHERE masv=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s',$masv);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($data) {
        $sql = "UPDATE sinhvien SET hoten=?,lop=?,gioitinh=?,cccd=?,sodienthoai=?,email=?,diachi=? WHERE masv=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssssss',$data['hoten'],$data['lop'],$data['gioitinh'],$data['cccd'],
                          $data['sodienthoai'],$data['email'],$data['diachi'],$data['masv']);
        return $stmt->execute();
    }
}

class IncidentRepository {
    private $db;
    public function __construct() { $this->db = \Config\Database::getConnection(); }

    public function findByStudent($masv) {
        $sql = "SELECT s.* FROM suco s
                JOIN hopdong h ON s.maphong=h.maphong
                WHERE h.masv=? AND h.trangthai='Đang Hoạt Động'
                ORDER BY s.ngaybao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s',$masv);
        $stmt->execute();
        $result = $stmt->get_result();
        $data=[];
        while($row=$result->fetch_assoc()) $data[]=$row;
        return $data;
    }

    public function insertRequest($data) {
        $sql = "INSERT INTO suco_yeucau(masv,maphong,mota,ngaybao,trangthai)
                VALUES(?,?,?,?,'Chờ duyệt')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssss',$data['masv'],$data['maphong'],$data['mota'],$data['ngaybao']);
        return $stmt->execute();
    }
}

class RoomRepository {
    private $db;
    public function __construct() { $this->db = \Config\Database::getConnection(); }

    public function findByStudent($masv) {
        $sql = "SELECT p.* FROM phong p
                JOIN hopdong h ON p.maphong=h.maphong
                WHERE h.masv=? AND h.trangthai='Đang Hoạt Động'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s',$masv);
        $stmt->execute();
        $result=$stmt->get_result();
        $data=[];
        while($row=$result->fetch_assoc()) $data[]=$row;
        return $data;
    }

    public function getContract($masv) {
        $sql = "SELECT * FROM hopdong WHERE masv=?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s',$masv);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
