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
        $isJsonRequest = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['from_account_id']) && isset($_POST['to_account_id']) && isset($_POST['amount'])) {
                // Handling form submission
                $this->transfer->from_account_id = $_POST['from_account_id'];
                $this->transfer->to_account_id = $_POST['to_account_id'];
                $this->transfer->amount = $_POST['amount'];
            } else {
                // Handling JSON request
                $data = json_decode(file_get_contents("php://input"));
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->transfer->from_account_id = $data->from_account_id;
                    $this->transfer->to_account_id = $data->to_account_id;
                    $this->transfer->amount = $data->amount;
                    $isJsonRequest = true;
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid JSON']);
                    return;
                }
            }

            if (!$this->account->verifyOwnership($this->transfer->from_account_id) || !$this->account->exists($this->transfer->to_account_id)) {
                $this->sendError('Unauthorized or target account does not exist', 403, $isJsonRequest);
                return;
            }

            try {
                if ($this->transfer->create()) {
                    $this->sendSuccess('Transfer successful.', $isJsonRequest);
                } else {
                    $this->sendError('Transfer failed.', 400, $isJsonRequest);
                }
            } catch (Exception $e) {
                $this->sendError($e->getMessage(), 400, $isJsonRequest);
            }
        } else {
            $this->sendError('Invalid request method.', 405, $isJsonRequest);
        }
    }

    private function sendError($message, $statusCode, $isJsonRequest) {
        if ($isJsonRequest) {
            http_response_code($statusCode);
            echo json_encode(['message' => $message]);
        } else {
            header('Location: ../views/transfer.php?error=' . urlencode($message));
            exit();
        }
    }

    private function sendSuccess($message, $isJsonRequest) {
        if ($isJsonRequest) {
            http_response_code(201);
            echo json_encode(['message' => $message]);
        } else {
            header('Location: ../views/transfer.php?success=' . urlencode($message));
            exit();
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
