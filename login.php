<?php
require_once "session.php";
require_once "./db/database.php";
require_once "utils/helpers.php";

// If user is already logged in, redirect
if (isLoggedIn()) {
    redirect('app.php');
}

$error = "";

function getUserByUsername($pdo, $username) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // returns one user or false
}

function handleLoginSession($user, $isAdmin) {
    session_regenerate_id(true); // Prevent session hijacking
    $_SESSION["logged_in"] = true; // This is how we know the user is logged in
    $_SESSION["username"] = $user["username"]; // this to store the name of the user to be used across pages
    $_SESSION["admin"] = $isAdmin; // This is how we know the user is an adm
    redirect($isAdmin ? 'admin.php' : 'app.php');
}

function validateLoginInput($username, $password) {
    if (empty($username) || empty($password)) {
        return "Username and Password are required.";
    }
    return "";
}

function processLogin($pdo, $username, $password) {
    $user = getUserByUsername($pdo, $username);
    if ($user && password_verify($password, $user["password"])) {
        handleLoginSession($user, $user["admin"] === 1);
    } else {
        return "Your login credentials are incorrect. Please try again.";
        // here one of the security precautions to not let the hacker knows that the user already exists in the DB or no
    }
    return "";
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $error = validateLoginInput($username, $password);
    if (!$error) {
        $error = processLogin($pdo, $username, $password);
    }
}


include "./components/header.php";
?>

<div class="hero <?php echo pageClass() ?>">
    <div class="form-container">
        <form method="POST" action="">

            <span class="error"><?php echo $error; ?></span><br><br>

            <h2>Login</h2>

            <input value="<?php echo isset($username) ? htmlspecialchars($username) : "" ?>" type="text" name="username" placeholder="username" required><br><br>
            <input type="password" name="password" placeholder="password" required><br><br>

            <input type="submit" value="Login">
        </form>
    </div>
</div>

<?php include "./components/footer.php"; ?>
