<?php
session_start();

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true
) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

$total_residents = $pdo->query("
    SELECT COUNT(*)
    FROM residents
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

$statement = $pdo->query("
    SELECT
        full_name,
        age,
        category,
        address,
        assistance_needed,
        assistance_status,
        assistance_date
    FROM residents
    ORDER BY created_at DESC
");

$residents = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistance Reports</title>
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
            <a href="dashboard.php">Dashboard</a>
            <a href="senior-citizen.php">Senior Citizens</a>
            <a href="pwd.php">PWD</a>
            <a href="reports.php" class="active">Reports</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>Assistance Reports</h1>
                <p>Resident assistance summary.</p>
            </div>

            <span>
                <?= htmlspecialchars(
                    $_SESSION["username"],
                    ENT_QUOTES,
                    "UTF-8"
                ); ?>
            </span>
        </header>

        <section class="content">
            <div class="stats report-stats">
                <div class="stat-card">
                    <p>Total Residents</p>
                    <h2><?= (int) $total_residents; ?></h2>
                </div>

                <div class="stat-card received-card">
                    <p>Received Assistance</p>
                    <h2><?= (int) $received_count; ?></h2>
                </div>

                <div class="stat-card not-received-card">
                    <p>Not Received</p>
                    <h2><?= (int) $not_received_count; ?></h2>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Resident Assistance Report</h2>
                        <p>
                            The status is based on the assistance date.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="button no-print"
                        onclick="window.print()"
                    >
                        Print Report
                    </button>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Resident Name</th>
                                <th>Age</th>
                                <th>Category</th>
                                <th>Address</th>
                                <th>Assistance Needed</th>
                                <th>Status</th>
                                <th>Date Received</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($residents)): ?>
                                <tr>
                                    <td colspan="7">
                                        No resident records found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($residents as $resident): ?>
                                    <?php
                                    $status = $resident[
                                        "assistance_status"
                                    ] ?: "Not Received";

                                    $status_class =
                                        $status === "Received"
                                            ? "status-received"
                                            : "status-not-received";
                                    ?>

                                    <tr>
                                        <td>
                                            <?= htmlspecialchars(
                                                $resident["full_name"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ); ?>
                                        </td>

                                        <td><?= (int) $resident["age"]; ?></td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $resident["category"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $resident["address"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $resident["assistance_needed"]
                                                    ?: "None",
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ); ?>
                                        </td>

                                        <td>
                                            <span class="status <?= $status_class; ?>">
                                                <?= htmlspecialchars(
                                                    $status,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars(
                                                $resident["assistance_date"]
                                                    ?: "—",
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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