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
    <title>Transfer</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <h2>Transfer</h2>
        <form method="POST" action="../controllers/TransferController.php?action=create">
            <label for="from_account_id">From Account ID:</label>
            <input type="text" id="from_account_id" name="from_account_id" required>
            <br>
            <label for="to_account_id">To Account ID:</label>
            <input type="text" id="to_account_id" name="to_account_id" required>
            <br>
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required>
            <br>
            <button type="submit">Transfer</button>
        </form>
        <?php if (isset($_GET['success'])): ?>
            <div class="success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
