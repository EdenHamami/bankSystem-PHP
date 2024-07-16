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
                .then(response => {
                    return response.json().then(data => ({
                        status: response.status,
                        body: data
                    }));
                })
                .then(result => {
                    const messageDiv = document.getElementById('message');
                    if (result.body.message) {
                        messageDiv.innerHTML = result.body.message;
                        messageDiv.className = result.status === 201 ? 'success' : 'error';
                    } else {
                        messageDiv.innerHTML = 'An unexpected error occurred.';
                        messageDiv.className = 'error';
                    }
                })
                .catch(error => {
                    document.getElementById('message').innerHTML = 'An error occurred: ' + error.message;
                    document.getElementById('message').className = 'error';
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
