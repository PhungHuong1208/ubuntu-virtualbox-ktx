<?php
require_once __DIR__ . '/../Models/Contract.php';

class ContractController extends Controller {
    private $contractRepo;

    public function __construct() {
        $this->ensureLoggedIn();
        $this->contractRepo = new ContractModel();
    }

    private function ensureLoggedIn() {
        if (!$this->getSession('user_id')) {
            $this->redirect(BASE_URL . 'auth');
        }
    }

    public function index() {
       // đảm bảo session đã start
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // kiểm tra login
    if (!$this->getSession('user_id')) {
        $this->redirect(BASE_URL . 'auth');
        return;
    }
        // dùng ContractModel thay vì StudentModel
    $contractModel = new ContractModel();

    // gọi hàm tìm dữ liệu; thay 'timmasv' bằng tên hàm thực tế nếu khác
    $hopdongs = $contractModel->timhopdong();

    if (!$hopdongs || empty($hopdongs)) {
        $_SESSION['error'] = 'Bạn chưa có hợp đồng.';
        $this->redirect(BASE_URL . 'auth/dashboard');
        return;
    }

    // truyền trực tiếp biến 'hopdong' vào view, lấy hợp đồng mới nhất (phần tử đầu tiên)
    $this->view('contract/list', [
        'title'   => 'Thông tin Hợp đồng',
        'hopdong' => $hopdongs[0]
    ]);



    }
}
    ?>