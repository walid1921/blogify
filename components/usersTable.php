<?php
    global $pdo;
    require_once "includes/database.php";
    require_once "includes/helpers.php";
    require_once "includes/crudUser.php";

    if (!isLoggedIn()) {
        redirect("login.php");
    }

    if (!isAdmin()) {
        redirect("todo.php");
    }

    $currentUser = $_SESSION["user"];
    $currentUserId = $_SESSION['user_id'];

    $search = "";
    $errors = [];
    $successMessage = "";


    //$currentUser = $_SESSION["username"];
    //
    //$stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = :username");
    //$stmt->execute(['username' => $currentUser]);
    //$users = $stmt->fetch(PDO::FETCH_ASSOC);
    //
    //
    //if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //    if (isset($_POST["editAdmin"])) {
    //        $userId = $_SESSION['user_id'];
    //        $newUsername = $_POST["username"];
    //        $newEmail = $_POST["email"];
    //        $newPassword = $_POST["password"];
    //        $confPassword = $_POST["confPassword"];
    //
    //        editUser($pdo, $userId, $newUsername, $newEmail, $newPassword, $confPassword);
    //        redirect("logout.php");
    //    } elseif (isset($_POST["deleteUser"])) {
    //        $userId = $_SESSION['user_id'];
    //        deleteUser($pdo, $userId);
    //        redirect("logout.php");
    //    }
    //}



    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $search = isset($_GET['search']) ? trim($_GET['search']) : "";

        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE :search OR email LIKE :search");
            $stmt->execute(['search' => "%$search%"]);
        } else {
            $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC "); // we used query instead of prepare because we don't need to bind any parameters
        }

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        registerUser($pdo, $_POST, $errors, $successMessage);
    }

?>



<div class="adminPage">

    <div>
       <h2>Admin Panel</h2>

        <div class="admin-header">
            <div class="update-admin">
                <div>
                    <h5>Account</h5>
                    <p>Update your account information here. Once you update your credentials you will be logged out.</p>
                </div>

                <div class="form-container">
                    <?php if ($successMessage): ?>
                        <p style="color: green;"><?php echo $successMessage; ?></p>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="text" name="username" placeholder="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : "" ?>" required>
                        <?php if ($errors['username']): ?><span class="error"><?php echo $errors['username'] ?></span><?php endif; ?><br>

                        <input type="email" name="email" placeholder="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>" required>
                        <?php if ($errors['email']): ?><span class="error"><?php echo $errors['email'] ?></span><?php endif; ?><br>

                        <button class="primary-button" type="submit" name="editAdmin">Save</button>
                    </form>
                </div>

                <button class="delete-button" type="submit" name="deleteAdmin" id="deleteAdminBtn">Delete my account <img src="/assets/images/bin.png" alt="bin image"></button>


            </div>

            <div class="right-column">
                <div class="password-admin">
                    <div>
                        <h5>Password</h5>
                        <p>Update your password information here. Once you update your credentials you will be logged out.</p>
                    </div>
                    <div class="form-container">
                        <?php if ($successMessage): ?>
                            <p style="color: green;"><?php echo $successMessage; ?></p>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                            <input type="password" name="password" placeholder="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>" required>
                            <?php if ($errors['password']): ?><span class="error"><?php echo $errors['password'] ?></span><?php endif; ?><br>

                            <input type="password" name="password" placeholder="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>" required>
                            <?php if ($errors['password']): ?><span class="error"><?php echo $errors['password'] ?></span><?php endif; ?><br>

                            <input type="password" name="confPassword" placeholder="confirm password" required>
                            <?php if ($errors['confPassword']): ?><span class="error"><?php echo $errors['confPassword'] ?></span><?php endif; ?><br>

                            <button class="primary-button" type="submit" name="passwordAdmin">Save</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="tableContainer">
        <h2>Other users</h2>
        <div class="table-header">
            <h3><span><?php echo !empty($users) ? count($users) : 0 ?> active users</span></h3>

            <div class="search">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Search by username or email" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0,0,256,256">
                            <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-size="none" style="mix-blend-mode: normal"><g transform="scale(8,8)"><path d="M19,3c-5.51172,0 -10,4.48828 -10,10c0,2.39453 0.83984,4.58984 2.25,6.3125l-7.96875,7.96875l1.4375,1.4375l7.96875,-7.96875c1.72266,1.41016 3.91797,2.25 6.3125,2.25c5.51172,0 10,-4.48828 10,-10c0,-5.51172 -4.48828,-10 -10,-10zM19,5c4.42969,0 8,3.57031 8,8c0,4.42969 -3.57031,8 -8,8c-4.42969,0 -8,-3.57031 -8,-8c0,-4.42969 3.57031,-8 8,-8z"></path></g></g>
                        </svg>
                    </button>
                </form>

                <button id="addUserBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 448 512">
                        <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none"  font-size="none" style="mix-blend-mode: normal">
                        <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <table class="user-table">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Registration Date</th>
                    <th>Update User</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($users) && count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <?php
                                if ($user["id"] == $currentUserId) {
                                    echo "<span class='admin'>you</span>";
                                } elseif ($user["admin"]) {
                                    echo "<span class='admin'>admin</span>";
                                } else {
                                    echo "<span class='user'>user</span>";
                                }
                                ?>
                            </td>

                            <td><?php echo htmlspecialchars($user["username"]); ?></td>
                            <td><?php echo htmlspecialchars($user["email"]); ?></td>
                            <td><?php echo htmlspecialchars($user["age"]); ?></td>
                            <td><?php echo htmlspecialchars($user["phone"]); ?></td>
                            <td><?php echo htmlspecialchars($user["gender"]); ?></td>
                            <td><?php echo htmlspecialchars($user["created_at"]); ?></td>
                            <td>
                                <!-- Edit User  -->
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user["id"]); ?>">
                                    <input type="text" name="username" value="<?php echo htmlspecialchars($user["username"]); ?>" required>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
                                    <button class="edit" type="submit" name="editUser">Edit</button>
                                </form>

                                <!-- Delete User  -->
                                <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user["id"]); ?>">
                                    <button class="delete" type="submit" name="deleteUser">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="8">No users found.</td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>

</div>

</div>

<script src="../assets/js/modal.js"></script>
