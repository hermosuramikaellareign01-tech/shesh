<?php
session_start();

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $age = (int) ($_POST["age"] ?? 0);
    $gender = trim($_POST["gender"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $contact_number = trim($_POST["contact_number"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $disability_type = trim($_POST["disability_type"] ?? "");
    $assistance_needed = trim($_POST["assistance_needed"] ?? "");
    $assistance_status = trim($_POST["assistance_status"] ?? "Not Received");
    $assistance_date = trim($_POST["assistance_date"] ?? "");

    // Validation
    if ($full_name === "" || $age <= 0 || $gender === "" || $address === "" || $category === "") {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $statement = $pdo->prepare("
                INSERT INTO residents (
                    full_name,
                    age,
                    gender,
                    address,
                    contact_number,
                    category,
                    disability_type,
                    assistance_needed,
                    assistance_status,
                    assistance_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $statement->execute([
                $full_name,
                $age,
                $gender,
                $address,
                $contact_number,
                $category,
                $disability_type,
                $assistance_needed,
                $assistance_status,
                $assistance_date ?: null
            ]);

            $success = "Resident added successfully!";
            
            // Clear form
            $_POST = [];
        } catch (PDOException $e) {
            $error = "Error adding resident: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resident</title>
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
            <a href="profile.php" class="active">Add Resident</a>
            <a href="senior-citizen.php">Senior Citizens</a>
            <a href="pwd.php">PWD</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <h1>Add Resident</h1>
                <p>Register a new resident profile</p>
            </div>

            <span>
                <?= htmlspecialchars($_SESSION["username"]); ?>
            </span>
        </header>

        <section class="form-page">
            <div class="form-container">

                <?php if ($success !== ""): ?>
                    <div class="success-message">
                        ✓ <?= htmlspecialchars($success, ENT_QUOTES, "UTF-8"); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error !== ""): ?>
                    <div class="error-message">
                        ✗ <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="profile.php">
                    <h2>Personal Information</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?= htmlspecialchars($_POST["full_name"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                                required
                                autofocus
                            >
                        </div>

                        <div class="form-group">
                            <label for="age">Age *</label>
                            <input
                                type="number"
                                id="age"
                                name="age"
                                value="<?= htmlspecialchars($_POST["age"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                                min="1"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" name="gender" required>
                                <option value="">-- Select Gender --</option>
                                <option value="Male" <?= ($_POST["gender"] ?? "") === "Male" ? "selected" : ""; ?>>Male</option>
                                <option value="Female" <?= ($_POST["gender"] ?? "") === "Female" ? "selected" : ""; ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full">
                        <label for="address">Address *</label>
                        <textarea
                            id="address"
                            name="address"
                            required
                        ><?= htmlspecialchars($_POST["address"] ?? "", ENT_QUOTES, "UTF-8"); ?></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input
                                type="tel"
                                id="contact_number"
                                name="contact_number"
                                value="<?= htmlspecialchars($_POST["contact_number"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                                placeholder="09XXXXXXXXX"
                            >
                        </div>

                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="Senior Citizen" <?= ($_POST["category"] ?? "") === "Senior Citizen" ? "selected" : ""; ?>>Senior Citizen</option>
                                <option value="PWD" <?= ($_POST["category"] ?? "") === "PWD" ? "selected" : ""; ?>>PWD (Person with Disability)</option>
                            </select>
                        </div>
                    </div>

                    <h2>Additional Information</h2>

                    <div class="form-group full">
                        <label for="disability_type">Disability Type (PWD only)</label>
                        <input
                            type="text"
                            id="disability_type"
                            name="disability_type"
                            value="<?= htmlspecialchars($_POST["disability_type"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                            placeholder="e.g., Hearing Impaired, Visually Impaired, etc."
                        >
                        <span class="form-help">Only fill if category is PWD</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="assistance_needed">Assistance Needed</label>
                            <input
                                type="text"
                                id="assistance_needed"
                                name="assistance_needed"
                                value="<?= htmlspecialchars($_POST["assistance_needed"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                                placeholder="e.g., Medicine, Food, Medical Aid"
                            >
                        </div>

                        <div class="form-group">
                            <label for="assistance_status">Assistance Status</label>
                            <select id="assistance_status" name="assistance_status">
                                <option value="Not Received" <?= ($_POST["assistance_status"] ?? "Not Received") === "Not Received" ? "selected" : ""; ?>>Not Received</option>
                                <option value="Received" <?= ($_POST["assistance_status"] ?? "") === "Received" ? "selected" : ""; ?>>Received</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group full">
                        <label for="assistance_date">Date Assistance Received</label>
                        <input
                            type="date"
                            id="assistance_date"
                            name="assistance_date"
                            value="<?= htmlspecialchars($_POST["assistance_date"] ?? "", ENT_QUOTES, "UTF-8"); ?>"
                        >
                        <span class="form-help">Only fill if assistance status is "Received"</span>
                    </div>

                    <div class="form-actions">
                        <a href="dashboard.php" class="cancel-button">Cancel</a>
                        <button type="reset" class="reset-button">Clear Form</button>
                        <button type="submit" class="button">Add Resident</button>
                    </div>
                </form>

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

    // Show/hide disability_type based on category selection
    const categorySelect = document.getElementById("category");
    const disabilityInput = document.getElementById("disability_type");

    categorySelect.addEventListener("change", function () {
        if (this.value === "PWD") {
            disabilityInput.parentElement.style.display = "block";
        } else {
            disabilityInput.parentElement.style.display = "none";
            disabilityInput.value = "";
        }
    });

    // Trigger on page load
    categorySelect.dispatchEvent(new Event("change"));
</script>

</body>
</html>
