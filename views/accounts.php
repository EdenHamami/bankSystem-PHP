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
    <title>Accounts</title>
    <link rel="stylesheet" type="text/css" href="../public/accounts.css">
</head>
<body>
    <div class="container">
        <h2>Your Accounts</h2>
        <ul id="accounts-list">
            <!-- Accounts will be dynamically inserted here -->
        </ul>
        <?php if (isset($_GET['error'])): ?>
            <div class="error">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../controllers/AccountController.php?action=getByUserId')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    const accountsList = document.getElementById('accounts-list');
                    if (Array.isArray(data)) {
                        data.forEach(account => {
                            const listItem = document.createElement('li');
                            const accountLink = document.createElement('a');
                            accountLink.href = `account_details.php?account_id=${account.account_id}`;
                            accountLink.textContent = `Account ID: ${account.account_id}, Balance: ${account.balance}`;
                            listItem.appendChild(accountLink);
                            accountsList.appendChild(listItem);
                        });
                    } else {
                        accountsList.textContent = 'No accounts found.';
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                    document.getElementById('accounts-list').textContent = 'Failed to load accounts.';
                });
        });
    </script>
</body>
</html>
