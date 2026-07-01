<?php
namespace Core;

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey;

    public function __construct() {
        $this->db = \Config\Database::getConnection();
    }
}
