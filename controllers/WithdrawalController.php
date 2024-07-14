<?php
session_start(); // Start session

include_once '../config/database.php';
include_once '../models/Withdrawal.php';
include_once '../models/Account.php';

class WithdrawalController {
    private $db;
    private $withdrawal;

    public function __construct() {
        // Create a database connection
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Create a new Withdrawal object
        $this->withdrawal = new Withdrawal($this->db);
    }

    // Create a new withdrawal
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                // Handling form submission
                $this->withdrawal->account_id = $_POST['account_id'];
                $this->withdrawal->amount = $_POST['amount'];
            } else {
                // Handling JSON request
                $data = json_decode(file_get_contents("php://input"));
                $this->withdrawal->account_id = $data->account_id;
                $this->withdrawal->amount = $data->amount;
            }

            try {
                if ($this->withdrawal->create()) {
                    if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                        header('Location: ../views/dashboard.php?success=Withdrawal successful.');
                        exit();
                    } else {
                        http_response_code(201);
                        echo json_encode(['message' => 'Withdrawal created successfully.']);
                    }
                } else {
                    if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                        header('Location: ../views/withdraw.php?error=Withdrawal failed.');
                        exit();
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Withdrawal creation failed.']);
                    }
                }
            } catch (Exception $e) {
                if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                    header('Location: ../views/withdraw.php?error=' . urlencode($e->getMessage()));
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

// Instantiate the WithdrawalController
$controller = new WithdrawalController();

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
