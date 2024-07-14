<?php
session_start(); // Start session

include_once '../config/database.php';
include_once '../models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    // Function to handle creating a new user
    public function createUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            $this->user->password_hash = $_POST['password'];

            try {
                if ($this->user->createUser()) {
                    http_response_code(201);
                    echo json_encode(['message' => 'User created successfully.']);
                } else {
                    if (!$this->user->isEmailUnique()) {
                        http_response_code(409);
                        echo json_encode(['message' => 'User creation failed. Email already exists.']);
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'User creation failed.']);
                    }
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

    // Function to handle login
    public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user_data = $this->user->getUserByEmail($email);
            if ($user_data && $this->user->verifyPassword($email, $password)) {
                $_SESSION['user_id'] = $user_data['user_id']; // Save user_id in session
                header('Location: ../views/accounts.php');
                exit();
            } else {
                echo "Invalid email or password.";
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    // Function to display the login page
    public function showLogin() {
        include_once '../views/login.php';
    }

    // Function to display the register page
    public function showRegister() {
        include_once '../views/register.php';
    }

    // Function to handle reading all users
    public function readAllUsers() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $users = $this->user->getAllUsers();
            http_response_code(200);
            echo json_encode($users);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    // Function to handle reading a user by ID
    public function readUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $user_data = $this->user->getUserById($id);
                if ($user_data) {
                    http_response_code(200);
                    echo json_encode($user_data);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'User not found.']);
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

    // Function to handle updating a user
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $data = json_decode(file_get_contents("php://input"));
            $id = $_GET['id'] ?? null;
            if ($id) {
                $this->user->user_id = $id;
                $this->user->name = $data->name;
                $this->user->email = $data->email;
                if (isset($data->password)) {
                    $this->user->password_hash = $data->password;
                }

                if ($this->user->updateUser()) {
                    http_response_code(200);
                    echo json_encode(['message' => 'User updated successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'User update failed.']);
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

    // Function to handle deleting a user
    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $this->user->user_id = $id;
                if ($this->user->deleteUser($id)) {
                    http_response_code(200);
                    echo json_encode(['message' => 'User deleted successfully.']);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'User deletion failed.']);
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
}

// Instantiate the UserController
$controller = new UserController();

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'createUser':
        $controller->createUser();
        break;
    case 'login':
        $controller->loginUser();
        break;
    case 'showLogin':
        $controller->showLogin();
        break;
    case 'showRegister':
        $controller->showRegister();
        break;
    case 'readAllUsers':
        $controller->readAllUsers();
        break;
    case 'readUser':
        $controller->readUser();
        break;
    case 'updateUser':
        $controller->updateUser();
        break;
    case 'deleteUser':
        $controller->deleteUser();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>
