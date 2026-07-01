<?php
namespace Services;

use Models\StudentRepository;

class StudentService {
    private $studentRepo;

    public function __construct() {
        $this->studentRepo = new StudentRepository();
    }

    public function generateNextMaSV() {
        return $this->studentRepo->generateNextMaSV();
    }

    public function getAllStudents() {
        return $this->studentRepo->getAllStudents();
    }

    public function getEligibleStudents() {
        return $this->studentRepo->getEligibleStudents();
    }

    public function getStudentById($masv) {
        return $this->studentRepo->findById($masv);
    }

    public function searchStudents($keyword) {
        return $this->studentRepo->search($keyword);
    }

    public function createStudent($data) {
        return $this->studentRepo->create($data);
    }

    public function updateStudent($masv, $data) {
        return $this->studentRepo->update($masv, $data);
    }

    public function deleteStudent($masv) {
        return $this->studentRepo->delete($masv);
    }
}
