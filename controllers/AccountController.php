<?php
session_start();
//controllers/AccountController.php
include_once '../config/database.php';
include_once '../models/Account.php';
include_once '../models/User.php';

class AccountController {
    private $db;
    private $account;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->account = new Account($this->db);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $balance = $_POST['balance'] ?? null;
            $this->account->balance = $balance;
    
            if (isset($_SESSION['user_id'])) {
                $this->account->user_id = $_SESSION['user_id'];
            } else {
                $data = json_decode(file_get_contents("php://input"));
                $this->account->user_id = $data->user_id ?? null;
            }
    
            if ($this->account->user_id === null) {
                http_response_code(400);
                echo json_encode(['message' => 'User ID is required.']);
                return;
            }
    
            try {
                if ($this->account->create()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Account created successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Account creation failed.']);
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['message' => $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }
    

    public function getById() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $account_id = $_GET['account_id'] ?? null;
            if ($account_id) {
                $account = $this->account->getById($account_id);
                if ($account) {
                    http_response_code(200);
                    echo json_encode($account);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Account not found.']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Account ID is required.']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    public function getAll() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $accounts = $this->account->getAll();
            http_response_code(200);
            echo json_encode($accounts);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    public function getByUserId() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $user_id = $_SESSION['user_id'] ?? null;
            if ($user_id) {
                $accounts = $this->account->getByUserId($user_id);
                if ($accounts) {
                    http_response_code(200);
                    echo json_encode($accounts);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Accounts not found.']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'User ID is required.']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    public function updateBalance() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $data = json_decode(file_get_contents("php://input"));
            $account_id = $_GET['account_id'] ?? null;
            if ($account_id && isset($data->new_balance)) {
                if ($this->account->updateBalance($account_id, $data->new_balance)) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Balance updated successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Balance update failed.']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Account ID and new balance are required.']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $account_id = $_GET['account_id'] ?? null;
            if ($account_id) {
                if ($this->account->delete($account_id)) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Account deleted successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Account deletion failed.']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Account ID is required.']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }
    public function getTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $account_id = $_GET['account_id'] ?? null;
            if ($account_id) {
                try {
                    $transactions = $this->account->getTransactions($account_id);
                    if ($transactions) {
                        http_response_code(200);
                        echo json_encode($transactions);
                    } else {
                        http_response_code(404);
                        echo json_encode(['message' => 'No transactions found for this account.']);
                    }
                } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Account ID is required.']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }
    
    
    public function verifyOwnership($account_id) {
        $user_id = $_SESSION['user_id'] ?? null;
        if ($user_id) {
            $account = $this->account->getById($account_id);
            if ($account && $account['user_id'] == $user_id) {
                return true;
            }
        }
        return false;
    }
    public function accountExists($account_id) {
        $account = $this->account->getById($account_id);
        return $account !== false;
    }
}

// Instantiate the AccountController
$controller = new AccountController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'getById':
        $controller->getById();
        break;
    case 'getAll':
        $controller->getAll();
        break;
    case 'getByUserId':
        $controller->getByUserId();
        break;
    case 'updateBalance':
        $controller->updateBalance();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'getTransactions':
        $controller->getTransactions();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>