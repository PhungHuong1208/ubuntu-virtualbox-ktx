<?php
namespace Controllers;

use Core\Controller;
use Services\ContractService;
use Services\StudentService;
use Services\RoomService;

class ContractController extends Controller {
    private $contractService;

    public function __construct() {
        $this->requireAuth();
        $this->contractService = new ContractService();
    }

    private function wantsJson() {
        return isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    public function index() {
        $keyword = $this->getInput('search');
        if ($keyword) {
            $contracts = $this->contractService->searchContracts($keyword);
        } else {
            $contracts = $this->contractService->getAllContracts();
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $contracts]);
        }

        $this->view('contract/list', [
            'title' => 'Danh Sách Hợp Đồng',
            'contracts' => $contracts,
            'keyword' => $keyword
        ]);
    }

    public function create() {
        $studentService = new StudentService();
        $roomService = new RoomService();
        
        // Filter eligible students from service
        $students = $studentService->getEligibleStudents();
        $rooms = $roomService->getAllRooms();
        $nextMaHopDong = $this->contractService->generateNextMaHopDong();

        $this->view('contract/form', [
            'title' => 'Thêm Hợp Đồng Mới',
            'students' => $students,
            'rooms' => $rooms,
            'nextMaHopDong' => $nextMaHopDong,
            'isEdit' => false
        ]);
    }

    public function store() {
        if (!$this->isPost()) $this->redirect(BASE_URL . 'contract');
        
        $data = [
            'mahopdong' => $this->getInput('mahopdong'),
            'masv' => $this->getInput('masv'),
            'maphong' => $this->getInput('maphong'),
            'batdau' => $this->getInput('batdau'),
            'hethan' => $this->getInput('hethan'),
            'trangthai' => $this->getInput('trangthai', 'Đang Hoạt Động')
        ];

        try {
            if ($this->contractService->createContract($data)) {
                if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
                $_SESSION['success'] = 'Thêm hợp đồng thành công!';
                $this->redirect(BASE_URL . 'contract');
            } else {
                throw new \Exception('Lỗi cơ sở dữ liệu khi lưu hợp đồng');
            }
        } catch (\Exception $e) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(BASE_URL . 'contract/create');
        }
    }

    public function edit($mahopdong = null) {
        if (!$mahopdong) $this->redirect(BASE_URL . 'contract');
        
        $contract = $this->contractService->getContractById($mahopdong);
        if (!$contract) {
            $_SESSION['error'] = 'Hợp đồng không tồn tại!';
            $this->redirect(BASE_URL . 'contract');
        }

        $studentService = new StudentService();
        $roomService = new RoomService();
        
        // When editing, we need all students to correctly display the current one
        $students = $studentService->getAllStudents();
        $rooms = $roomService->getAllRooms();

        $this->view('contract/form', [
            'title' => 'Chỉnh Sửa Hợp Đồng',
            'contract' => $contract,
            'students' => $students,
            'rooms' => $rooms,
            'isEdit' => true
        ]);
    }

    public function update($mahopdong = null) {
        if (!$this->isPost() || !$mahopdong) $this->redirect(BASE_URL . 'contract');
        
        $data = [
            'masv' => $this->getInput('masv'),
            'maphong' => $this->getInput('maphong'),
            'batdau' => $this->getInput('batdau'),
            'hethan' => $this->getInput('hethan'),
            'trangthai' => $this->getInput('trangthai')
        ];

        try {
            if ($this->contractService->updateContract($mahopdong, $data)) {
                if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
                $_SESSION['success'] = 'Cập nhật hợp đồng thành công!';
                $this->redirect(BASE_URL . 'contract');
            } else {
                throw new \Exception('Lỗi cơ sở dữ liệu khi cập nhật hợp đồng');
            }
        } catch (\Exception $e) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(BASE_URL . 'contract/edit/' . $mahopdong);
        }
    }

    public function terminate($mahopdong = null) {
        if (!$mahopdong) $this->redirect(BASE_URL . 'contract');
        
        if ($this->contractService->terminateContract($mahopdong)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Hợp đồng đã được chấm dứt!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi chấm dứt hợp đồng!';
        }
        $this->redirect(BASE_URL . 'contract');
    }

    public function delete($mahopdong = null) {
        if (!$mahopdong) $this->redirect(BASE_URL . 'contract');
        
        if ($this->contractService->deleteContract($mahopdong)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Xóa hợp đồng thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi xóa hợp đồng!';
        }
        $this->redirect(BASE_URL . 'contract');
    }
    public function export() {
        $exportService = new \Services\ExportService();
        $contracts = $this->contractService->getAllContracts();
        
        $headers = ['Mã HĐ', 'Mã SV', 'Mã Phòng', 'Ngày Bắt Đầu', 'Ngày Hết Hạn', 'Trạng Thái'];
        $data = [];
        foreach ($contracts as $c) {
            $data[] = [
                $c['mahopdong'], $c['masv'], $c['maphong'], 
                $c['batdau'], $c['hethan'], $c['trangthai']
            ];
        }
        $exportService->exportCsv('DanhSachHopDong.csv', $headers, $data);
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
                            'mahopdong' => $row['Mã HĐ'] ?? $row['mahopdong'] ?? '',
                            'masv' => $row['Mã SV'] ?? $row['masv'] ?? '',
                            'maphong' => $row['Mã Phòng'] ?? $row['maphong'] ?? '',
                            'batdau' => $row['Ngày Bắt Đầu'] ?? $row['batdau'] ?? '',
                            'hethan' => $row['Ngày Hết Hạn'] ?? $row['hethan'] ?? '',
                            'trangthai' => $row['Trạng Thái'] ?? $row['trangthai'] ?? 'Đang Hoạt Động'
                        ];
                        if (!empty($data['mahopdong'])) {
                            if ($this->contractService->createContract($data)) {
                                $count++;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                $_SESSION['success'] = "Đã nhập thành công $count hợp đồng!";
                $this->redirect(BASE_URL . 'contract');
            } else {
                $_SESSION['error'] = $result['error'];
            }
        }
        $this->view('contract/import', ['title' => 'Nhập Danh Sách Hợp Đồng']);
    }
}