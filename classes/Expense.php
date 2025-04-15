<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Expense {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addExpense($category, $description, $date, $amount) {
        try {
            $sql = "INSERT INTO expenses (category, description, expense_date, amount, created_at)
                    VALUES (?, ?, ?, ?, NOW())";
            $this->db->query($sql, [$category, $description, $date, $amount]);
            return true;
        } catch (Exception $e) {
            error_log("Add expense error: " . $e->getMessage());
            return false;
        }
    }

    public function getExpensesByDateRange($startDate, $endDate) {
        try {
            $sql = "SELECT * FROM expenses WHERE expense_date BETWEEN ? AND ? ORDER BY expense_date DESC";
            $stmt = $this->db->query($sql, [$startDate, $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get expenses error: " . $e->getMessage());
            return [];
        }
    }
}
