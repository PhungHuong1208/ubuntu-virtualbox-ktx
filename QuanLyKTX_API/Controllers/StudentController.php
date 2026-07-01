<?php
namespace Controllers;

use Core\Controller;
use Services\StudentService;

class StudentController extends Controller {
    private $studentService;

    public function __construct() {
        $this->requireAuth();
        $this->studentService = new StudentService();
    }

    private function wantsJson() {
        return isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    public function index() {
        $keyword = $this->getInput('keyword');
        if ($keyword) {
            $students = $this->studentService->searchStudents($keyword);
        } else {
            $students = $this->studentService->getAllStudents();
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $students, 'total' => count($students)]);
        }

        $this->view('student/list', [
            'title' => 'Danh Sách Sinh Viên',
            'students' => $students,
            'keyword' => $keyword,
            'total' => count($students)
        ]);
    }

    public function create() {
        $this->view('student/form', [
            'title' => 'Thêm Sinh Viên Mới',
            'isEdit' => false
        ]);
    }

    public function store() {
        if (!$this->isPost()) $this->redirect(BASE_URL . 'student');
        
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

        if ($this->studentService->createStudent($data)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true, 'message' => 'Thêm sinh viên thành công']);
            $_SESSION['success'] = 'Thêm sinh viên thành công!';
            $this->redirect(BASE_URL . 'student');
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => 'Lỗi thêm sinh viên'], 400);
            $_SESSION['error'] = 'Lỗi cơ sở dữ liệu khi thêm sinh viên!';
            $this->redirect(BASE_URL . 'student/create');
        }
    }

    public function edit($masv = null) {
        if (!$masv) $this->redirect(BASE_URL . 'student');
        
        $student = $this->studentService->getStudentById($masv);
        if (!$student) {
            $_SESSION['error'] = 'Sinh viên không tồn tại!';
            $this->redirect(BASE_URL . 'student');
        }

        $this->view('student/form', [
            'title' => 'Sửa Thông Tin Sinh Viên',
            'student' => $student,
            'isEdit' => true
        ]);
    }

    public function update($masv = null) {
        if (!$this->isPost() || !$masv) $this->redirect(BASE_URL . 'student');
        
        $data = [
            'hoten' => $this->getInput('hoten'),
            'lop' => $this->getInput('lop'),
            'gioitinh' => $this->getInput('gioitinh'),
            'cccd' => $this->getInput('cccd'),
            'sodienthoai' => $this->getInput('sodienthoai'),
            'email' => $this->getInput('email'),
            'diachi' => $this->getInput('diachi')
        ];

        if ($this->studentService->updateStudent($masv, $data)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Cập nhật sinh viên thành công!';
            $this->redirect(BASE_URL . 'student');
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi cập nhật sinh viên!';
            $this->redirect(BASE_URL . 'student/edit/' . $masv);
        }
    }

    public function delete($masv = null) {
        if (!$masv) $this->redirect(BASE_URL . 'student');
        
        if ($this->studentService->deleteStudent($masv)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Xóa sinh viên thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi xóa sinh viên! Có thể sinh viên đang có hợp đồng.';
        }
        $this->redirect(BASE_URL . 'student');
    }
    public function export() {
        $exportService = new \Services\ExportService();
        $students = $this->studentService->getAllStudents();
        
        $headers = ['Mã SV', 'Họ Tên', 'Lớp', 'Giới Tính', 'CCCD', 'SĐT', 'Email', 'Địa Chỉ'];
        $data = [];
        foreach ($students as $s) {
            $data[] = [
                $s['masv'], $s['hoten'], $s['lop'], $s['gioitinh'], 
                $s['cccd'], $s['sodienthoai'], $s['email'], $s['diachi']
            ];
        }
        $exportService->exportCsv('DanhSachSinhVien.csv', $headers, $data);
    }

    public function import() {
        if ($this->isPost() && isset($_FILES['file'])) {
            $importService = new \Services\ImportService();
            $result = $importService->parseCsv($_FILES['file']);
            
            if ($result['success']) {
                $count = 0;
                foreach ($result['data'] as $row) {
                    try {
                        $data = [
                            'masv' => $row['Mã SV'] ?? $row['masv'] ?? '',
                            'hoten' => $row['Họ Tên'] ?? $row['hoten'] ?? '',
                            'lop' => $row['Lớp'] ?? $row['lop'] ?? '',
                            'gioitinh' => $row['Giới Tính'] ?? $row['gioitinh'] ?? 'Nam',
                            'cccd' => $row['CCCD'] ?? $row['cccd'] ?? '',
                            'sodienthoai' => $row['SĐT'] ?? $row['sodienthoai'] ?? '',
                            'email' => $row['Email'] ?? $row['email'] ?? '',
                            'diachi' => $row['Địa Chỉ'] ?? $row['diachi'] ?? ''
                        ];
                        if (!empty($data['masv'])) {
                            if ($this->studentService->createStudent($data)) {
                                $count++;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                $_SESSION['success'] = "Đã nhập thành công $count sinh viên!";
                $this->redirect(BASE_URL . 'student');
            } else {
                $_SESSION['error'] = $result['error'];
            }
        }
        $this->view('student/import', ['title' => 'Nhập Danh Sách Sinh Viên']);
    }
}
