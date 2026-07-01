<?php
/**
 * Room Controller (Phía Sinh Viên - User)
 * Sinh viên xem DANH SÁCH TẤT CẢ CÁC PHÒNG để tra cứu giá/thông tin (Trạng thái Read Only).
 */
require_once __DIR__ . '/../Models/Room.php';

class RoomController extends Controller {
    private $roomModel;

    public function __construct() {
        $this->ensureLoggedIn();
        $this->roomModel = new RoomModel();
    }

    private function ensureLoggedIn() {
        if (!$this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth');
        }
    }

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

            $roomModel = new RoomModel();
            $room = $roomModel->timphong();

            if (!$room || empty($room)) {
                $_SESSION['error'] = 'Bạn chưa có trong phòng.';
                $this->redirect(BASE_URL . 'auth/dashboard');
                return;
            }

    $this->view('room/list', [
        'title'   => 'Thông tin Phòng của tôi',
        'room'    => $room
    ]);
        }
}
?>