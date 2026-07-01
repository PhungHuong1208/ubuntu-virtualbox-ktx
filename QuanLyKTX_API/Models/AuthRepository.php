<?php
namespace Models;

use Core\Repository;

class AuthRepository extends Repository {
    protected $table = 'taikhoan_admin';
    protected $primaryKey = 'username';

    /**
     * Tìm tài khoản quản trị theo username
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        return $user;
    }

    /**
     * API: Đăng nhập Sinh Viên (User)
     */
    public function login($masv, $password) {
        $sql = "SELECT * FROM taikhoan_user WHERE masv = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $masv);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && $user['password'] === $password) {
            // Lấy thêm thông tin tên sinh viên
            $sqlSv = "SELECT hoten FROM sinhvien WHERE masv = ?";
            $stmtSv = $this->db->prepare($sqlSv);
            $stmtSv->bind_param('s', $masv);
            $stmtSv->execute();
            $svResult = $stmtSv->get_result()->fetch_assoc();
            $stmtSv->close();

            return ['status' => 'success', 'data' => ['masv' => $masv, 'hoten' => $svResult['hoten'] ?? '']];
        }
        return ['status' => 'error', 'message' => 'Sai tài khoản hoặc mật khẩu'];
    }

    /**
     * API: Đổi mật khẩu Sinh Viên
     */
    public function changePassword($masv, $old_pw, $new_pw) {
        $sql = "SELECT password FROM taikhoan_user WHERE masv = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $masv);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($result && $result['password'] === $old_pw) {
            $updateSql = "UPDATE taikhoan_user SET password = ? WHERE masv = ?";
            $stmtUpdate = $this->db->prepare($updateSql);
            $stmtUpdate->bind_param('ss', $new_pw, $masv);
            $success = $stmtUpdate->execute();
            $stmtUpdate->close();
            
            if ($success) return ['status' => 'success', 'message' => 'Đổi mật khẩu thành công'];
        }
        return ['status' => 'error', 'message' => 'Mật khẩu cũ không chính xác'];
    }
}
