<?php
global $pdo;
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "includes/crudUser.php";

if (!isLoggedIn()) {
    redirect("login.php");
}


$errors = [];
$successMessage = "";
$currentUser = $_SESSION["username"];
$currentUserId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, username, email, age, phone, gender FROM users WHERE id = :id");
$stmt->execute(['id' => $currentUserId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["editProfileUser"])) {
        $newUsername = isset($_POST["username"]) ? trim($_POST["username"]) : '';
        $newEmail = isset($_POST["email"]) ? trim($_POST["email"]) : '';

        // Add validation
        if (empty($newUsername) || !preg_match("/^[a-zA-Z0-9_]{5,20}$/", $newUsername)) {
            $errors['username'] = "Username must be 5-20 chars (letters, numbers, underscore)";
        }


        if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        }

        // Only proceed if no errors
        if (empty($errors)) {
            if (editUser($pdo, $currentUserId, $newUsername, $newEmail)) {
                // Success - log out to apply changes
                redirect("logout.php");
            } else {
                $errors['database'] = "Failed to update user information";
            }
        }

    }
    elseif (isset($_POST["deleteProfileUser"])) {
        deleteUser($pdo, $currentUserId);
        redirect("logout.php");

    }
    elseif (isset($_POST["passwordProfileUser"])) {
        $password = isset($_POST["password"]) ? $_POST["password"] : '';
        $confPassword = isset($_POST["confPassword"]) ? $_POST["confPassword"] : '';

        if (
            empty($password) || strlen($password) < 8 ||
            !preg_match("/[A-Z]/", $password) ||
            !preg_match("/[a-z]/", $password) ||
            !preg_match("/[0-9]/", $password)
        ) {
            $errors['password'] = "Password must be at least 8 characters, include 1 uppercase, 1 lowercase, and 1 number.";
        }

        if ($password !== $confPassword) {
            $errors['confPassword'] = "Passwords do not match";
        }

        if (empty($errors)) {
            if (updatePassword($pdo, $currentUserId, $password, $confPassword)) {
                // Success - log out to apply changes
                redirect("logout.php");
            } else {
                $errors['database'] = "Failed to update user information";
            }
        }
    }

}


?>

<div class="profilePage">
    <div>
        <h2>Account</h2>
        <p class="account-info"><span>Hi, <?php echo $currentUser ?>!</span> Update your account information here. Once you update your credentials you will be logged out.</p>




        <div class="user-header">
            <div class="update-user">
                <div class="form-container">
                    <?php if ($successMessage): ?>
                        <p style="color: green;"><?php echo $successMessage; ?></p>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="text" name="username" placeholder="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        <?php if (!empty($errors['username'])): ?><span class="error"><?php echo $errors['username'] ?></span><?php endif; ?><br>

                        <input type="email" name="email" placeholder="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        <?php if (!empty($errors['email'])): ?><span class="error"><?php echo $errors['email'] ?></span><?php endif; ?><br>

                        <button class="primary-button" type="submit" name="editProfileUser">Save changes</button>
                    </form>
                </div>
            </div>

            <div class="right-column">
                <div class="password-user">
                    <div class="form-container">
                        <?php if ($successMessage): ?>
                            <p style="color: green;"><?php echo $successMessage; ?></p>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="password" name="password" placeholder="New password"
                                   value="<?php echo htmlspecialchars(isset($_POST['password']) ? $_POST['password'] : ''); ?>" required>
                            <?php if (!empty($errors['password'])): ?>
                                <span class="error"><?php echo $errors['password']; ?></span>
                            <?php endif; ?><br>

                            <input type="password" name="confPassword" placeholder="Confirm password" required>
                            <?php if (!empty($errors['confPassword'])): ?>
                                <span class="error"><?php echo $errors['confPassword']; ?></span>
                            <?php endif; ?><br>

                            <button class="primary-button" type="submit" name="passwordProfileUser">Confirm</button>
                        </form>
                    </div>

                </div>
            </div>

            <div class="user-info">
                <div class="user-info-content">
                    <div>
                        <span>Username: <?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                    <div>
                        <span>Email: <?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div>
                        <span>Age: <?php echo htmlspecialchars($user['age']); ?></span>
                    </div>
                    <div>
                        <span>Phone: <?php echo htmlspecialchars($user['phone']); ?></span>
                    </div>
                    <div>
                        <span>Gender: <?php echo htmlspecialchars($user['gender']); ?></span>
                    </div>
                </div>
                <br>
                <button class="delete-button" type="submit" id="deleteUserBtn">Delete account <img src="/assets/images/bin.png" alt="bin image"></button>

            </div>
        </div>
    </div>

<!--  show delete modal -->
    <div id="deleteModal" class="deleteUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Delete Account</h4>
                <span class="close">&times;</span>
            </div>
            <p>Are you sure you want to delete your account? This action is not reversible.</p>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <button class="delete-button" type="submit" name="deleteProfileUser">Yes, delete my account</button>
            </form>
        </div>
    </div>
</div>
