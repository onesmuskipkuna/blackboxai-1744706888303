<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Invoice {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createInvoice($studentId, $term, $academicYear, $feeItems) {
        try {
            $totalAmount = 0;
            foreach ($feeItems as $item) {
                $totalAmount += $item['amount'];
            }

            $sql = "INSERT INTO invoices (student_id, term, academic_year, total_amount, balance, status, created_at)
                    VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
            $this->db->query($sql, [$studentId, $term, $academicYear, $totalAmount, $totalAmount]);
            $invoiceId = $this->db->getConnection()->lastInsertId();

            foreach ($feeItems as $item) {
                $sqlItem = "INSERT INTO invoice_items (invoice_id, fee_item_id, amount, balance)
                            VALUES (?, ?, ?, ?)";
                $this->db->query($sqlItem, [$invoiceId, $item['fee_item_id'], $item['amount'], $item['amount']]);
            }

            return $invoiceId;
        } catch (Exception $e) {
            error_log("Create invoice error: " . $e->getMessage());
            return false;
        }
    }

    public function getInvoicesByStudent($studentId) {
        try {
            $sql = "SELECT * FROM invoices WHERE student_id = ? ORDER BY created_at DESC";
            $stmt = $this->db->query($sql, [$studentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get invoices error: " . $e->getMessage());
            return [];
        }
    }

    public function getInvoiceItems($invoiceId) {
        try {
            $sql = "SELECT ii.*, fs.fee_item FROM invoice_items ii
                    JOIN fee_structure fs ON ii.fee_item_id = fs.id
                    WHERE ii.invoice_id = ?";
            $stmt = $this->db->query($sql, [$invoiceId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get invoice items error: " . $e->getMessage());
            return [];
        }
    }

    // Additional methods like update balance, mark as paid, etc. can be added here
}
