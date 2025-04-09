<?php
require_once "includes/database.php";
require_once "includes/helpers.php";

if (!isLoggedIn()) {
    redirect("login.php");
}

if (!isAdmin()) {
    redirect("todo.php");
}

$search = "";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $search = isset($_GET['search']) ? trim($_GET['search']) : "";

    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE :search OR email LIKE :search");
        $stmt->execute(['search' => "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM users");
    }

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="tableContainer">
    <div class="table-header">
        <h3><span><?php echo count($users); ?> active users</span></h3>

        <div class="search">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by username or email" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0,0,256,256">
                        <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><g transform="scale(8,8)"><path d="M19,3c-5.51172,0 -10,4.48828 -10,10c0,2.39453 0.83984,4.58984 2.25,6.3125l-7.96875,7.96875l1.4375,1.4375l7.96875,-7.96875c1.72266,1.41016 3.91797,2.25 6.3125,2.25c5.51172,0 10,-4.48828 10,-10c0,-5.51172 -4.48828,-10 -10,-10zM19,5c4.42969,0 8,3.57031 8,8c0,4.42969 -3.57031,8 -8,8c-4.42969,0 -8,-3.57031 -8,-8c0,-4.42969 3.57031,-8 8,-8z"></path></g></g>
                    </svg>
                </button>
            </form>
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
                        <td><?php
                            if (htmlspecialchars($user["admin"]) == 1) {
                                echo "<span class='admin'>Admin</span>";
                            } else {
                                echo "<span class='user'>User</span>";
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
                            <!-- Edit User Form -->
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($user["id"]); ?>">
                                <input type="text" name="username" value="<?php echo htmlspecialchars($user["username"]); ?>" required>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required>
                                <button class="edit" type="submit" name="editUser">Edit</button>
                            </form>
                            <!-- Delete User Form -->
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
