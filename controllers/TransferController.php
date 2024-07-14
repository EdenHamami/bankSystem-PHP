<?php
include_once '../config/database.php';
include_once '../models/Transfer.php';
include_once '../models/Account.php';

class TransferController {
    private $db;
    private $transfer;

    public function __construct() {
        // Create a database connection
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Create a new Transfer object
        $this->transfer = new Transfer($this->db);
    }

    // Function to handle creating a new transfer
    public function createTransfer() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"));
            $this->transfer->from_account_id = $data->from_account_id;
            $this->transfer->to_account_id = $data->to_account_id;
            $this->transfer->amount = $data->amount;

            try {
                if ($this->transfer->createTransfer()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'Transfer created successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Transfer creation failed.']);
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

// Instantiate the TransferController
$controller = new TransferController();

// Determine the action based on the request
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $controller->createTransfer();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>
