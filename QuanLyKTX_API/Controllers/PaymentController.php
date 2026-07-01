<?php
namespace Controllers;

use Core\Controller;
use Services\PaymentService;
use Services\RoomService;

class PaymentController extends Controller {
    private $paymentService;

    public function __construct() {
        $this->requireAuth();
        $this->paymentService = new PaymentService();
    }

    private function wantsJson() {
        return isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    public function index() {
        $keyword = $this->getInput('search');
        if ($keyword) {
            $payments = $this->paymentService->searchPayments($keyword);
        } else {
            $payments = $this->paymentService->getAllPayments();
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $payments]);
        }

        $this->view('payment/list', [
            'title' => 'Quản Lý Thanh Toán',
            'payments' => $payments,
            'keyword' => $keyword
        ]);
    }

    public function create() {
        $roomService = new RoomService();
        $rooms = $roomService->getAllRooms();
        $nextPaymentCode = $this->paymentService->getNextPaymentCode();

        $this->view('payment/form', [
            'title' => 'Tạo Hóa Đơn Mới',
            'rooms' => $rooms,
            'isEdit' => false,
            'nextPaymentCode' => $nextPaymentCode
        ]);
    }

    public function store() {
        if (!$this->isPost()) $this->redirect(BASE_URL . 'payment');
        
        $data = [
            'maphong' => $this->getInput('maphong'),
            'ngaytra' => $this->getInput('ngaytra')
        ];

        if ($this->paymentService->createPayment($data)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Thêm hóa đơn thanh toán thành công!';
            $this->redirect(BASE_URL . 'payment');
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi cơ sở dữ liệu khi lưu thanh toán';
            $this->redirect(BASE_URL . 'payment/create');
        }
    }

    public function edit($mathanhtoan = null) {
        if (!$mathanhtoan) $this->redirect(BASE_URL . 'payment');
        
        $payment = $this->paymentService->getPaymentById($mathanhtoan);
        if (!$payment) {
            $_SESSION['error'] = 'Giao dịch không tồn tại!';
            $this->redirect(BASE_URL . 'payment');
        }

        $roomService = new RoomService();
        $rooms = $roomService->getAllRooms();

        $this->view('payment/form', [
            'title' => 'Chỉnh Sửa Hóa Đơn',
            'payment' => $payment,
            'rooms' => $rooms,
            'isEdit' => true
        ]);
    }

    public function update($mathanhtoan = null) {
        if (!$this->isPost() || !$mathanhtoan) $this->redirect(BASE_URL . 'payment');
        
        $data = [
            'maphong' => $this->getInput('maphong'),
            'ngaytra' => $this->getInput('ngaytra'),
            'trangthai' => $this->getInput('trangthai')
        ];

        if ($this->paymentService->updatePayment($mathanhtoan, $data)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Cập nhật thanh toán thành công!';
            $this->redirect(BASE_URL . 'payment');
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi cập nhật thanh toán';
            $this->redirect(BASE_URL . 'payment/edit/' . $mathanhtoan);
        }
    }

    public function markAsPaid($mathanhtoan = null) {
        if (!$mathanhtoan) $this->redirect(BASE_URL . 'payment');
        
        if ($this->paymentService->updatePaymentStatus($mathanhtoan, 'Đã Thanh Toán')) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Đánh dấu thanh toán thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi cập nhật trạng thái!';
        }
        $this->redirect(BASE_URL . 'payment');
    }

    public function getRoomPrice() {
        if (!$this->isPost()) {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request'], 400);
            return;
        }

        $maphong = $this->getInput('maphong');
        $gia = $this->paymentService->getPriceByRoom($maphong);
        
        $this->jsonResponse(['success' => true, 'gia' => $gia]);
    }

    public function delete($mathanhtoan = null) {
        if (!$mathanhtoan) $this->redirect(BASE_URL . 'payment');
        
        if ($this->paymentService->deletePayment($mathanhtoan)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Xóa giao dịch thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi xóa giao dịch!';
        }
        $this->redirect(BASE_URL . 'payment');
    }
    public function export() {
        $exportService = new \Services\ExportService();
        $payments = $this->paymentService->getAllPayments();
        
        $headers = ['Mã Thanh Toán', 'Mã Phòng', 'Số Tiền', 'Ngày Trả', 'Trạng Thái'];
        $data = [];
        foreach ($payments as $p) {
            $data[] = [
                $p['mathanhtoan'], $p['maphong'], $p['sotien'], 
                $p['ngaytra'], $p['trangthai']
            ];
        }
        $exportService->exportCsv('DanhSachThanhToan.csv', $headers, $data);
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
                            'maphong' => $row['Mã Phòng'] ?? $row['maphong'] ?? '',
                            'sotien' => $row['Số Tiền'] ?? $row['sotien'] ?? 0,
                            'ngaytra' => $row['Ngày Trả'] ?? $row['ngaytra'] ?? '',
                            'trangthai' => $row['Trạng Thái'] ?? $row['trangthai'] ?? 'Chưa Thanh Toán'
                        ];
                        if (!empty($data['maphong'])) {
                            if ($this->paymentService->createPayment($data)) {
                                $count++;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                $_SESSION['success'] = "Đã nhập thành công $count hóa đơn!";
                $this->redirect(BASE_URL . 'payment');
            } else {
                $_SESSION['error'] = $result['error'];
            }
        }
        $this->view('payment/import', ['title' => 'Nhập Danh Sách Thanh Toán']);
    }
}