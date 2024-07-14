<?php
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

    // Function to handle creating a new withdrawal
    public function createWithdrawal() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            $this->withdrawal->account_id = $data->account_id;
            $this->withdrawal->amount = $data->amount;

            try {
                if ($this->withdrawal->createWithdrawal()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Withdrawal created successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Withdrawal creation failed.']);
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
}

// Instantiate the WithdrawalController
$controller = new WithdrawalController();

// Determine the action based on the request
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $controller->createWithdrawal();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>
