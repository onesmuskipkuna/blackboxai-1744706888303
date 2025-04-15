<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Report {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getDailyFeeCollection($date) {
        try {
            $sql = "SELECT SUM(amount) as total_collection FROM payments WHERE DATE(payment_date) = ?";
            $stmt = $this->db->query($sql, [$date]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_collection'] ?? 0;
        } catch (Exception $e) {
            error_log("Get daily fee collection error: " . $e->getMessage());
            return 0;
        }
    }

    public function getDailyExpenses($date) {
        try {
            $sql = "SELECT SUM(amount) as total_expenses FROM expenses WHERE expense_date = ?";
            $stmt = $this->db->query($sql, [$date]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_expenses'] ?? 0;
        } catch (Exception $e) {
            error_log("Get daily expenses error: " . $e->getMessage());
            return 0;
        }
    }

    public function getPayrollReport($startDate, $endDate) {
        try {
            $sql = "SELECT * FROM payroll WHERE payslip_date BETWEEN ? AND ?";
            $stmt = $this->db->query($sql, [$startDate, $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get payroll report error: " . $e->getMessage());
            return [];
        }
    }

    public function getFeeItemReport($feeItemId, $startDate, $endDate) {
        try {
            $sql = "SELECT SUM(p.amount) as total_paid FROM payments p
                    JOIN invoice_items ii ON p.invoice_item_id = ii.id
                    WHERE ii.fee_item_id = ? AND p.payment_date BETWEEN ? AND ?";
            $stmt = $this->db->query($sql, [$feeItemId, $startDate, $endDate]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_paid'] ?? 0;
        } catch (Exception $e) {
            error_log("Get fee item report error: " . $e->getMessage());
            return 0;
        }
    }

    public function getProfitAndLossReport($startDate, $endDate) {
        try {
            $totalCollection = $this->getTotalCollection($startDate, $endDate);
            $totalExpenses = $this->getTotalExpenses($startDate, $endDate);
            $profitLoss = $totalCollection - $totalExpenses;
            return [
                'total_collection' => $totalCollection,
                'total_expenses' => $totalExpenses,
                'profit_loss' => $profitLoss
            ];
        } catch (Exception $e) {
            error_log("Get profit and loss report error: " . $e->getMessage());
            return [];
        }
    }

    private function getTotalCollection($startDate, $endDate) {
        $sql = "SELECT SUM(amount) as total_collection FROM payments WHERE payment_date BETWEEN ? AND ?";
        $stmt = $this->db->query($sql, [$startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_collection'] ?? 0;
    }

    private function getTotalExpenses($startDate, $endDate) {
        $sql = "SELECT SUM(amount) as total_expenses FROM expenses WHERE expense_date BETWEEN ? AND ?";
        $stmt = $this->db->query($sql, [$startDate, $endDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_expenses'] ?? 0;
    }
}
