<?php
session_start();

// בדיקת אם המשתמש מחובר
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        <p>What would you like to do?</p>
        <ul>
            <li><a href="../controllers/AccountController.php?action=getByUserId">View My Accounts</a></li>
            <li><a href="create_account.php">Create New Account</a></li>
            <li><a href="deposit.php">Deposit Money</a></li>
            <li><a href="withdraw.php">Withdraw Money</a></li>
            <li><a href="transfer.php">Transfer Money</a></li>
        </ul>
    </div>
</body>
</html>
