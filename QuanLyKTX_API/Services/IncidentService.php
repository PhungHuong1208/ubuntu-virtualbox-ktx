<?php
namespace Services;

use Models\IncidentRepository;

class IncidentService {
    private $incidentRepo;

    public function __construct() {
        $this->incidentRepo = new IncidentRepository();
    }

    public function generateNextMaSuCo() {
        return $this->incidentRepo->generateNextMaSuCo();
    }

    public function getAllIncidents() {
        return $this->incidentRepo->getAll();
    }

    public function getIncidentById($masuco) {
        return $this->incidentRepo->findById($masuco);
    }

    public function searchIncidents($keyword, $status = null) {
        return $this->incidentRepo->search($keyword, $status);
    }

    public function createIncident($data) {
        return $this->incidentRepo->create($data);
    }

    public function updateIncident($masuco, $data) {
        return $this->incidentRepo->update($masuco, $data);
    }

    public function deleteIncident($masuco) {
        return $this->incidentRepo->delete($masuco);
    }
}
