<?php
    global $pdo;
    require_once "includes/database.php";
    require_once "includes/helpers.php";
    require_once "includes/crudUser.php";

    if (!isLoggedIn()) {
        redirect("login.php");
    }

    $search = "";
    $errors = [];
    $successMessage = "";
    $currentUser = $_SESSION["username"];
    $currentUserId = $_SESSION['user_id'];


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

        if (isset($_POST["registerNewUser"])) {
            registerUser($pdo, $_POST, $errors, $successMessage, false, true);

            if (empty($errors)) {
                $successMessage = "User registered successfully.";
            } else {
                $successMessage = "Registration failed.";
            }

        }

        elseif (isset($_POST["editUser"])) {

            editUser($pdo, $_POST, $errors, $successMessage);

            if (empty($errors)) {
                $successMessage = "User information updated successfully.";
            } else {
                $successMessage = "Failed to update user information.";
            }

        }

        elseif (isset($_POST["deleteUser"])) {
            $userId = isset($_POST["userId"]) ? (int)$_POST["userId"] : 0;
            deleteUser($pdo, $userId);
            $successMessage = "User deleted successfully.";

        }

        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

?>


<?php if(isAdmin()): ?>

    <div class="userTable">

        <?php if ($successMessage): ?>
            <div class="toast"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <h2>Users</h2>


        <div class="tableContainer">
            <div class="table-header">
                <p><span><?php echo !empty($users) ? count($users) : 0 ?> active users</span></p>

                <div class="search">
                    <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
                                        if ($user["id"] === $currentUserId) {
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

                                <?php if ($user["admin"]): ?>
                                    <td>
                                       Restricted!
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <button
                                                class="editUserBtn edit"
                                                type="button"
                                                data-user-id="<?php echo $user['id']; ?>"
                                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                        >Edit</button>


                                        <!-- Delete User  -->
                                        <form
                                                method="POST"
                                                style="display:inline-block;"
                                                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                                                onsubmit="return confirm('Are you sure you want to delete this user?');"
                                        >
                                            <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user["id"]); ?>">
                                            <button class="delete" type="submit" name="deleteUser">Delete</button>
                                        </form>
                                    </td>
                                <?php endif; ?>

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





<!--   Register Modal -->
    <div id="registerModal" class="modal">
        <div class="form-container">
            <div class="modal-header">
                <h4>Register New User</h4>
                <span class="close">&times;</span>
            </div>

            <form
                    method="POST"
                    action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
            >

                <!-- username-->
                <input
                        type="text"
                        name="username"
                        placeholder="username"
                        value="<?php echo isset($_POST['username']) ? $_POST['username'] : "" ?>"
                        required
                >
                <?php if ($errors['username']): ?>
                    <span class="error">
                        <?php echo $errors['username'] ?>
                    </span>
                <?php endif; ?>
                <br>

                <!-- email-->
                <input
                        type="email"
                        name="email"
                        placeholder="email"
                        value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>"
                        required
                >
                <?php if ($errors['email']): ?>
                    <span class="error">
                        <?php echo $errors['email'] ?>
                    </span>
                <?php endif; ?>
                <br>

                <!-- password-->
                <input
                        type="password"
                        name="password"
                        placeholder="password"
                        value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>"
                        required>
                <?php if ($errors['password']): ?>
                    <span class="error">
                        <?php echo $errors['password'] ?>
                    </span>
                <?php endif; ?>
                <br>


                <input type="password" name="confPassword" placeholder="confirm password" required>
                <?php if ($errors['confPassword']): ?><span class="error"><?php echo $errors['confPassword'] ?></span><?php endif; ?>
                <br>


                <input type="number" name="age" placeholder="age" value="<?php echo isset($_POST['age']) ? $_POST['age'] : "" ?>" required>
                <?php if ($errors['age']): ?><span class="error"><?php echo $errors['age'] ?></span><?php endif; ?>
                <br>


                <input type="text" name="phone" placeholder="phone number" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : "" ?>">
                <?php if ($errors['phone']): ?><span class="error"><?php echo $errors['phone'] ?></span><?php endif; ?>
                <br>


                <select name="admin">
                    <option value="0" <?php echo isset($_POST['admin']) && $_POST['admin'] === "0" ? "selected" : ""?>>User</option>
                    <option value="1" <?php echo isset($_POST['admin']) && $_POST['admin'] === "1" ? "selected" : ""?>>Admin</option>
                </select>
                <?php if ($errors['admin']): ?><span class="error"><?php echo $errors['admin'] ?></span><?php endif; ?>
                <br>


                <div>
                    <input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'checked' : ''; ?>> Male
                    <input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>> Female
                    <input type="radio" name="gender" value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'checked' : ''; ?>> Other
                </div>
                <?php if ($errors['gender']): ?><span class="error"><?php echo $errors['gender'] ?></span><?php endif; ?>
                <br>

                <label><input type="checkbox" name="terms" value="agree" <?php echo (isset($_POST['terms']) && $_POST['terms'] === 'agree') ? 'checked' : ''; ?>> I agree to the terms and conditions</label>
                <?php if ($errors['terms']): ?><span class="error"><?php echo  $errors['terms']  ?></span><?php endif; ?>
                <br>

                <input type="submit" value="Register" name="registerNewUser">
            </form>
        </div>
    </div>

<!--   Edit Modal -->
    <div id="editUserModal" class="editModal">
        <div class="form-container">
            <div class="modal-header">
                <h4>Update username</h4>
                <span class="closeEditUserModal">&times;</span>

            </div>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="userId" id="editUserId">

                <input type="text" name="username" id="editUsername" placeholder="Username" required>
                <?php if (!empty($errors['username'])): ?>
                    <span class="error"><?php echo $errors['username']; ?></span>
                <?php endif; ?>
                <br>

                <input type="email" name="email" id="editEmail" placeholder="Email" required>
                <?php if (!empty($errors['email'])): ?>
                    <span class="error"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
                <br>

                <input type="submit" value="Save changes" name="editUser">
            </form>
        </div>
    </div>


</div>
<?php endif;?>

<script src="../assets/js/modal.js"></script>
