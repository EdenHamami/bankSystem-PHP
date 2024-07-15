<?php
class Transfer {
    private $conn;
    private $table = 'transfers';

    public $transfer_id;
    public $from_account_id;
    public $to_account_id;
    public $amount;
    public $timestamp;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new transfer
    public function create() {
        // Start transaction
        $this->conn->beginTransaction();

        try {
            // Lock the rows for both accounts
            $query = "SELECT balance FROM accounts WHERE account_id = :from_account_id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':from_account_id', $this->from_account_id);
            $stmt->execute();
            $from_account = $stmt->fetch(PDO::FETCH_ASSOC);

            $query = "SELECT balance FROM accounts WHERE account_id = :to_account_id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':to_account_id', $this->to_account_id);
            $stmt->execute();
            $to_account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($from_account && $to_account) {
                $from_balance = $from_account['balance'];
                $to_balance = $to_account['balance'];

                // Check if balance is sufficient
                if ($from_balance < $this->amount) {
                    throw new Exception("Insufficient balance in the from account.");
                }

                // Update the balances
                $new_from_balance = $from_balance - $this->amount;
                $new_to_balance = $to_balance + $this->amount;

                $query = "UPDATE accounts SET balance = :balance WHERE account_id = :account_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':balance', $new_from_balance);
                $stmt->bindParam(':account_id', $this->from_account_id);
                $stmt->execute();

                $stmt->bindParam(':balance', $new_to_balance);
                $stmt->bindParam(':account_id', $this->to_account_id);
                $stmt->execute();

                // Insert the transfer record
                $query = "INSERT INTO " . $this->table . " (from_account_id, to_account_id, amount, timestamp) VALUES (:from_account_id, :to_account_id, :amount, NOW())";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':from_account_id', $this->from_account_id);
                $stmt->bindParam(':to_account_id', $this->to_account_id);
                $stmt->bindParam(':amount', $this->amount);
                $stmt->execute();

                // Commit the transaction
                $this->conn->commit();
                return true;
            } else {
                throw new Exception("One or both accounts not found.");
            }
        } catch (Exception $e) {
            // Rollback the transaction if an error occurred
            $this->conn->rollBack();
            throw new Exception("Error creating transfer: " . $e->getMessage());
        }
    }
}

