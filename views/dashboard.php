<?php
session_start();

// בדיקת אם המשתמש מחובר
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

// קבלת שם המשתמש מה-SESSION
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../public/dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>
        <p>What would you like to do?</p>
        <ul>
            <li><a href="../views/accounts.php">View My Accounts</a></li>
            <li><a href="create_account.php">Create New Account</a></li>
            <li><a href="deposit.php">Deposit Money</a></li>
            <li><a href="withdraw.php">Withdraw Money</a></li>
            <li><a href="transfer.php">Transfer Money</a></li>
            <li><a href="../controllers/logout.php">Logout</a></li> <!-- Updated logout link -->
        </ul>
    </div>
</body>
</html>
