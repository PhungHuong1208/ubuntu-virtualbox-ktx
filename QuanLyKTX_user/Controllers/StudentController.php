<?php
/**
 * Student Controller
 * Xử lý logic xác thực sinh viên
 */
require_once __DIR__ . '/../Models/Student.php';

class StudentController extends Controller {
    private $studentModel;

    public function __construct() {
        $this->ensureLoggedIn();
        $this->studentModel = new StudentModel();
    }
    /**
     * Kiểm tra đã login
     */
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

    $studentModel = new StudentModel();
    $student = $studentModel->timmasv();

    if (!$student) {
        $_SESSION['error'] = 'Không tìm thấy thông tin sinh viên.';
        $this->redirect(BASE_URL . 'auth/dashboard');
        return;
    }

    // truyền dữ liệu sinh viên vào view 'list' dưới dạng mảng để vòng lặp foreach trong list.php hoạt động
    $this->view('student/list', [
        'title' => 'Thông tin Sinh viên',
        'students' => [$student]
    ]);
}

    /**
     * Cập nhật sinh viên
     */
    public function update($masv = null) {
        if (!$this->isPost() || !$masv) {
            $this->redirect(BASE_URL . 'student');
        }

        $student = $this->studentModel->findByMaSV($masv);

        if (!$student) {
            $_SESSION['error'] = 'Sinh viên không tồn tại!';
            $this->redirect(BASE_URL . 'student');
        }

        $hoten = trim($this->getInput('hoten'));
        $lop = trim($this->getInput('lop'));
        $gioitinh = $this->getInput('gioitinh');
        $cccd = trim($this->getInput('cccd'));
        $sodienthoai = trim($this->getInput('sodienthoai'));
        $email = trim($this->getInput('email'));
        $diachi = trim($this->getInput('diachi'));

        if (empty($hoten) || empty($lop)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            $this->redirect(BASE_URL . 'student/edit/' . $masv);
        }

        $data = [
            'hoten' => $hoten,
            'lop' => $lop,
            'gioitinh' => $gioitinh,
            'cccd' => $cccd,
            'sodienthoai' => $sodienthoai,
            'email' => $email,
            'diachi' => $diachi
        ];

        if ($this->studentModel->updateStudent($masv, $data)) {
            $_SESSION['success'] = 'Cập nhật sinh viên thành công!';
            $this->redirect(BASE_URL . 'student');
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật sinh viên!';
            $this->redirect(BASE_URL . 'student/edit/' . $masv);
        }
    }
}


?>