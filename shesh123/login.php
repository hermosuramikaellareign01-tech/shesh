<?php
session_start();

if (
    isset($_SESSION["logged_in"]) &&
    $_SESSION["logged_in"] === true
) {
    header("Location: dashboard.php");
    exit;
}

require_once "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        $statement = $pdo->prepare("
            SELECT id, username, password
            FROM users
            WHERE username = ?
        ");

        $statement->execute([$username]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["logged_in"] = true;
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];

            header("Location: dashboard.php");
            exit;
        }

        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-page">
    <div class="login-box">

        <div class="login-logo">B</div>

        <div class="login-header">
            <h1>Barangay System</h1>
            <p>Senior Citizen and PWD Management</p>
        </div>

        <?php if ($error !== ""): ?>
            <div class="error-message">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <button type="submit" class="button full-width">
                Login
            </button>
        </form>

    </div>
</div>

</body>
</html>