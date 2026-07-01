<?php
namespace Services;

use Models\RoomRepository;

class RoomService {
    private $roomRepo;

    public function __construct() {
        $this->roomRepo = new RoomRepository();
    }

    public function getAllRooms() {
        return $this->roomRepo->getAll();
    }

    public function getRoomById($maphong) {
        return $this->roomRepo->findById($maphong);
    }

    public function searchRooms($keyword) {
        return $this->roomRepo->search($keyword);
    }

    public function getStudentsInRoom($maphong) {
        return $this->roomRepo->svinroom($maphong);
    }

    public function createRoom($data) {
        // Business logic: Ensure the building letter matches the first character of maphong
        if (isset($data['maphong']) && isset($data['toa'])) {
            $firstChar = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $data['maphong']), 0, 1));
            if ($firstChar !== $data['toa']) {
                throw new \Exception("Mã phòng không khớp với Tòa.");
            }
        }
        
        // Ensure defaults
        if (!isset($data['phonghientai'])) $data['phonghientai'] = 0;
        if (!isset($data['trangthai'])) $data['trangthai'] = 'Trống';

        return $this->roomRepo->create($data);
    }

    public function updateRoom($maphong, $data) {
        // Prevent changing building letter logic can go here
        return $this->roomRepo->update($maphong, $data);
    }

    public function deleteRoom($maphong) {
        return $this->roomRepo->delete($maphong);
    }
}
