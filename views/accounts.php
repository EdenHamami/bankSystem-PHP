<!DOCTYPE html>
<html>
<head>
    <title>Accounts</title>
</head>
<body>
    <h2>Your Accounts</h2>
    <ul id="accounts-list">
        <!-- Accounts will be dynamically inserted here -->
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../controllers/AccountController.php?action=getByUserId')
                .then(response => response.json())
                .then(data => {
                    const accountsList = document.getElementById('accounts-list');
                    if (Array.isArray(data)) {
                        data.forEach(account => {
                            const listItem = document.createElement('li');
                            listItem.textContent = `Account ID: ${account.account_id}, Balance: ${account.balance}`;
                            accountsList.appendChild(listItem);
                        });
                    } else {
                        accountsList.textContent = 'No accounts found.';
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        });
    </script>
</body>
</html>
