<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="../controllers/UserController.php?action=login">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
        <br>
        <a href="../controllers/UserController.php?action=showRegister">Register</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
</body>
</html>
