<?php
namespace Services;

use Models\UtilityRepository;

class UtilityService {
    private $utilityRepo;

    public function __construct() {
        $this->utilityRepo = new UtilityRepository();
    }

    /**
     * Lấy tất cả tiền điện
     */
    public function getAllElectricity() {
        return $this->utilityRepo->getAllElectricity();
    }

    /**
     * Lấy tất cả tiền nước
     */
    public function getAllWater() {
        return $this->utilityRepo->getAllWater();
    }

    /**
     * Tìm kiếm tiền điện
     */
    public function searchElectricity($keyword) {
        return $this->utilityRepo->searchElectricity($keyword);
    }

    /**
     * Tìm kiếm tiền nước
     */
    public function searchWater($keyword) {
        return $this->utilityRepo->searchWater($keyword);
    }

    /**
     * Tìm tiền điện theo mã
     */
    public function findElectricityById($matd) {
        return $this->utilityRepo->findElectricityById($matd);
    }

    /**
     * Tìm tiền nước theo mã
     */
    public function findWaterById($matn) {
        return $this->utilityRepo->findWaterById($matn);
    }

    /**
     * Tạo hóa đơn tiền điện mới
     */
    public function createElectricity($data) {
        return $this->utilityRepo->createElectricity($data);
    }

    /**
     * Tạo hóa đơn tiền nước mới
     */
    public function createWater($data) {
        return $this->utilityRepo->createWater($data);
    }

    /**
     * Kiểm tra mã hóa đơn điện đã tồn tại
     */
    public function electricityExists($matd) {
        return $this->utilityRepo->electricityExists($matd);
    }

    /**
     * Kiểm tra mã hóa đơn nước đã tồn tại
     */
    public function waterExists($matn) {
        return $this->utilityRepo->waterExists($matn);
    }

    /**
     * Tạo mã hóa đơn điện tự động
     */
    public function getNextElectricityCode($prefix = 'TD', $length = 3) {
        return $this->utilityRepo->getNextElectricityCode($prefix, $length);
    }

    /**
     * Tạo mã hóa đơn nước tự động
     */
    public function getNextWaterCode($prefix = 'TN', $length = 3) {
        return $this->utilityRepo->getNextWaterCode($prefix, $length);
    }

    /**
     * Xóa hóa đơn tiền điện
     */
    public function deleteElectricity($matd) {
        return $this->utilityRepo->deleteElectricity($matd);
    }

    /**
     * Đánh dấu hóa đơn điện đã thanh toán
     */
    public function markElectricityAsPaid($matd) {
        return $this->utilityRepo->markElectricityAsPaid($matd);
    }

    /**
     * Xóa hóa đơn tiền nước
     */
    public function deleteWater($matn) {
        return $this->utilityRepo->deleteWater($matn);
    }

    /**
     * Đánh dấu hóa đơn nước đã thanh toán
     */
    public function markWaterAsPaid($matn) {
        return $this->utilityRepo->markWaterAsPaid($matn);
    }

    /**
     * Lấy tất cả phòng (để dropdown)
     */
    public function getAllRooms() {
        return $this->utilityRepo->getAllRooms();
    }

    /**
     * Cập nhật hóa đơn tiền điện
     */
    public function updateElectricity($data) {
        return $this->utilityRepo->updateElectricity($data);
    }

    /**
     * Cập nhật hóa đơn tiền nước
     */
    public function updateWater($data) {
        return $this->utilityRepo->updateWater($data);
    }
}