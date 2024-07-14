<?php
include_once '../config/database.php';
include_once '../models/Deposit.php';
include_once '../models/Account.php';

class DepositController {
    private $db;
    private $deposit;

    public function __construct() {
        // Create a database connection
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Create a new Deposit object
        $this->deposit = new Deposit($this->db);
    }

    // Function to handle creating a new deposit
    public function createDeposit() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            $this->deposit->account_id = $data->account_id;
            $this->deposit->amount = $data->amount;

            try {
                if ($this->deposit->createDeposit()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Deposit created successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Deposit creation failed.']);
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

// Instantiate the DepositController
$controller = new DepositController();

// Determine the action based on the request
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $controller->createDeposit();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>
