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

$search = "";
$errors = [];
$successMessage = "";



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

<div class="tableContainer">
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
                        <td><?php echo htmlspecialchars($user["admin"]) ? "<span class='admin'>Admin</span>" : "<span class='user'>User</span>"?></td>
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

    <div id="registerModal" class="modal">
        <div class="form-container">
            <div style="display: flex; width: 100%; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 10px;">
                <p style="font-size: 20px;  font-weight: bold;" >Register New User</p>
                <span class="close">&times;</span>
            </div>

            <?php if ($successMessage): ?>
                <p style="color: green;"><?php echo $successMessage; ?></p>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" name="username" placeholder="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : "" ?>" required>
                <span class="error"><?php echo isset($errors['username']) ? $errors['username'] : ''; ?></span>

                <input type="email" name="email" placeholder="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>" required>
                <span class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?></span>

                <input type="password" name="password" placeholder="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>" required>
                <span class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?></span>

                <input type="password" name="confPassword" placeholder="confirm password" required>
                <span class="error"><?php echo isset($errors['confPassword']) ? $errors['confPassword'] : ''; ?></span>

                <input type="number" name="age" placeholder="age" value="<?php echo isset($_POST['age']) ? $_POST['age'] : "" ?>" required>
                <span class="error"><?php echo isset($errors['age']) ? $errors['age'] : ''; ?></span>

                <input type="number" name="phone" placeholder="phone number" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : "" ?>">
                <span class="error"><?php echo isset($errors['phone']) ? $errors['phone'] : ''; ?></span>

                <div>
                    <input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'checked' : ''; ?>> Male
                    <input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>> Female
                    <input type="radio" name="gender" value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'checked' : ''; ?>> Other
                </div>
                <span class="error"><?php echo isset($errors['gender']) ? $errors['gender'] : ''; ?></span>

                <label><input type="checkbox" name="terms" value="agree" <?php echo (isset($_POST['terms']) && $_POST['terms'] === 'agree') ? 'checked' : ''; ?>> I agree to the terms and conditions</label>
                <span class="error"><?php echo isset($errors['terms']) ? $errors['terms'] : ''; ?></span>

                <input type="submit" value="Register">
            </form>
        </div>
    </div>
</div>

<script src="/assets/modal.js"></script>
