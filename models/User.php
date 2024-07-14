<?php
class User {
    private $conn;
    private $table = 'users';

    public $user_id;
    public $name;
    public $email;
    public $password_hash;

    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new user
    public function createUser() {
        // Validate email format
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Check if email is unique
        if (!$this->isEmailUnique()) {
            throw new Exception("Email already in use.");
        }

        // Validate password length
        if (strlen($this->password_hash) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        // Hash the password
        $this->password_hash = password_hash($this->password_hash, PASSWORD_BCRYPT);

        // SQL query to insert new user into the Users table
        $query = "INSERT INTO " . $this->table . " (name, email, password_hash) VALUES (:name, :email, :password_hash)";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);

        // Bind parameters to the query
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);

        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get user by ID
    public function getUserById($user_id) {
        // SQL query to retrieve user details by user_id from the database
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Bind user_id parameter to the query
        $stmt->bindParam(':user_id', $user_id);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch the result
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set user properties
        if ($user) {
            $this->user_id = $user['user_id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->password_hash = $user['password_hash'];
        }
        
        return $user;
    }

    // Get user by email
    public function getUserByEmail($email) {
        // SQL query to retrieve user details by email from the database
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Bind email parameter to the query
        $stmt->bindParam(':email', $email);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch the result
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set user properties
        if ($user) {
            $this->user_id = $user['user_id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->password_hash = $user['password_hash'];
        }
        
        return $user;
    }

    // Update user details
    public function updateUser() {
        // Validate email format
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
    
        // Check if email is unique if it has been changed
        $originalUser = $this->getUserById($this->user_id);
        if ($originalUser['email'] !== $this->email && !$this->isEmailUnique()) {
            throw new Exception("Email already in use.");
        }
    
        // Validate password length if it has been changed
        if (!empty($this->password_hash) && strlen($this->password_hash) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }
    
        // Hash the password if it has been changed
        if (!empty($this->password_hash)) {
            $this->password_hash = password_hash($this->password_hash, PASSWORD_BCRYPT);
        }
    
        // SQL query to update user details in the Users table
        $query = "UPDATE " . $this->table . " SET name = :name, email = :email";
        if (!empty($this->password_hash)) {
            $query .= ", password_hash = :password_hash";
        }
        $query .= " WHERE user_id = :user_id";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
    
        // Bind parameters to the query
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        if (!empty($this->password_hash)) {
            $stmt->bindParam(':password_hash', $this->password_hash);
        }
        $stmt->bindParam(':user_id', $this->user_id);
    
        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
    
        // If the query failed
        return false;
    }
    
    // Delete user by ID
    public function deleteUser($user_id) {
        // SQL query to delete a user by user_id
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
    
        // Bind user_id parameter to the query
        $stmt->bindParam(':user_id', $user_id);
    
        // Execute the query
        if ($stmt->execute()) {
            return true;
        }
    
        // If the query failed
        return false;
    }

    // Verify user password
    public function verifyPassword($email, $password) {
        // SQL query to retrieve the password hash for the given email
        $query = "SELECT password_hash FROM " . $this->table . " WHERE email = :email";
    
        // Prepare the query
        $stmt = $this->conn->prepare($query);
    
        // Bind email parameter to the query
        $stmt->bindParam(':email', $email);
    
        // Execute the query
        $stmt->execute();
    
        // Fetch the result
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verify the provided password against the stored hash
        return $user && password_verify($password, $user['password_hash']);
    }

    // Check if email is unique
    public function isEmailUnique() {
        // SQL query to check if email exists
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE email = :email";
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
    
        // Bind email parameter to the query
        $stmt->bindParam(':email', $this->email);
    
        // Execute the query
        $stmt->execute();
    
        // Fetch the result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if count is greater than 0, which means email exists
        return $row['count'] == 0;
    }

    // Get all users
    public function getAllUsers() {
        // SQL query to retrieve all users
        $query = "SELECT * FROM " . $this->table;
        
        // Prepare the query
        $stmt = $this->conn->prepare($query);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch all results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        // Check if a user exists by ID
        public function userExists($user_id) {
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'] > 0;
        }
}
?>
