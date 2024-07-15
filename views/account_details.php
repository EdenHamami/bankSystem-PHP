<?php
session_start();

// בדיקת אם המשתמש מחובר
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include_once '../config/database.php';
include_once '../models/Account.php';

// יצירת חיבור למסד הנתונים
$database = Database::getInstance();
$db = $database->getConnection();
$account = new Account($db);

$account_id = $_GET['account_id'] ?? null;
if ($account_id) {
    $account_data = $account->getById($account_id);
} else {
    header('Location: accounts.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Details</title>
    <link rel="stylesheet" type="text/css" href="../public/accounts.css">
</head>
<body>
    <div class="container">
        <h2>Account Details</h2>
        <?php if ($account_data): ?>
            <p>Account ID: <?php echo htmlspecialchars($account_data['account_id']); ?></p>
            <p>User ID: <?php echo htmlspecialchars($account_data['user_id']); ?></p>
            <p>Balance: <?php echo htmlspecialchars($account_data['balance']); ?></p>
            <a href="accounts.php">Back to Accounts</a>
        <?php else: ?>
            <p>Account not found.</p>
            <a href="accounts.php">Back to Accounts</a>
        <?php endif; ?>
    </div>
</body>
</html>
