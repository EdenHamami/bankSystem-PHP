<?php
session_start(); // Start session

include_once '../config/database.php';
include_once '../models/Withdrawal.php';
include_once '../models/Account.php';

class WithdrawalController {
    private $db;
    private $withdrawal;
    private $account;

    public function __construct() {
        // Create a database connection
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Create a new Withdrawal object
        $this->withdrawal = new Withdrawal($this->db);
        $this->account = new Account($this->db);
    }

    // Create a new withdrawal
    public function create() {
        $isJsonRequest = false;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['account_id']) && isset($_POST['amount'])) {
                // Handling form submission
                $this->withdrawal->account_id = $_POST['account_id'];
                $this->withdrawal->amount = $_POST['amount'];
            } else {
                // Handling JSON request
                $data = json_decode(file_get_contents("php://input"));
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->withdrawal->account_id = $data->account_id;
                    $this->withdrawal->amount = $data->amount;
                    $isJsonRequest = true;
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid JSON']);
                    return;
                }
            }

            if (!$this->account->verifyOwnership($this->withdrawal->account_id)) {
                $this->sendError('Unauthorized', 403, $isJsonRequest);
                return;
            }

            try {
                if ($this->withdrawal->create()) {
                    $this->sendSuccess('Withdrawal successful.', $isJsonRequest);
                } else {
                    $this->sendError('Withdrawal failed.', 400, $isJsonRequest);
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
            header('Location: ../views/withdraw.php?error=' . urlencode($message));
            exit();
        }
    }

    private function sendSuccess($message, $isJsonRequest) {
        if ($isJsonRequest) {
            http_response_code(201);
            echo json_encode(['message' => $message]);
        } else {
            header('Location: ../views/withdraw.php?success=' . urlencode($message));
            exit();
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
