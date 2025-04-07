<div class="tableContainer">
    <table class="user-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Age</th>
            <th>Phone</th>
            <th>Gender</th>
            <th>Registration Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <!-- Loop through all users -->
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user["id"]); ?></td>
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
        </tbody>
    </table>
</div>