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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('create-account-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const balance = document.getElementById('balance').value;
                const formData = new FormData();
                formData.append('balance', balance);

                fetch('../controllers/AccountController.php?action=create', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    const messageDiv = document.getElementById('message');
                    if (data.message) {
                        messageDiv.innerHTML = data.message;
                    } else {
                        messageDiv.innerHTML = 'An unexpected error occurred.';
                    }
                })
                .catch(error => {
                    document.getElementById('message').innerHTML = 'An error occurred: ' + error.message;
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Create New Account</h2>
        <form id="create-account-form">
            <label for="balance">Initial Balance:</label>
            <input type="number" id="balance" name="balance" required>
            <br>
            <button type="submit">Create Account</button>
        </form>
        <br>
        <div id="message"></div>
        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
