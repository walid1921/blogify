<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";

// Check if user is logged in and has admin privileges
if (!isLoggedIn()) {
    redirect("login.php");
}

if (!isAdmin()) {
    redirect("app.php");
}

// Handle form submissions for editing and deleting users
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["editUser"])) {
        $userId = $_POST["userId"];
        $newUsername = $_POST["username"];
        $newEmail = $_POST["email"];

        if (editUser($pdo, $userId, $newUsername, $newEmail)) {
            redirect("admin.php");
        }
    } elseif (isset($_POST["deleteUser"])) {
        $userId = $_POST["userId"];

        if (deleteUser($pdo, $userId)) {
            redirect("admin.php");
        }
    }
}

// Fetch users for display
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


include "./components/header.php";
?>

<h1 style="text-align:center; margin-top:60px; margin-bottom:60px;">Welcome <?php echo $_SESSION["username"]; ?></h1>

<!-- Include User Table -->
<?php include './components/usersTable.php'; ?>

<?php include "./components/footer.php"; ?>
