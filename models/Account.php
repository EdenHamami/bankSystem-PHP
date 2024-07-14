<?php
class Account {
    private $conn;
    private $table = 'accounts';

    public $account_id;
    public $user_id;
    public $balance;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new account
    public function createAccount() {
        // Check if user exists before creating account
        $userModel = new User($this->conn);
        if (!$userModel->userExists($this->user_id)) {
            throw new Exception("User does not exist.");
        }

        $query = "INSERT INTO " . $this->table . " (user_id, balance) VALUES (:user_id, :balance)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':balance', $this->balance);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get account details by ID
    public function getAccountById($account_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':account_id', $account_id);
        $stmt->execute();

        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($account) {
            $this->account_id = $account['account_id'];
            $this->user_id = $account['user_id'];
            $this->balance = $account['balance'];
        }
        return $account;
    }

    // Get all accounts
    public function getAllAccounts() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all accounts by user ID
    public function getAccountsByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update account balance
    public function updateBalance($account_id, $new_balance) {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // Lock the account row
            $query = "SELECT balance FROM " . $this->table . " WHERE account_id = :account_id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':account_id', $account_id);
            $stmt->execute();

            // Get the current balance
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($account) {
                // Update the balance to the new balance
                $query = "UPDATE " . $this->table . " SET balance = :balance WHERE account_id = :account_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':balance', $new_balance);
                $stmt->bindParam(':account_id', $account_id);

                if ($stmt->execute()) {
                    // Commit the transaction
                    $this->conn->commit();
                    return true;
                }
            }

            // Rollback the transaction if something failed
            $this->conn->rollBack();
            return false;
        } catch (PDOException $e) {
            // Rollback the transaction if an error occurred
            $this->conn->rollBack();
            throw new Exception("Error updating balance: " . $e->getMessage());
        }
    }

    // Delete account by ID
    public function deleteAccount($account_id) {
        $query = "DELETE FROM " . $this->table . " WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':account_id', $account_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all transactions for an account
    public function getAccountTransactions($account_id) {
        $query = "
            SELECT 'deposit' as type, amount, timestamp 
            FROM deposits 
            WHERE account_id = :account_id
            UNION ALL
            SELECT 'withdrawal' as type, amount, timestamp 
            FROM withdrawals 
            WHERE account_id = :account_id
            UNION ALL
            SELECT 'transfer_out' as type, amount, timestamp 
            FROM transfers 
            WHERE from_account_id = :account_id
            UNION ALL
            SELECT 'transfer_in' as type, amount, timestamp 
            FROM transfers 
            WHERE to_account_id = :account_id
            ORDER BY timestamp DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':account_id', $account_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
