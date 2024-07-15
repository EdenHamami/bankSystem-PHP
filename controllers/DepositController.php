<?php
session_start(); // Start session

include_once '../config/database.php';
include_once '../models/Deposit.php';
include_once '../models/Account.php';

class DepositController {
    private $db;
    private $deposit;
    private $account;

    public function __construct() {
        // Create a database connection
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Create a new Deposit object
        $this->deposit = new Deposit($this->db);
        $this->account = new Account($this->db);
    }

    // Create a new deposit
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                // Handling form submission
                $this->deposit->account_id = $_POST['account_id'];
                $this->deposit->amount = $_POST['amount'];
            } else {
                // Handling JSON request
                $data = json_decode(file_get_contents("php://input"));
                $this->deposit->account_id = $data->account_id;
                $this->deposit->amount = $data->amount;
            }

            if (!$this->account->verifyOwnership($this->deposit->account_id)) {
                http_response_code(403);
                echo json_encode(['message' => 'Unauthorized']);
                return;
            }

            try {
                if ($this->deposit->create()) {
                    if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                        header('Location: ../views/dashboard.php?success=Deposit successful.');
                        exit();
                    } else {
                        http_response_code(201);
                        echo json_encode(['message' => 'Deposit created successfully.']);
                    }
                } else {
                    if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                        header('Location: ../views/deposit.php?error=Deposit failed.');
                        exit();
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Deposit creation failed.']);
                    }
                }
            } catch (Exception $e) {
                if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                    header('Location: ../views/deposit.php?error=' . urlencode($e->getMessage()));
                    exit();
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => $e->getMessage()]);
                }
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }
}

// Instantiate the DepositController
$controller = new DepositController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $controller->create();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>
