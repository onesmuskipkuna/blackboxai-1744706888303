<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class FeeStructure {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addFeeStructure($class, $term, $feeItem, $amount) {
        try {
            $sql = "INSERT INTO fee_structure (class, term, fee_item, amount, created_at) VALUES (?, ?, ?, ?, NOW())";
            $this->db->query($sql, [$class, $term, $feeItem, $amount]);
            return true;
        } catch (Exception $e) {
            error_log("Add fee structure error: " . $e->getMessage());
            return false;
        }
    }

    public function getFeeStructureByClassAndTerm($class, $term) {
        try {
            $sql = "SELECT * FROM fee_structure WHERE class = ? AND term = ?";
            $stmt = $this->db->query($sql, [$class, $term]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get fee structure error: " . $e->getMessage());
            return [];
        }
    }

    // Additional CRUD methods can be added here
}
