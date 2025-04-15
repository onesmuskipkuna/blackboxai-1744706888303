<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class FeeBalance {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function carryForwardTermBalance($studentId, $fromTerm, $toTerm, $fromYear, $toYear) {
        try {
            // Fetch outstanding balances for the student in the fromTerm and fromYear
            $sql = "SELECT * FROM invoice_items ii
                    JOIN invoices i ON ii.invoice_id = i.id
                    WHERE i.student_id = ? AND i.term = ? AND i.academic_year = ? AND ii.balance > 0";
            $stmt = $this->db->query($sql, [$studentId, $fromTerm, $fromYear]);
            $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($balances as $balance) {
                // Insert new invoice item for the toTerm and toYear with carried forward balance
                $insertInvoiceSql = "INSERT INTO invoices (student_id, term, academic_year, total_amount, balance, status, created_at)
                                     VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
                $this->db->query($insertInvoiceSql, [$studentId, $toTerm, $toYear, $balance['balance'], $balance['balance']]);
                $newInvoiceId = $this->db->getConnection()->lastInsertId();

                $insertInvoiceItemSql = "INSERT INTO invoice_items (invoice_id, fee_item_id, amount, balance)
                                         VALUES (?, ?, ?, ?)";
                $this->db->query($insertInvoiceItemSql, [$newInvoiceId, $balance['fee_item_id'], $balance['balance'], $balance['balance']]);

                // Update old invoice item balance to zero or mark as carried forward
                $updateOldBalanceSql = "UPDATE invoice_items SET balance = 0 WHERE id = ?";
                $this->db->query($updateOldBalanceSql, [$balance['id']]);
            }
            return true;
        } catch (Exception $e) {
            error_log("Carry forward term balance error: " . $e->getMessage());
            return false;
        }
    }

    public function carryForwardClassBalance($studentId, $fromClass, $toClass, $academicYear) {
        try {
            // Fetch all outstanding balances for the student in the fromClass
            $sql = "SELECT * FROM invoice_items ii
                    JOIN invoices i ON ii.invoice_id = i.id
                    JOIN students s ON i.student_id = s.id
                    WHERE i.student_id = ? AND s.class = ? AND ii.balance > 0";
            $stmt = $this->db->query($sql, [$studentId, $fromClass]);
            $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($balances as $balance) {
                // Insert new invoice for the toClass with carried forward balance
                $insertInvoiceSql = "INSERT INTO invoices (student_id, term, academic_year, total_amount, balance, status, created_at)
                                     VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
                $this->db->query($insertInvoiceSql, [$studentId, 1, $academicYear, $balance['balance'], $balance['balance']]);
                $newInvoiceId = $this->db->getConnection()->lastInsertId();

                $insertInvoiceItemSql = "INSERT INTO invoice_items (invoice_id, fee_item_id, amount, balance)
                                         VALUES (?, ?, ?, ?)";
                $this->db->query($insertInvoiceItemSql, [$newInvoiceId, $balance['fee_item_id'], $balance['balance'], $balance['balance']]);

                // Update old invoice item balance to zero or mark as carried forward
                $updateOldBalanceSql = "UPDATE invoice_items SET balance = 0 WHERE id = ?";
                $this->db->query($updateOldBalanceSql, [$balance['id']]);
            }
            return true;
        } catch (Exception $e) {
            error_log("Carry forward class balance error: " . $e->getMessage());
            return false;
        }
    }
}
