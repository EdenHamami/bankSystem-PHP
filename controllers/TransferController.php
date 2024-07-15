<?php
session_start(); // Start session

include_once '../config/database.php';
include_once '../models/Transfer.php';
include_once '../models/Account.php';

class TransferController {
    private $db;
    private $transfer;
    private $account;

    public function __construct() {
        // Create a database connection
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Create a new Transfer object
        $this->transfer = new Transfer($this->db);
        $this->account = new Account($this->db);
    }

    // Create a new transfer
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['from_account_id']) && isset($_POST['to_account_id']) && isset($_POST['amount'])) {
                // Handling form submission
                $this->transfer->from_account_id = $_POST['from_account_id'];
                $this->transfer->to_account_id = $_POST['to_account_id'];
                $this->transfer->amount = $_POST['amount'];
            } else {
                // Handling JSON request
                $data = json_decode(file_get_contents("php://input"));
                $this->transfer->from_account_id = $data->from_account_id;
                $this->transfer->to_account_id = $data->to_account_id;
                $this->transfer->amount = $data->amount;
            }

            if (!$this->account->verifyOwnership($this->transfer->from_account_id) || !$this->account->exists($this->transfer->to_account_id)) {
                http_response_code(403);
                echo json_encode(['message' => 'Unauthorized or target account does not exist']);
                return;
            }

            try {
                if ($this->transfer->create()) {
                    if (isset($_POST['from_account_id']) && isset($_POST['to_account_id']) && isset($_POST['amount'])) {
                        header('Location: ../views/transfer.php?success=Transfer successful.');
                        exit();
                    } else {
                        http_response_code(201);
                        echo json_encode(['message' => 'Transfer created successfully.']);
                    }
                } else {
                    if (isset($_POST['from_account_id']) && isset($_POST['to_account_id']) && isset($_POST['amount'])) {
                        header('Location: ../views/transfer.php?error=Transfer failed.');
                        exit();
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Transfer creation failed.']);
                    }
                }
            } catch (Exception $e) {
                if (isset($_POST['from_account_id']) && isset($_POST['to_account_id']) && isset($_POST['amount'])) {
                    header('Location: ../views/transfer.php?error=' . urlencode($e->getMessage()));
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

// Instantiate the TransferController
$controller = new TransferController();

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
