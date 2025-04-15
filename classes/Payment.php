<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Payment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function recordPayment($invoiceId, $payments, $paymentMode, $remarks, $createdBy) {
        try {
            $this->db->getConnection()->beginTransaction();

            $totalPaid = 0;
            foreach ($payments as $itemId => $amount) {
                if ($amount <= 0) {
                    continue;
                }
                // Insert payment record
                $sql = "INSERT INTO payments (invoice_id, invoice_item_id, amount, payment_mode, remarks, payment_date, created_by)
                        VALUES (?, ?, ?, ?, ?, NOW(), ?)";
                $this->db->query($sql, [$invoiceId, $itemId, $amount, $paymentMode, $remarks, $createdBy]);

                // Update invoice_items balance
                $updateSql = "UPDATE invoice_items SET balance = balance - ? WHERE id = ?";
                $this->db->query($updateSql, [$amount, $itemId]);

                $totalPaid += $amount;
            }

            // Update invoice balance
            $updateInvoiceSql = "UPDATE invoices SET balance = balance - ? WHERE id = ?";
            $this->db->query($updateInvoiceSql, [$totalPaid, $invoiceId]);

            // Optionally update invoice status if fully paid
            $checkSql = "SELECT balance FROM invoices WHERE id = ?";
            $stmt = $this->db->query($checkSql, [$invoiceId]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($invoice && $invoice['balance'] <= 0) {
                $statusSql = "UPDATE invoices SET status = 'paid' WHERE id = ?";
                $this->db->query($statusSql, [$invoiceId]);
            }

            $this->db->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->db->getConnection()->rollBack();
            error_log("Record payment error: " . $e->getMessage());
            return false;
        }
    }

    public function getPaymentsByInvoice($invoiceId) {
        try {
            $sql = "SELECT * FROM payments WHERE invoice_id = ? ORDER BY payment_date DESC";
            $stmt = $this->db->query($sql, [$invoiceId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get payments error: " . $e->getMessage());
            return [];
        }
    }
}
