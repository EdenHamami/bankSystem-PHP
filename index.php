<?php
session_start();

$request = $_SERVER['REQUEST_URI'];
$base = '/BankingApp/';

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

switch (true) {
    case $request === $base || $request === $base . 'index.php':
        if (is_logged_in()) {
            header("Location: views/dashboard.php");
        } else {
            header("Location: views/login.php");
        }
        break;
    
    case $request === $base . 'login':
        include 'controllers/UserController.php';
        $controller = new UserController();
        $controller->showLogin();
        break;
    
    case $request === $base . 'register':
        include 'controllers/UserController.php';
        $controller = new UserController();
        $controller->showRegister();
        break;
    
    case $request === $base . 'user/create':
        include 'controllers/UserController.php';
        $controller = new UserController();
        $controller->register();
        break;
    
    case $request === $base . 'user/login':
        include 'controllers/UserController.php';
        $controller = new UserController();
        $controller->login();
        break;
    
    case $request === $base . 'accounts':
        if (is_logged_in()) {
            include 'controllers/AccountController.php';
            $controller = new AccountController();
            $controller->getByUserId();
        } else {
            header("Location: views/login.php");
        }
        break;

    case preg_match('/^' . preg_quote($base, '/') . 'account\/create$/', $request):
        if (is_logged_in()) {
            include 'controllers/AccountController.php';
            $controller = new AccountController();
            $controller->create();
        } else {
            header("Location: views/login.php");
        }
        break;
    
    case preg_match('/^' . preg_quote($base, '/') . 'account\/(\d+)$/', $request, $matches):
        if (is_logged_in()) {
            include 'controllers/AccountController.php';
            $controller = new AccountController();
            $controller->getById($matches[1]);
        } else {
            header("Location: views/login.php");
        }
        break;

    default:
        http_response_code(404);
        echo "Page not found.";
        break;
}
?>
