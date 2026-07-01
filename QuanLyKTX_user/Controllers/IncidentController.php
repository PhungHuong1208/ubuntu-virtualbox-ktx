<?php
/**
 * Incident Controller (Phía Sinh Viên)
 * Sinh viên chỉ được XEM sự cố và THÊM (Báo cáo) sự cố. Không có quyền sửa/xóa/import/export.
 */
require_once __DIR__ . '/../Models/Incident.php';
require_once __DIR__ . '/../Models/Contract.php'; // Require thêm Model Hợp đồng để sử dụng

class IncidentController extends Controller {
    private $incidentRepo;

    public function __construct() {
        $this->ensureLoggedIn();
        $this->incidentRepo = new IncidentModel();
    }

    private function ensureLoggedIn() {
        if (!$this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth');
        }
    }

    // Hiển thị danh sách sự cố
    public function index() {
        // đảm bảo session đã start (nếu base Controller chưa làm)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // kiểm tra login
        if (!$this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth');
            return;
        }

        // --- KIỂM TRA HỢP ĐỒNG ---
        $contractModel = new ContractModel();
        $hopdongs = $contractModel->timhopdong();

        if (!$hopdongs || empty($hopdongs)) {
            $_SESSION['error'] = 'Không tìm thấy thông tin hợp đồng.';
            $this->redirect(BASE_URL . 'auth/dashboard');
            return;
        }
        // -------------------------

        $incidentModel = new IncidentModel();
        $incidents = $incidentModel->timsuco() ?: [];

        // Truyền toàn bộ danh sách sự cố sang view
        $this->view('incident/list', [
            'title'     => 'Danh sách Sự cố',
            'incidents' => $incidents
        ]);
    }

    // Gửi Yêu Cầu Sự Cố
    public function baocao() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!$this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth');
            return;
        }

        // --- KIỂM TRA HỢP ĐỒNG ---
        $contractModel = new ContractModel();
        $hopdongs = $contractModel->timhopdong();

        if (!$hopdongs || empty($hopdongs)) {
            $_SESSION['error'] = 'Không tìm thấy thông tin hợp đồng.';
            $this->redirect(BASE_URL . 'auth/dashboard');
            return;
        }
        // -------------------------

        require_once __DIR__ . '/../Models/Room.php';
        $roomModel = new RoomModel();
        $room = $roomModel->timphong();
        $maphong = $room['maphong'] ?? '';

        $incidentModel = new IncidentModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $masv = $this->getSession('masv');
            $maphong = $this->getInput('maphong');
            $mota = $this->getInput('mota');
            $ngaybao = $this->getInput('ngaybao') ?: date('Y-m-d');

            $result = $incidentModel->sendIncidentRequest($masv, $maphong, $mota, $ngaybao);

            if ($result['status'] === 'success') {
                $_SESSION['success'] = 'Báo cáo sự cố thành công!';
                $this->redirect(BASE_URL . 'incident/baocao');
                return;
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }

        $this->view('incident/baocao', [
            'title'    => 'Gửi Yêu Cầu Sự Cố',
            'masv'     => $this->getSession('masv'),
            'maphong'  => $maphong
        ]);
    }
}
?>