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

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $delete_id = (int) $_POST["delete_id"];

    $statement = $pdo->prepare("
        DELETE FROM residents
        WHERE id = ? AND category = 'PWD'
    ");

    $statement->execute([$delete_id]);

    header("Location: pwd.php");
    exit;
}

$statement = $pdo->prepare("
    SELECT
        id,
        full_name,
        age,
        gender,
        address,
        disability_type,
        assistance_needed,
        assistance_status,
        assistance_date
    FROM residents
    WHERE category = 'PWD'
    ORDER BY created_at DESC
");

$statement->execute();
$residents = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWD Records</title>
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
            <a href="pwd.php" class="active">PWD</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>PWD Records</h1>
                <p>Registered PWD residents.</p>
            </div>

            <span>
                <?= htmlspecialchars(
                    $_SESSION["username"],
                    ENT_QUOTES,
                    "UTF-8"
                ); ?>
            </span>
        </header>

        <section class="page-container">
            <div class="page-header">
                <div>
                    <h1>PWD Residents</h1>
                    <p>All saved PWD records.</p>
                </div>
            </div>

            <div class="panel table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Address</th>
                            <th>Disability</th>
                            <th>Assistance</th>
                            <th>Status</th>
                            <th>Date Received</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($residents)): ?>
                            <tr>
                                <td colspan="9">
                                    No PWD records yet.
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
                                            $resident["gender"],
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
                                            $resident["disability_type"]
                                                ?: "—",
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

                                    <td>
                                        <form
                                            method="POST"
                                            onsubmit="return confirm('Delete this resident?');"
                                            style="display:inline;"
                                        >
                                            <input
                                                type="hidden"
                                                name="delete_id"
                                                value="<?= (int) $resident['id']; ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="delete-button"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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