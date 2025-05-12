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

    if (isset($_POST["editProfileUser"])) {

        $_POST["userId"] = $currentUserId; // Inject the current user ID if not passed from form

        editUser($pdo, $_POST, $errors, $successMessage);

        if (empty($errors)) {
            $successMessage = "User information updated successfully.";
            $_SESSION["username"] = trim($_POST["username"]);
//            redirect("admin.php");
        } else {
            $successMessage = "Failed to update user information.";
        }
    }

    elseif (isset($_POST["deleteProfileUser"])) {
        deleteUser($pdo, $currentUserId);
        redirect("logout.php");
    }

    elseif (isset($_POST["passwordProfileUser"])) {
        updatePassword($pdo, $_POST, $currentUserId, $errors, $successMessage);

        if (empty($errors)) {
            $successMessage = "Password updated successfully. Please log in again.";

        } else {
            $errors['database'] = "Failed to update user information";
        }
    }
}

$stmt = $pdo->prepare("SELECT id, username, email, age, phone, gender FROM users WHERE id = :id");
$stmt->execute(['id' => $currentUserId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


?>

<div class="profilePage">

    <?php if ($successMessage): ?>
        <div class="toast"><?php echo $successMessage; ?></div>
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
                <button class="delete-button" id="deleteUserBtn">Delete account <img src="/assets/images/bin.png" alt="bin image"></button>
            </div>
            <div id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
                <div class="user-info-content">
                    <div>
                        <h6 id="deleteModalTitle">Are you sure you want to delete your account <span>permanently</span>?</h6>

                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <button class="delete-button" type="submit" name="deleteProfileUser">Confirm</button>
                            <button class="closeDeleteModal" type="button">Cancel</button>
                        </form>
                    </div>
                </div>
                <br>
            </div>

        </div>
    </div>

<!--  show delete modal-->
<!--    <div id="deleteModal" class="deleteUserModal">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <h4>Delete Account</h4>-->
<!--                <span class="close">&times;</span>-->
<!--            </div>-->
<!--            <p>Are you sure you want to delete your account? This action is not reversible.</p>-->
<!--            <form method="POST" action="--><?php //echo htmlspecialchars($_SERVER["PHP_SELF"]); ?><!--">-->
<!--                <button class="delete-button" type="submit" name="deleteProfileUser">Yes, delete my account</button>-->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
</div>


<script src="../assets/js/modal.js"></script>
