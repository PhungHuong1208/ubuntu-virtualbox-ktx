<?php
namespace Services;

use Models\PaymentRepository;

class PaymentService {
    private $paymentRepo;

    public function __construct() {
        $this->paymentRepo = new PaymentRepository();
    }

    public function getAllPayments() {
        return $this->paymentRepo->getAll();
    }

    public function getPaymentById($mathanhtoan) {
        return $this->paymentRepo->findById($mathanhtoan);
    }

    public function searchPayments($keyword) {
        return $this->paymentRepo->search($keyword);
    }

    public function createPayment($data) {
        return $this->paymentRepo->create($data);
    }

    public function getNextPaymentCode() {
        return $this->paymentRepo->getNextPaymentCode();
    }

    public function updatePayment($mathanhtoan, $data) {
        return $this->paymentRepo->update($mathanhtoan, $data);
    }

    public function getPriceByRoom($maphong) {
        return $this->paymentRepo->getPriceByRoom($maphong);
    }

    public function updatePaymentStatus($mathanhtoan, $trangthai) {
        return $this->paymentRepo->updateStatus($mathanhtoan, $trangthai);
    }

    public function deletePayment($mathanhtoan) {
        return $this->paymentRepo->delete($mathanhtoan);
    }
}