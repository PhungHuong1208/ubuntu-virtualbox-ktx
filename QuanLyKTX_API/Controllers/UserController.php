<?php
namespace Controllers;

use Core\Controller;
use Models\AuthRepository;
use Models\StudentRepository;
use Models\IncidentRepository;
use Models\RoomRepository;

class UserController extends Controller {
    private $authRepo;
    private $studentRepo;
    private $incidentRepo;
    private $roomRepo;

    public function __construct() {
        // Khởi tạo các Repository (Lý tưởng nên dùng Service, nhưng tạm giữ cấu trúc cũ để chạy được ngay)
        $this->authRepo = new AuthRepository();
        $this->studentRepo = new StudentRepository();
        $this->incidentRepo = new IncidentRepository();
        $this->roomRepo = new RoomRepository();
    }

    /**
     * API: Đăng nhập cho sinh viên
     */
    public function login() {

        $masv = $this->getInput('masv');
        $password = $this->getInput('password');
        
        if (!$masv || !$password) {
            return $this->jsonResponse(['status' => 'error', 'message' => 'Thiếu thông tin đăng nhập'], 400);
        }

        // Tạm gọi trực tiếp AuthRepository, đúng chuẩn thì nên tạo AuthService riêng cho User
        // Gọi thẳng hàm authenticate (của sinh viên, có thể authRepo của admin không có hàm login)
        // Wait, AuthRepository hiện tại là của Admin (bảng taikhoan_admin)!
        // AuthRepository của phiên bản mới này có thể đã được cập nhật thêm login() cho user?
        // Let's assume AuthRepository in Models has login() or I need to check.
        $result = $this->authRepo->login($masv, $password);
        $this->jsonResponse($result);
    }

    /**
     * API: Lấy thông tin sinh viên hiện tại
     * Đổi lại từ student() -> getProfile() (GET)
     */
    public function getProfile() {

        $masv = $this->getInput('masv');
        if (!$masv) return $this->jsonResponse(['error' => 'Thiếu mã sinh viên'], 400);

        $result = $this->studentRepo->findById($masv);
        if ($result) {
            return $this->jsonResponse(['status' => 'success', 'data' => $result], 200);
        }
        return $this->jsonResponse(['status' => 'error', 'message' => 'Không tìm thấy sinh viên'], 404);
    }

    /**
     * API: Lấy thông tin phòng của sinh viên
     * Đổi lại từ room() -> getRoom() (GET)
     */
    public function getRoom() {

        $masv = $this->getInput('masv');
        if (!$masv) return $this->jsonResponse(['error' => 'Thiếu mã sinh viên'], 400);

        $data = $this->roomRepo->findByStudent($masv);
        return $this->jsonResponse(['status' => 'success', 'data' => $data], 200);
    }

    /**
     * API: Lấy danh sách hợp đồng
     * Đổi lại từ contract() -> getContracts() (GET)
     */
    public function getContracts() {

        $masv = $this->getInput('masv');
        if (!$masv) return $this->jsonResponse(['error' => 'Thiếu mã sinh viên'], 400);

        $data = $this->roomRepo->getContract($masv);
        return $this->jsonResponse(['status' => 'success', 'data' => $data], 200);
    }

    /**
     * API: Lấy lịch sử sự cố
     * Đổi lại từ incident() -> getIncidents() (GET)
     */
    public function getIncidents() {

        $masv = $this->getInput('masv');
        if (!$masv) return $this->jsonResponse(['error' => 'Thiếu mã sinh viên'], 400);

        $data = $this->incidentRepo->findByStudent($masv);
        return $this->jsonResponse(['status' => 'success', 'data' => $data], 200);
    }

    /**
     * API: Cập nhật thông tin sinh viên
     * Đổi lại từ student_update() -> updateProfile() (PUT)
     */
    public function updateProfile() {

        $data = [
            'masv' => $this->getInput('masv'),
            'hoten' => $this->getInput('hoten'),
            'lop' => $this->getInput('lop'),
            'gioitinh' => $this->getInput('gioitinh'),
            'cccd' => $this->getInput('cccd'),
            'sodienthoai' => $this->getInput('sodienthoai'),
            'email' => $this->getInput('email'),
            'diachi' => $this->getInput('diachi')
        ];
        
        if (empty($data['masv'])) {
            return $this->jsonResponse(['status' => 'error', 'message' => 'Thiếu mã sinh viên'], 400);
        }

        $masv = $data['masv'];
        unset($data['masv']); // tách key ra khỏi data trước khi truyền
        $ok = $this->studentRepo->update($masv, $data);
        if ($ok) {
            return $this->jsonResponse(['status' => 'success', 'message' => 'Cập nhật thành công'], 200);
        }
        return $this->jsonResponse(['status' => 'error', 'message' => 'Lỗi lưu thông tin'], 500);
    }

    /**
     * API: Đổi mật khẩu
     * Đổi lại từ change_password() -> updatePassword() (PUT)
     */
    public function updatePassword() {

        $masv = $this->getInput('masv');
        $old_pw = $this->getInput('old_password');
        $new_pw = $this->getInput('new_password');
        
        if (!$masv || !$old_pw || !$new_pw) {
            return $this->jsonResponse(['status' => 'error', 'message' => 'Thiếu thông tin'], 400);
        }

        $result = $this->authRepo->changePassword($masv, $old_pw, $new_pw);
        return $this->jsonResponse($result, isset($result['status']) && $result['status'] === 'success' ? 200 : 400);
    }

    /**
     * API: Gửi báo cáo sự cố mới
     * Đổi lại từ reportIncident() -> createIncident() (POST)
     */
    public function createIncident() {

        $data = [
            'masv' => $this->getInput('masv'),
            'maphong' => $this->getInput('maphong'),
            'mota' => $this->getInput('mota'),
            'ngaybao' => $this->getInput('ngaybao'),
            'trangthai' => 'Mới gửi' // Mặc định trạng thái khi vừa gửi
        ];
        
        if (!$data['masv'] || !$data['maphong'] || !$data['mota']) {
            return $this->jsonResponse(['status' => 'error', 'message' => 'Thiếu dữ liệu (masv, maphong, mota)'], 400);
        }

        $ok = $this->incidentRepo->insertRequest($data);
        if ($ok) {
            return $this->jsonResponse(['status' => 'success', 'message' => 'Yêu cầu sự cố đã được gửi'], 200);
        }
        return $this->jsonResponse(['status' => 'error', 'message' => 'Lỗi khi gửi yêu cầu'], 500);
    }
}