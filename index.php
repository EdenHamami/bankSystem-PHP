<?php
session_start();

$request = $_SERVER['REQUEST_URI'];

// ניתוב לפי הנתיב המבוקש
switch ($request) {
    case '/BankingApp/':
    case '/BankingApp/index.php':
        if (isset($_SESSION['user_id'])) {
            header("Location: views/dashboard.php");
        } else {
            header("Location: views/login.php");
        }
        break;
    default:
        // כלל ברירת המחדל - ניתוב ל-controllers בהתאם ל-rewrite rules
        include_once 'public/index.php';
        break;
}
exit();
