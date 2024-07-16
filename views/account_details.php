<?php
session_start();

// בדיקת אם המשתמש מחובר
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$account_id = $_GET['account_id'] ?? null;

if (!$account_id) {
    header('Location: accounts.php?error=Account ID is required.');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Details</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <div class="account-info">
            <h2>Account Details</h2>
            <p id="account-id">Account ID: <?php echo htmlspecialchars($account_id); ?></p>
            <p id="account-balance">Balance: </p>
        </div>
        <div class="transactions">
            <h2>Transactions</h2>
            <ul id="transactions-list">
                <!-- Transactions will be dynamically inserted here -->
            </ul>
        </div>
        <a href="accounts.php" class="back-link">Back to Accounts</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const accountId = <?php echo json_encode($account_id); ?>;
            fetch(`../controllers/AccountController.php?action=getById&account_id=${accountId}`)
                .then(response => response.json())
                .then(account => {
                    document.getElementById('account-balance').textContent = `Balance: ${account.balance}`;
                })
                .catch(error => console.error('Error fetching account details:', error));

            fetch(`../controllers/AccountController.php?action=getTransactions&account_id=${accountId}`)
                .then(response => response.json())
                .then(transactions => {
                    const transactionsList = document.getElementById('transactions-list');
                    if (Array.isArray(transactions) && transactions.length > 0) {
                        transactions.forEach(transaction => {
                            const listItem = document.createElement('li');
                            listItem.textContent = `Date: ${transaction.date}, Amount: ${transaction.amount}, Type: ${transaction.type}, Direction: ${transaction.direction}`;
                            transactionsList.appendChild(listItem);
                        });
                    } else {
                        transactionsList.textContent = 'No transactions found.';
                    }
                })
                .catch(error => {
                    console.error('Error fetching transactions:', error);
                    document.getElementById('transactions-list').textContent = 'Failed to load transactions.';
                });
        });
    </script>
</body>
</html>
