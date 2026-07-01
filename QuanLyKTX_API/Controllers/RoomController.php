<?php
namespace Controllers;

use Core\Controller;
use Services\RoomService;

class RoomController extends Controller {
    private $roomService;

    public function __construct() {
        $this->requireAuth(); // Bảo vệ toàn bộ module này
        $this->roomService = new RoomService();
    }

    private function wantsJson() {
        return isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    public function index() {
        $keyword = $this->getInput('keyword');
        if ($keyword) {
            $rooms = $this->roomService->searchRooms($keyword);
        } else {
            $rooms = $this->roomService->getAllRooms();
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $rooms]);
        }

        $this->view('room/list', [
            'title' => 'Danh Sách Phòng',
            'rooms' => $rooms,
            'keyword' => $keyword
        ]);
    }

    public function create() {
        $this->view('room/form', [
            'title' => 'Thêm Phòng Mới',
            'isEdit' => false
        ]);
    }

    public function store() {
        if (!$this->isPost()) {
            if ($this->wantsJson()) $this->jsonResponse(['error' => 'Method Not Allowed'], 405);
            $this->redirect(BASE_URL . 'room');
        }

        $data = [
            'maphong' => $this->getInput('maphong'),
            'sophong' => $this->getInput('sophong'),
            'toa' => $this->getInput('toa'),
            'succhua' => $this->getInput('succhua'),
            'phonghientai' => $this->getInput('phonghientai', 0),
            'gia' => $this->getInput('gia'),
            'trangthai' => $this->getInput('trangthai', 'Trống')
        ];

        try {
            if ($this->roomService->createRoom($data)) {
                if ($this->wantsJson()) $this->jsonResponse(['success' => true, 'message' => 'Thêm phòng thành công']);
                $_SESSION['success'] = 'Thêm phòng thành công!';
                $this->redirect(BASE_URL . 'room');
            } else {
                throw new \Exception('Lỗi cơ sở dữ liệu khi thêm phòng.');
            }
        } catch (\Exception $e) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(BASE_URL . 'room/create');
        }
    }

    public function edit($maphong = null) {
        if (!$maphong) $this->redirect(BASE_URL . 'room');
        $room = $this->roomService->getRoomById($maphong);
        
        if (!$room) {
            $_SESSION['error'] = 'Phòng không tồn tại!';
            $this->redirect(BASE_URL . 'room');
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $room]);
        }

        $this->view('room/form', [
            'title' => 'Cập Nhật Phòng',
            'room' => $room,
            'isEdit' => true
        ]);
    }

    public function update($maphong = null) {
        if (!$this->isPost() || !$maphong) {
            $this->redirect(BASE_URL . 'room');
        }

        $data = [
            'sophong' => $this->getInput('sophong'),
            'toa' => $this->getInput('toa'),
            'succhua' => $this->getInput('succhua'),
            'phonghientai' => $this->getInput('phonghientai'),
            'gia' => $this->getInput('gia'),
            'trangthai' => $this->getInput('trangthai')
        ];

        try {
            if ($this->roomService->updateRoom($maphong, $data)) {
                if ($this->wantsJson()) $this->jsonResponse(['success' => true, 'message' => 'Cập nhật phòng thành công']);
                $_SESSION['success'] = 'Cập nhật phòng thành công!';
                $this->redirect(BASE_URL . 'room');
            } else {
                throw new \Exception('Lỗi cơ sở dữ liệu khi cập nhật phòng.');
            }
        } catch (\Exception $e) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(BASE_URL . 'room/edit/' . $maphong);
        }
    }

    public function delete($maphong = null) {
        if (!$maphong) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => 'Thiếu mã phòng'], 400);
            $this->redirect(BASE_URL . 'room');
        }

        if ($this->roomService->deleteRoom($maphong)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true, 'message' => 'Xóa phòng thành công']);
            $_SESSION['success'] = 'Xóa phòng thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => 'Lỗi khi xóa phòng'], 500);
            $_SESSION['error'] = 'Lỗi khi xóa phòng!';
        }

        $this->redirect(BASE_URL . 'room');
    }

    public function danhsach($maphong = null) {
        if (!$maphong) $this->redirect(BASE_URL . 'room');
        
        $room = $this->roomService->getStudentsInRoom($maphong);

        if (!$room) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false, 'error' => 'Phòng chưa có sinh viên nào!'], 404);
            $_SESSION['error'] = 'Phòng chưa có sinh viên nào!';
            $this->redirect(BASE_URL . 'room');
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $room]);
        }

        $this->view('room/danhsach', [
            'title' => 'Danh Sách Phòng',
            'room' => $room
        ]);
    }
    public function export() {
        $exportService = new \Services\ExportService();
        $rooms = $this->roomService->getAllRooms();
        
        $headers = ['Mã Phòng', 'Số Phòng', 'Tòa', 'Sức Chứa', 'Hiện Tại', 'Giá', 'Trạng Thái'];
        $data = [];
        foreach ($rooms as $r) {
            $data[] = [
                $r['maphong'], $r['sophong'], $r['toa'], $r['succhua'], 
                $r['phonghientai'], $r['gia'], $r['trangthai']
            ];
        }
        $exportService->exportCsv('DanhSachPhong.csv', $headers, $data);
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
                            'sophong' => $row['Số Phòng'] ?? $row['sophong'] ?? '',
                            'toa' => $row['Tòa'] ?? $row['toa'] ?? '',
                            'succhua' => $row['Sức Chứa'] ?? $row['succhua'] ?? 8,
                            'phonghientai' => $row['Hiện Tại'] ?? $row['phonghientai'] ?? 0,
                            'gia' => $row['Giá'] ?? $row['gia'] ?? 0,
                            'trangthai' => $row['Trạng Thái'] ?? $row['trangthai'] ?? 'Trống'
                        ];
                        if (!empty($data['maphong'])) {
                            if ($this->roomService->createRoom($data)) {
                                $count++;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                $_SESSION['success'] = "Đã nhập thành công $count phòng!";
                $this->redirect(BASE_URL . 'room');
            } else {
                $_SESSION['error'] = $result['error'];
            }
        }
        $this->view('room/import', ['title' => 'Nhập Danh Sách Phòng']);
    }
}