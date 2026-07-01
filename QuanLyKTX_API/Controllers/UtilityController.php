<?php
namespace Controllers;

use Core\Controller;
use Services\UtilityService;
use Services\ExportService;
use Services\ImportService;

class UtilityController extends Controller {
    private $utilityService;

    public function __construct() {
        $this->requireAuth();
        $this->utilityService = new UtilityService();
    }

    private function wantsJson() {
        return isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    private function normalizeUtilityCode($code, $prefix) {
        $code = trim($code);
        if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/i', $code, $matches)) {
            return strtoupper($code);
        }
        if (preg_match('/^(\d+)$/', $code, $matches)) {
            return $prefix . str_pad(intval($matches[1]), 3, '0', STR_PAD_LEFT);
        }
        return strtoupper($code);
    }

    /**
     * Hiển thị danh sách tiền điện và tiền nước (tabs)
     */
    public function index() {
        $tab = $this->getInput('tab') ?? 'electricity';

        if ($tab === 'water') {
            $data = $this->utilityService->getAllWater();
        } else {
            $data = $this->utilityService->getAllElectricity();
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $data]);
        }

        $this->view('utility/list', [
            'title' => 'Quản Lý Tiền Điện & Nước',
            'data' => $data,
            'tab' => $tab
        ]);
    }

    /**
     * Xử lý tìm kiếm
     */
    public function search() {
        $keyword = $this->getInput('search') ?? '';
        $tab = $this->getInput('tab') ?? 'electricity';

        if (empty($keyword)) {
            $this->redirect(BASE_URL . 'utility?tab=' . $tab);
        }

        if ($tab === 'water') {
            $data = $this->utilityService->searchWater($keyword);
        } else {
            $data = $this->utilityService->searchElectricity($keyword);
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $data]);
        }

        $this->view('utility/list', [
            'title' => 'Kết quả tìm kiếm',
            'data' => $data,
            'tab' => $tab,
            'keyword' => $keyword
        ]);
    }

    /**
     * Hiển thị form tính tiền
     */
    public function create() {
        $type = $this->getInput('type') ?? 'electricity'; // electricity hoặc water
        $rooms = $this->utilityService->getAllRooms();
        $invoiceCode = null;

        if ($type === 'electricity') {
            $invoiceCode = $this->utilityService->getNextElectricityCode('TD', 3);
        } elseif ($type === 'water') {
            $invoiceCode = $this->utilityService->getNextWaterCode('TN', 3);
        }

        $this->view('utility/form', [
            'title' => $type === 'water' ? 'Tính Tiền Nước' : 'Tính Tiền Điện',
            'type' => $type,
            'rooms' => $rooms,
            'invoiceCode' => $invoiceCode
        ]);
    }

    /**
     * Lưu hóa đơn mới
     */
    public function store() {
        if (!$this->isPost()) $this->redirect(BASE_URL . 'utility');

        $type = $this->getInput('type') ?? 'electricity';

        if ($type === 'water') {
            $matn = $this->getInput('matn');
            if (empty($matn)) {
                $matn = $this->utilityService->getNextWaterCode('TN', 3);
            } else {
                $matn = $this->normalizeUtilityCode($matn, 'TN');
            }

            $data = [
                'matn' => $matn,
                'maphong' => $this->getInput('maphong'),
                'gianuoc' => $this->getInput('gianuoc'),
                'ngay' => $this->getInput('ngay'),
                'trangthai' => $this->getInput('trangthai') ?? 'Chưa thanh toán'
            ];

            if ($this->utilityService->waterExists($data['matn'])) {
                $_SESSION['error'] = 'Mã hóa đơn nước đã tồn tại!';
                $this->redirect(BASE_URL . 'utility/create?type=water');
                return;
            }

            if ($this->utilityService->createWater($data)) {
                if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
                $_SESSION['success'] = 'Lưu hóa đơn tiền nước thành công!';
                $this->redirect(BASE_URL . 'utility?tab=water');
            } else {
                if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
                $_SESSION['error'] = 'Lỗi khi lưu dữ liệu!';
                $this->redirect(BASE_URL . 'utility/create?type=water');
            }
        } else {
            $matd = $this->getInput('matd');
            if (empty($matd)) {
                $matd = $this->utilityService->getNextElectricityCode('TD', 3);
            } else {
                $matd = $this->normalizeUtilityCode($matd, 'TD');
            }

            $data = [
                'matd' => $matd,
                'maphong' => $this->getInput('maphong'),
                'giadien' => $this->getInput('giadien'),
                'ngay' => $this->getInput('ngay'),
                'trangthai' => $this->getInput('trangthai') ?? 'Chưa thanh toán'
            ];

            if ($this->utilityService->electricityExists($data['matd'])) {
                $_SESSION['error'] = 'Mã hóa đơn điện đã tồn tại!';
                $this->redirect(BASE_URL . 'utility/create?type=electricity');
                return;
            }

            if ($this->utilityService->createElectricity($data)) {
                if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
                $_SESSION['success'] = 'Lưu hóa đơn tiền điện thành công!';
                $this->redirect(BASE_URL . 'utility?tab=electricity');
            } else {
                if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
                $_SESSION['error'] = 'Lỗi khi lưu dữ liệu!';
                $this->redirect(BASE_URL . 'utility/create?type=electricity');
            }
        }
    }

    /**
     * Xóa hóa đơn tiền điện
     */
    public function deleteElectricity($matd = null) {
        if (!$matd) {
            $_SESSION['error'] = 'Mã hóa đơn không hợp lệ!';
            $this->redirect(BASE_URL . 'utility');
            return;
        }

        if ($this->utilityService->deleteElectricity($matd)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Xóa hóa đơn tiền điện thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi xóa dữ liệu!';
        }
        $this->redirect(BASE_URL . 'utility?tab=electricity');
    }

    /**
     * Thanh toán hóa đơn tiền điện
     */
    public function payElectricity($matd = null) {
        if (!$matd) {
            $_SESSION['error'] = 'Mã hóa đơn không hợp lệ!';
            $this->redirect(BASE_URL . 'utility?tab=electricity');
            return;
        }

        if ($this->utilityService->markElectricityAsPaid($matd)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Thanh toán hóa đơn điện thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi cập nhật trạng thái!';
        }
        $this->redirect(BASE_URL . 'utility?tab=electricity');
    }

    /**
     * Xóa hóa đơn tiền nước
     */
    public function deleteWater($matn = null) {
        if (!$matn) {
            $_SESSION['error'] = 'Mã hóa đơn không hợp lệ!';
            $this->redirect(BASE_URL . 'utility');
            return;
        }

        if ($this->utilityService->deleteWater($matn)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Xóa hóa đơn tiền nước thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi xóa dữ liệu!';
        }
        $this->redirect(BASE_URL . 'utility?tab=water');
    }

    /**
     * Thanh toán hóa đơn tiền nước
     */
    public function payWater($matn = null) {
        if (!$matn) {
            $_SESSION['error'] = 'Mã hóa đơn không hợp lệ!';
            $this->redirect(BASE_URL . 'utility?tab=water');
            return;
        }

        if ($this->utilityService->markWaterAsPaid($matn)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Thanh toán hóa đơn nước thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi cập nhật trạng thái!';
        }
        $this->redirect(BASE_URL . 'utility?tab=water');
    }

    /**
     * API: Tính tiền điện
     */
    public function calculateElectricity() {
        if (!$this->isPost()) {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
            return;
        }

        $kwhUsed = (int) $this->getInput('kwh') ?? 0;

        // Bậc giá điện theo quy định (6 bậc lũy tiến)
        $tiers = [
            ['limit' => 50, 'price' => 1806],
            ['limit' => 50, 'price' => 1866],
            ['limit' => 100, 'price' => 2167],
            ['limit' => 100, 'price' => 2729],
            ['limit' => 100, 'price' => 3050],
            ['limit' => PHP_INT_MAX, 'price' => 3151]
        ];

        $total = 0;
        $remaining = $kwhUsed;
        $details = [];

        foreach ($tiers as $index => $tier) {
            if ($remaining <= 0) break;

            $used = min($remaining, $tier['limit']);
            $cost = $used * $tier['price'];
            $total += $cost;

            $details[] = [
                'tier' => $index + 1,
                'used' => $used,
                'price' => $tier['price'],
                'cost' => $cost
            ];

            $remaining -= $used;
        }

        $this->jsonResponse([
            'success' => true,
            'total' => $total,
            'details' => $details
        ]);
    }

    /**
     * API: Tính tiền nước
     */
    public function calculateWater() {
        if (!$this->isPost()) {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
            return;
        }

        $m3Used = (int) $this->getInput('m3') ?? 0;
        $pricePerM3 = 4000; // Giá cố định: 4000 VND/m³
        $total = $m3Used * $pricePerM3;

        $this->jsonResponse([
            'success' => true,
            'total' => $total,
            'm3' => $m3Used,
            'price' => $pricePerM3
        ]);
    }

    /**
     * Hiển thị form nhập khẩu
     */
    public function import() {
        $msg = ['text' => '', 'type' => ''];

        if ($this->isPost() && isset($_FILES['file'])) {
            $type = $this->getInput('type') ?? 'electricity';
            $importService = new ImportService();
            $result = $importService->importUtility($_FILES['file'], $type);

            if (isset($result['success']) && $result['success']) {
                $msg = [
                    'text' => "Nhập thành công! Đã thêm: {$result['imported']}, Bỏ qua: {$result['skipped']}",
                    'type' => 'success'
                ];
            } else {
                $msg = [
                    'text' => $result['message'] ?? 'Lỗi không xác định!',
                    'type' => 'error'
                ];
            }
        }

        $this->view('utility/import', ['msg' => $msg]);
    }

    /**
     * Xuất Excel
     */
    public function export() {
        $exportService = new ExportService();
        $type = $this->getInput('type') ?? 'electricity';
        $exportService->exportUtility($type);
    }
}