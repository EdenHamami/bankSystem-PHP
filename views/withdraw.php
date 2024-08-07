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
    <title>Withdraw</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <h2>Withdraw</h2>
        <form method="POST" action="../controllers/WithdrawalController.php?action=create" id="withdrawForm">
            <label for="account_id">Account ID:</label>
            <input type="text" id="account_id" name="account_id" required>
            <br>
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required>
            <br>
            <button type="submit">Withdraw</button>
        </form>
        <div id="message"></div>
        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <script>
        document.getElementById('withdrawForm').addEventListener('submit', function(event) {
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
                    if (status >= 200 && status < 300) {
                        messageDiv.className = 'success';
                    } else {
                        messageDiv.className = 'error';
                    }
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
