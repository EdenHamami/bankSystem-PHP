<?php
//views/withdraw.php

session_start();

// בדיקת אם המשתמש מחובר
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Withdraw Money</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <h2>Withdraw Money</h2>
        <form method="POST" action="../controllers/WithdrawalController.php?action=create">
            <label for="account_id">Account ID:</label>
            <input type="text" id="account_id" name="account_id" required>
            <br>
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required>
            <br>
            <button type="submit">Withdraw</button>
        </form>
        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
