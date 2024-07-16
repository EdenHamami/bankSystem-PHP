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
        <form method="POST" action="../controllers/TransferController.php?action=create" id="transferForm">
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
        <div id="message"></div>
        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <script>
        document.getElementById('transferForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const jsonData = JSON.stringify(Object.fromEntries(formData));
            fetch(event.target.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: jsonData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                const messageDiv = document.getElementById('message');
                if (body.message) {
                    messageDiv.className = status >= 200 && status < 300 ? 'success' : 'error';
                    messageDiv.textContent = body.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const messageDiv = document.getElementById('message');
                messageDiv.className = 'error';
                messageDiv.textContent = 'An unexpected error occurred.';
            });
        });
    </script>
</body>
</html>
