<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Payroll {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addPayroll($staffName, $position, $basicSalary, $allowances, $deductions, $loan, $payslipDate) {
        try {
            $netPay = $basicSalary + $allowances - $deductions - $loan;
            if ($netPay < 0) {
                $netPay = 0;
            }
            $sql = "INSERT INTO payroll (staff_name, position, basic_salary, allowances, deductions, loan, net_pay, payslip_date, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $this->db->query($sql, [$staffName, $position, $basicSalary, $allowances, $deductions, $loan, $netPay, $payslipDate]);
            return true;
        } catch (Exception $e) {
            error_log("Add payroll error: " . $e->getMessage());
            return false;
        }
    }

    public function getPayrollByDateRange($startDate, $endDate) {
        try {
            $sql = "SELECT * FROM payroll WHERE payslip_date BETWEEN ? AND ? ORDER BY payslip_date DESC";
            $stmt = $this->db->query($sql, [$startDate, $endDate]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get payroll error: " . $e->getMessage());
            return [];
        }
    }
}
