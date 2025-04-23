<?php
global $pdo;
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "includes/crudUser.php";

// Check if user is logged in and is an admin
if (!isLoggedIn()) {
    redirect("login.php");
}

if (!isAdmin()) {
    redirect("todo.php");
}

$currentUser = $_SESSION["username"];
$idUser = $_SESSION['user_id'];

// Handle form submissions for editing and deleting users
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["editUser"])) {
        $userId = $_POST["userId"];
        $newUsername = $_POST["username"];
        $newEmail = $_POST["email"];

        editUser($pdo, $userId, $newUsername, $newEmail);
        redirect("admin.php");

    } elseif (isset($_POST["deleteUser"])) {
        $userId = $_POST["userId"];
        deleteUser($pdo, $userId);
        redirect("admin.php");
    }
}


include "./components/header.php";
?>

<h1 style="text-align:center; margin-top:60px; margin-bottom:60px;">Welcome <?php echo $currentUser, $idUser; ?></h1>

<!-- Include User Table -->
<?php include './components/usersTable.php'; ?>

<?php include "./components/footer.php"; ?>
