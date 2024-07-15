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
        $isJsonRequest = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                // Handling form submission
                $this->deposit->account_id = $_POST['account_id'];
                $this->deposit->amount = $_POST['amount'];
            } else {
                // Handling JSON request
                $data = json_decode(file_get_contents("php://input"));
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->deposit->account_id = $data->account_id;
                    $this->deposit->amount = $data->amount;
                    $isJsonRequest = true;
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid JSON']);
                    return;
                }
            }

            if (!$this->account->verifyOwnership($this->deposit->account_id)) {
                $this->sendError('Unauthorized', 403, $isJsonRequest);
                return;
            }

            try {
                if ($this->deposit->create()) {
                    $this->sendSuccess('Deposit successful.', $isJsonRequest);
                } else {
                    $this->sendError('Deposit failed.', 400, $isJsonRequest);
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
            header('Location: ../views/deposit.php?error=' . urlencode($message));
            exit();
        }
    }

    private function sendSuccess($message, $isJsonRequest) {
        if ($isJsonRequest) {
            http_response_code(201);
            echo json_encode(['message' => $message]);
        } else {
            header('Location: ../views/deposit.php?success=' . urlencode($message));
            exit();
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
