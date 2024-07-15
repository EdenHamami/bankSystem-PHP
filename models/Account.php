<?php
//models/Account.php
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
    public function create() {
        $userModel = new User($this->conn);
        if (!$userModel->exists($this->user_id)) {
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
    public function getById($account_id) {
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
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all accounts by user ID
    public function getByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update account balance
    public function updateBalance($account_id, $new_balance) {
        try {
            $this->conn->beginTransaction();

            $query = "SELECT balance FROM " . $this->table . " WHERE account_id = :account_id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':account_id', $account_id);
            $stmt->execute();

            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($account) {
                $query = "UPDATE " . $this->table . " SET balance = :balance WHERE account_id = :account_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':balance', $new_balance);
                $stmt->bindParam(':account_id', $account_id);

                if ($stmt->execute()) {
                    $this->conn->commit();
                    return true;
                }
            }

            $this->conn->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new Exception("Error updating balance: " . $e->getMessage());
        }
    }

    // Delete account by ID
    public function delete($account_id) {
        $query = "DELETE FROM " . $this->table . " WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':account_id', $account_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all transactions for an account
    public function getTransactions($account_id) {
        $query = "
            SELECT * FROM transfers WHERE from_account_id = :account_id
            UNION ALL
            SELECT * FROM transfers WHERE to_account_id = :account_id
            UNION ALL
            SELECT * FROM withdrawals WHERE account_id = :account_id
            UNION ALL
            SELECT * FROM deposits WHERE account_id = :account_id
            ORDER BY timestamp DESC
        ";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':account_id', $account_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        // Verify account ownership
        public function verifyOwnership($account_id) {
            $query = "SELECT user_id FROM " . $this->table . " WHERE account_id = :account_id";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':account_id', $account_id);
            $stmt->execute();
    
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($account && $account['user_id'] == $_SESSION['user_id']) {
                return true;
            }
            return false;
        }

    // Check if an account exists by ID
    public function exists($account_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE account_id = :account_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':account_id', $account_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
}
?>
