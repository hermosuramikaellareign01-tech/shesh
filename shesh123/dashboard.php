<?php
session_start();

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

$total_residents = $pdo->query("
    SELECT COUNT(*)
    FROM residents
")->fetchColumn();

$senior_count = $pdo->query("
    SELECT COUNT(*)
    FROM residents
    WHERE category = 'Senior Citizen'
")->fetchColumn();

$pwd_count = $pdo->query("
    SELECT COUNT(*)
    FROM residents
    WHERE category = 'PWD'
")->fetchColumn();

$received_count = $pdo->query("
    SELECT COUNT(*)
    FROM residents
    WHERE assistance_status = 'Received'
")->fetchColumn();

$not_received_count = $pdo->query("
    SELECT COUNT(*)
    FROM residents
    WHERE assistance_status = 'Not Received'
")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<button
    type="button"
    class="menu-button"
    id="menuButton"
    aria-label="Open menu"
>
    <span></span>
    <span></span>
    <span></span>
</button>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="sidebar-logo">B</div>
            <h2>Barangay System</h2>
            <p>Senior Citizen and PWD</p>
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="profile.php">Add Resident</a>
            <a href="senior-citizen.php">Senior Citizens</a>
            <a href="pwd.php">PWD</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>Dashboard</h1>
                <p>Resident profiling overview</p>
            </div>

            <span>
                <?= htmlspecialchars($_SESSION["username"]); ?>
            </span>
        </header>

        <section class="content">
            <div class="stats">
                <div class="stat-card">
                    <p>Total Residents</p>
                    <h2><?= $total_residents; ?></h2>
                </div>

                <div class="stat-card">
                    <p>Senior Citizens</p>
                    <h2><?= $senior_count; ?></h2>
                </div>

                <div class="stat-card">
                    <p>PWD Residents</p>
                    <h2><?= $pwd_count; ?></h2>
                </div>

                <div class="stat-card received-card">
                    <p>Received Assistance</p>
                    <h2><?= $received_count; ?></h2>
                </div>

                <div class="stat-card not-received-card">
                    <p>Not Received</p>
                    <h2><?= $not_received_count; ?></h2>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Resident Management</h2>
                        <p>Add and manage resident profiles.</p>
                    </div>

                    <a href="profile.php" class="button">
                        Add Resident
                    </a>
                </div>

                <div class="quick-links">
                    <a href="senior-citizen.php">View Senior Citizens</a>
                    <a href="pwd.php">View PWD Records</a>
                    <a href="reports.php">View Reports</a>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
    const menuButton = document.getElementById("menuButton");
    const sidebar = document.getElementById("sidebar");

    menuButton.addEventListener("click", function () {
        sidebar.classList.toggle("sidebar-open");
        menuButton.classList.toggle("menu-active");
    });
</script>

</body>
</html>