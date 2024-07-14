<?php
session_start();

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

    // Create a new user
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->user->name = $_POST['name'];
            $this->user->email = $_POST['email'];
            $this->user->password_hash = $_POST['password'];

            try {
                if ($this->user->create()) {
                    header('Location: ../views/login.php?success=Registration successful. Please login.');
                    exit();
                } else {
                    if (!$this->user->isEmailUnique()) {
                        header('Location: ../views/register.php?error=Email already exists.');
                        exit();
                    } else {
                        header('Location: ../views/register.php?error=Registration failed.');
                        exit();
                    }
                }
            } catch (Exception $e) {
                header('Location: ../views/register.php?error=' . urlencode($e->getMessage()));
                exit();
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    // Handle user login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user_data = $this->user->getByEmail($email);
            if ($user_data && $this->user->verifyPassword($email, $password)) {
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['user_name'] = $user_data['name'];
                header('Location: ../views/dashboard.php');
                exit();
            } else {
                header('Location: ../views/login.php?error=Invalid email or password.');
                exit();
            }
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    // Show login page
    public function showLogin() {
        include_once '../views/login.php';
    }

    // Show register page
    public function showRegister() {
        include_once '../views/register.php';
    }

    // Get all users
    public function getAll() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $users = $this->user->getAll();
            http_response_code(200);
            echo json_encode($users);
        } else {
            http_response_code(405);
            echo json_encode(['message' => 'Invalid request method.']);
        }
    }

    // Get user by ID
    public function getById() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $user_data = $this->user->getById($id);
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

    // Update user details
    public function update() {
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

                if ($this->user->update()) {
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

    // Delete user by ID
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $id = $_GET['id'] ?? null;
            if ($id) {
                $this->user->user_id = $id;
                if ($this->user->delete($id)) {
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
    case 'create':
        $controller->register();
        break;
    case 'login':
        $controller->login();
        break;
    case 'showLogin':
        $controller->showLogin();
        break;
    case 'showRegister':
        $controller->showRegister();
        break;
    case 'getAll':
        $controller->getAll();
        break;
    case 'getById':
        $controller->getById();
        break;
    case 'update':
        $controller->update();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Action not found.']);
        break;
}
?>
