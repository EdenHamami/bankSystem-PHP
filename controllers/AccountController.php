<?php
session_start(); // Start session

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

    public function createAccount() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            $this->account->user_id = $data->user_id;
            $this->account->balance = $data->balance;

            try {
                if ($this->account->createAccount()) {
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

    public function getAccountById() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $account_id = $_GET['account_id'] ?? null;
            if ($account_id) {
                $account = $this->account->getAccountById($account_id);
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

    public function getAllAccounts() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $accounts = $this->account->getAllAccounts();
            http_response_code(200);
            echo json_encode($accounts);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    public function getAccountsByUserId() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $user_id = $_SESSION['user_id'] ?? null;
            if ($user_id) {
                $accounts = $this->account->getAccountsByUserId($user_id);
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

    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $account_id = $_GET['account_id'] ?? null;
            if ($account_id) {
                if ($this->account->deleteAccount($account_id)) {
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

    public function getAccountTransactions() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $account_id = $_GET['account_id'] ?? null;
            if ($account_id) {
                $transactions = $this->account->getAccountTransactions($account_id);
                if ($transactions) {
                    http_response_code(200);
                    echo json_encode($transactions);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'No transactions found for this account.']);
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
}

// Instantiate the AccountController
$controller = new AccountController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $controller->createAccount();
        break;
    case 'get':
        $controller->getAccountById();
        break;
    case 'getAll':
        $controller->getAllAccounts();
        break;
    case 'getByUserId':
        $controller->getAccountsByUserId();
        break;
    case 'update':
        $controller->updateBalance();
        break;
    case 'delete':
        $controller->deleteAccount();
        break;
    case 'getTransactions':
        $controller->getAccountTransactions();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>
