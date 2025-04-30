<?php
global $pdo;
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";

// If user is already logged in, redirect
if (isLoggedIn()) {
    redirect('todo.php');
}

$username = $password = "";
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
    $_SESSION["admin"] = $isAdmin; // This is how we know the user is an admin
    $_SESSION['user_id'] = $user['id']; // Store user ID in session
    redirect($isAdmin ? 'admin.php' : 'todo.php');
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

<div>
    <canvas id="gradient-canvas"></canvas>


    <div class="hero <?php echo pageClass() ?>">
        <div class="form-container">
            <h2>Login to your account</h2>
            <?php if ($error): ?>
                <span class="error"><?php echo $error; ?></span>
            <?php endif; ?>

            <form method="POST" action="">

                <input value="<?php echo isset($username) ? htmlspecialchars($username) : "" ?>" type="text" name="username" placeholder="username" required><br>
                <input type="password" name="password" value="<?php echo isset($password) ? $password : "" ?>" placeholder="password" required><br>

                <p style=font-size:14px>Don't have an account? <a href="register.php" >Register</a></p>

                <input type="submit" value="Login">
            </form>
        </div>
    </div>


</div>

<script src="/assets/js/gradient.js"></script>


<?php include "./components/footer.php"; ?>
