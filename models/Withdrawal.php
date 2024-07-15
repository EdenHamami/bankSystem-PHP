<?php
class Withdrawal {
    private $conn;
    private $table = 'withdrawals';

    public $withdrawal_id;
    public $account_id;
    public $amount;
    public $timestamp;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new withdrawal
    public function create() {
        // Start transaction
        $this->conn->beginTransaction();

        try {
            // Lock the account row
            $query = "SELECT balance FROM accounts WHERE account_id = :account_id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':account_id', $this->account_id);
            $stmt->execute();

            // Get the current balance
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($account) {
                $current_balance = $account['balance'];

                // Check if balance is sufficient
                if ($current_balance < $this->amount) {
                    throw new Exception("Insufficient balance.");
                }

                // Update the balance
                $new_balance = $current_balance - $this->amount;
                $query = "UPDATE accounts SET balance = :balance WHERE account_id = :account_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':balance', $new_balance);
                $stmt->bindParam(':account_id', $this->account_id);
                $stmt->execute();

                // Insert the withdrawal record
                $query = "INSERT INTO " . $this->table . " (account_id, amount, timestamp) VALUES (:account_id, :amount, NOW())";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':account_id', $this->account_id);
                $stmt->bindParam(':amount', $this->amount);
                $stmt->execute();

                // Commit the transaction
                $this->conn->commit();
                return true;
            } else {
                throw new Exception("Account not found.");
            }
        } catch (Exception $e) {
            // Rollback the transaction if an error occurred
            $this->conn->rollBack();
            throw new Exception("Error creating withdrawal: " . $e->getMessage());
        }
    }

    // Check if balance is sufficient for the withdrawal
    public function isBalanceSufficient() {
        $query = "SELECT balance FROM accounts WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_id', $this->account_id);
        $stmt->execute();

        $account = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($account) {
            return $account['balance'] >= $this->amount;
        }
        return false;
    }
}

