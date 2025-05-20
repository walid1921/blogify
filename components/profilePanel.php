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




if ($_SERVER["REQUEST_METHOD"] === "POST") {

    //! Update My Profile
    if (isset($_POST["editProfileUser"])) {
        $_POST["userId"] = $currentUserId; // Inject the current user ID if not passed from form
        editUser($pdo, $_POST, $errors);
        redirect("admin.php");
    }

    //! Delete My Account
    elseif (isset($_POST["deleteProfileUser"])) {
        $password = isset($_POST["password"]) ? $_POST["password"] : "";

        if (deleteUser($pdo, $currentUserId, $password)) {
            redirect("logout.php"); // Clears session after successful deletion
        } else {
            redirect("admin.php"); // Show message and error type via session
        }
    }

    //! Update My password
    elseif (isset($_POST["passwordProfileUser"])) {
        updatePassword($pdo, $_POST, $currentUserId, $errors);
    }
}

$stmt = $pdo->prepare("SELECT id, username, email, age, phone, gender FROM users WHERE id = :id");
$stmt->execute(['id' => $currentUserId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<div class="profilePage">

    <?php if(isset($_SESSION["message"])): ?>
        <div class="notification-container">
            <div class="notification <?php echo $_SESSION["msg_type"]?>">
                <!-- Success message will go here -->
                <?php echo  $_SESSION["message"];?>
                <?php unset($_SESSION["message"]);?>
            </div>
        </div>
    <?php endif; ?>

    <div>
        <h2>Account</h2>
        <p class="account-info"><span>Hi, <?php echo $currentUser ?>!</span> Update your account information here.</p>

        <div class="user-header">
            <div class="update-user">
                <div class="form-container">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="text" name="username" placeholder="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        <?php if (!empty($errors['username'])): ?><span class="error"><?php echo $errors['username'] ?></span><?php endif; ?><br>

                        <input type="email" name="email" placeholder="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        <?php if (!empty($errors['email'])): ?><span class="error"><?php echo $errors['email'] ?></span><?php endif; ?><br>

                        <button class="primary-button" type="submit" name="editProfileUser">Save changes</button>
                    </form>
                </div>
            </div>

            <div id="passwordForm">
                <div class="password-user">
                    <div class="form-container">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="password" name="password" placeholder="New password" required>
                            <?php if (!empty($errors['password'])): ?>
                                <span class="error"><?php echo $errors['password']; ?></span>
                            <?php endif; ?><br>

                            <input type="password" name="confPassword" placeholder="Confirm password" required>
                            <?php if (!empty($errors['confPassword'])): ?>
                                <span class="error"><?php echo $errors['confPassword']; ?></span>
                            <?php endif; ?><br>

                            <!-- This is a button that *reveals* the confirm step -->
                            <button class="primary-button" type="button" id="passwordBtn">Save</button>

                            <!-- This is the real submit button that gets revealed -->
                            <div class="confirmationStep">
                                <button class="delete-button" id="passwordConfirm" style="display: none;" type="submit" name="passwordProfileUser">Confirm</button>
                                <button id="cancelConfirm" class="cancelConfirm" style="display: none;" type="button">Cancel</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <!-- User info + Delete btn -->
            <div id="userInfo">
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
                <button class="delete-button" id="deleteUserBtn">Delete account <i class="fa-solid fa-trash"></i></button>
            </div>

            <!-- Delete Confirmation Step 1 -->
            <div id="deleteModal">
                <div class="user-info-content">
                    <h6 id="deleteModalTitle">Are you sure you want to delete your account <span>permanently</span>?</h6>

                    <div class="user-info-buttons">
                        <button class="delete-button" id="confirmDeleteBtn">Yes, continue</button>
                        <button class="cancelConfirmDelete cancelConfirm">Cancel</button>
                    </div>

                </div>
            </div>

            <!-- Delete Confirmation Step 2 (Password) -->
            <div id="confirmDeleteModal" class="modal deleteUserModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Confirm Deletion</h4>
                        <span class="closePasswordModal close">&times;</span>
                    </div>
                    <p>to confirm deletion of your account, please enter your current password</p>
                    <form class="confPasswordForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="password" name="password" placeholder="Enter current password" required>
                        <?php if (!empty($errors['deletePassword'])): ?>
                            <span class="error"><?php echo $errors['deletePassword']; ?></span>
                        <?php endif; ?>
                        <button class="delete-button" type="submit" name="deleteProfileUser">Confirm Deletion</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>


<script src="../assets/js/modal.js"></script>
