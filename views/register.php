<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../public/styles.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="../controllers/UserController.php?action=register">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Register</button>
        </form>
        <br>
        <a href="../controllers/UserController.php?action=showLogin">Login</a>
    </div>

    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
</body>
</html>
