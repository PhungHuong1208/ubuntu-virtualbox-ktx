<?php
namespace Controllers;

use Core\Controller;
use Services\IncidentService;
use Services\RoomService;

class IncidentController extends Controller {
    private $incidentService;

    public function __construct() {
        $this->requireAuth();
        $this->incidentService = new IncidentService();
    }

    private function wantsJson() {
        return isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    public function index() {
        $keyword = $this->getInput('search');
        if ($keyword) {
            $incidents = $this->incidentService->searchIncidents($keyword);
        } else {
            $incidents = $this->incidentService->getAllIncidents();
        }

        if ($this->wantsJson()) {
            $this->jsonResponse(['success' => true, 'data' => $incidents]);
        }

        $this->view('incident/list', [
            'title' => 'Danh Sách Sự Cố',
            'incidents' => $incidents,
            'keyword' => $keyword
        ]);
    }

    public function create() {
        $roomService = new RoomService();
        $rooms = $roomService->getAllRooms();
        $nextMaSuCo = $this->incidentService->generateNextMaSuCo();

        $this->view('incident/form', [
            'title' => 'Báo Cáo Sự Cố Mới',
            'rooms' => $rooms,
            'nextMaSuCo' => $nextMaSuCo,
            'isEdit' => false
        ]);
    }

    public function store() {
        if (!$this->isPost()) $this->redirect(BASE_URL . 'incident');
        
        $data = [
            'maphong' => $this->getInput('maphong'),
            'mota' => $this->getInput('mota'),
            'ngaybao' => $this->getInput('ngaybao'),
            'trangthai' => $this->getInput('trangthai', 'Chờ Xử Lý')
        ];

        if ($this->incidentService->createIncident($data)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Báo cáo sự cố thành công!';
            $this->redirect(BASE_URL . 'incident');
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi cơ sở dữ liệu khi báo cáo sự cố!';
            $this->redirect(BASE_URL . 'incident/create');
        }
    }

    public function edit($masuco = null) {
        if (!$masuco) $this->redirect(BASE_URL . 'incident');
        
        $incident = $this->incidentService->getIncidentById($masuco);
        if (!$incident) {
            $_SESSION['error'] = 'Sự cố không tồn tại!';
            $this->redirect(BASE_URL . 'incident');
        }

        $roomService = new RoomService();
        $rooms = $roomService->getAllRooms();

        $this->view('incident/form', [
            'title' => 'Cập Nhật Sự Cố',
            'incident' => $incident,
            'rooms' => $rooms,
            'isEdit' => true
        ]);
    }

    public function update($masuco = null) {
        if (!$this->isPost() || !$masuco) $this->redirect(BASE_URL . 'incident');
        
        $data = [
            'maphong' => $this->getInput('maphong'),
            'mota' => $this->getInput('mota'),
            'ngaybao' => $this->getInput('ngaybao'),
            'trangthai' => $this->getInput('trangthai')
        ];

        if ($this->incidentService->updateIncident($masuco, $data)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Cập nhật sự cố thành công!';
            $this->redirect(BASE_URL . 'incident');
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi cập nhật sự cố';
            $this->redirect(BASE_URL . 'incident/edit/' . $masuco);
        }
    }

    public function delete($masuco = null) {
        if (!$masuco) $this->redirect(BASE_URL . 'incident');
        
        if ($this->incidentService->deleteIncident($masuco)) {
            if ($this->wantsJson()) $this->jsonResponse(['success' => true]);
            $_SESSION['success'] = 'Xóa sự cố thành công!';
        } else {
            if ($this->wantsJson()) $this->jsonResponse(['success' => false], 400);
            $_SESSION['error'] = 'Lỗi khi xóa sự cố!';
        }
        $this->redirect(BASE_URL . 'incident');
    }
    public function export() {
        $exportService = new \Services\ExportService();
        $incidents = $this->incidentService->getAllIncidents();
        
        $headers = ['Mã Sự Cố', 'Mã Phòng', 'Mô Tả', 'Ngày Báo', 'Trạng Thái'];
        $data = [];
        foreach ($incidents as $i) {
            $data[] = [
                $i['masuco'], $i['maphong'], $i['mota'], 
                $i['ngaybao'], $i['trangthai']
            ];
        }
        $exportService->exportCsv('DanhSachSuCo.csv', $headers, $data);
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
                            'mota' => $row['Mô Tả'] ?? $row['mota'] ?? '',
                            'ngaybao' => $row['Ngày Báo'] ?? $row['ngaybao'] ?? '',
                            'trangthai' => $row['Trạng Thái'] ?? $row['trangthai'] ?? 'Chờ Xử Lý'
                        ];
                        if (!empty($data['maphong'])) {
                            if ($this->incidentService->createIncident($data)) {
                                $count++;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                $_SESSION['success'] = "Đã nhập thành công $count sự cố!";
                $this->redirect(BASE_URL . 'incident');
            } else {
                $_SESSION['error'] = $result['error'];
            }
        }
        $this->view('incident/import', ['title' => 'Nhập Danh Sách Sự Cố']);
    }
}