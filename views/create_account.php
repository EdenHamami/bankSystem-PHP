<?php
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
    <title>Create Account</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <h2>Create New Account</h2>
        <form method="POST" action="../controllers/AccountController.php?action=create">
            <label for="balance">Initial Balance:</label>
            <input type="number" id="balance" name="balance" required>
            <br>
            <button type="submit">Create Account</button>
        </form>
        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
