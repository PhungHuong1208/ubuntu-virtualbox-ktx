<?php
namespace Services;

use Models\ContractRepository;

class ContractService {
    private $contractRepo;

    public function __construct() {
        $this->contractRepo = new ContractRepository();
    }

    public function generateNextMaHopDong() {
        return $this->contractRepo->generateNextMaHopDong();
    }

    public function getAllContracts() {
        return $this->contractRepo->getAll();
    }

    public function getContractById($mahopdong) {
        return $this->contractRepo->findById($mahopdong);
    }

    public function searchContracts($keyword) {
        return $this->contractRepo->search($keyword);
    }

    public function createContract($data) {
        // Business logic: Check expiration
        $today = date('Y-m-d');
        if ($data['hethan'] < $today) {
            if ($data['trangthai'] === 'Đang Hoạt Động') {
                throw new \Exception('Ngày hết hạn không hợp lệ cho hợp đồng Đang Hoạt Động.');
            }
        }
        
        return $this->contractRepo->create($data);
    }

    public function updateContract($mahopdong, $data) {
        // Business logic: check expiration when updating
        $today = date('Y-m-d');
        if ($data['hethan'] < $today && ($data['trangthai'] === 'Đang Hoạt Động' || empty($data['trangthai']))) {
            throw new \Exception('Không thể đổi trạng thái thành Đang Hoạt Động nếu hợp đồng đã hết hạn.');
        }

        return $this->contractRepo->update($mahopdong, $data);
    }

    public function terminateContract($mahopdong) {
        // Logic to terminate contract early
        $contract = $this->getContractById($mahopdong);
        if ($contract) {
            $data = [
                'masv' => $contract['masv'],
                'maphong' => $contract['maphong'],
                'batdau' => $contract['batdau'],
                'hethan' => date('Y-m-d'), // Hết hạn bằng hôm nay
                'trangthai' => 'Đã Chấm Dứt'
            ];
            return $this->contractRepo->update($mahopdong, $data);
        }
        return false;
    }

    public function deleteContract($mahopdong) {
        return $this->contractRepo->delete($mahopdong);
    }
}
