<?php if(isAdmin()): ?>
    <div class="userTable">
        <?php if(isset($_SESSION["message"])): ?>
            <div class="notification-container">
                <div class="notification <?php echo $_SESSION["msg_type"]?>">
                    <?php echo $_SESSION["message"];?>
                    <?php unset($_SESSION["message"]);?>
                </div>
            </div>
        <?php endif; ?>

        <h2>Users</h2>

        <div class="tableContainer">
            <div class="table-header">

                <?php
                $start = $pagination['totalPages'] > 0 ? ($pagination['offset'] + 1) : 0;
                $end   = $pagination['offset'] + count($users); // $end is offset + how many rows we actually fetched in this page
                ?>
                <p>
                    <span><?php echo $end ?> / <?php echo (int)$totalUsers ?> users</span>
                </p>
                <!-- If there are no pages (totalPages = 0), we show 0–0 of 0.-->


                <!-- Search -->
                <div class="search">
                    <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input
                                type="text"
                                name="search"
                                placeholder="Search by username or email"
                                value="<?php echo htmlspecialchars($this->getSearchTerm()); ?>" /> <!-- value echoes the current search, so the input is persistent. -->
                        <button type="submit">
                            <i class="fa-solid fa-magnifying-glass fa-xl"></i>
                        </button>
                    </form>

                    <button id="addUserBtn">
                        <i class="fa-solid fa-plus fa-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <table class="user-table">
                <thead>
                <tr>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Registration Date</th>
                    <th>Published Blogs</th>
                    <th>Update User</th>
                </tr>
                </thead>

                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <?php
                                    if ($user["id"] === $_SESSION['user_id']) {
                                        echo "<span class='admin'>you</span>";
                                    } elseif ($user["admin"]) {
                                        echo "<span class='admin'>admin</span>";
                                    } else {
                                        echo "<span class='user'>user</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        if ($user["is_active"]) {
                                            echo "<span class='active'>Active</span>";
                                        } else {
                                            echo "<span class='inactive'>Inactive</span>";
                                        }
                                    ?>
                                </td>


                                <td><?php echo htmlspecialchars($user["username"]); ?></td>
                                <td><?php echo htmlspecialchars($user["email"]); ?></td>
                                <td><?php echo day_month_year(htmlspecialchars($user["created_at"]));?></td>
                                <td><?php echo htmlspecialchars($user["blogs_num"]); ?></td>

                                <?php if ($user["admin"]): ?>
                                    <td>
                                        <span class="restricted">Restricted!</span>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <div class="action-buttons" >
                                            <button class="edit-btn"
                                                    type="button"
                                                    data-user-id="<?php echo $user['id']; ?>"
                                                    data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                                    data-is-active="<?php echo $user['is_active']; ?>"
                                            >
                                                <span class="edit-text">Edit</span>
                                                <span class="edit-icon"><i class="fa-solid fa-pen fa-sm"></i></span>
                                            </button>

                                            <button class="delete-btn"
                                                    type="button"
                                                    data-user-id="<?php echo $user['id']; ?>">
                                                <span class="delete-icon"><i class="fa-solid fa-trash fa-sm"></i></span>
                                                <span class="delete-text">Delete</span>
                                            </button>
                                        </div>
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


            <!-- Pagination -->
            <?php
                // Helper function local to the view: builds a URL that preserves all GET params
                if (!function_exists('pageUrl')) {
                    function pageUrl($page) {
                        $params = $_GET;          // preserve search and others
                        $params['page'] = $page;  // set target page
                        $qs = http_build_query($params);
                        return htmlspecialchars($_SERVER['PHP_SELF'] . ($qs ? ('?' . $qs) : ''), ENT_QUOTES, 'UTF-8');
                    }
                }
            ?>
            <?php if ($pagination['totalPages'] > 1): ?>
                <nav class="pagination">
                    <?php
                    $current = (int)$pagination['currentPage'];
                    $total   = (int)$pagination['totalPages'];
                    $window  = 2; // how many numbers to show around current
                    ?>

                    <?php if ($current > 1): ?>
                        <a class="page-link prev" href="<?php echo pageUrl($current - 1); ?>">← Prev</a>
                    <?php endif; ?>

                    <?php if ($current > ($window + 1)): ?>
                        <a class="page-link" href="<?php echo pageUrl(1); ?>">1</a>
                        <span class="dots">…</span>
                    <?php endif; ?>

                    <?php for ($i = max(1, $current - $window); $i <= min($total, $current + $window); $i++): ?>
                        <?php if ($i === $current): ?>
                            <span class="page-link active"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a class="page-link" href="<?php echo pageUrl($i); ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($current < ($total - $window)): ?>
                        <span class="dots">…</span>
                        <a class="page-link" href="<?php echo pageUrl($total); ?>"><?php echo $total; ?></a>
                    <?php endif; ?>

                    <?php if ($current < $total): ?>
                        <a class="page-link next" href="<?php echo pageUrl($current + 1); ?>">Next →</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>

        <!-- Register Modal -->
        <div id="registerModal" class="modal">
            <div class="form-container">
                <div class="modal-header">
                    <h4>Register New User</h4>
                    <span class="close">&times;</span>
                </div>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <!-- username-->
                    <input type="text" name="username" placeholder="username" value="<?php echo isset($_POST['username']) ? $_POST['username'] : "" ?>" required>
                    <?php if (isset($this->getErrors()['username'])): ?>
                        <span class="error"><?php echo $this->getErrors()['username'] ?></span>
                    <?php endif; ?>
                    <br>

                    <!-- email-->
                    <input type="email" name="email" placeholder="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : "" ?>" required>
                    <?php if (isset($this->getErrors()['email'])): ?>
                        <span class="error"><?php echo $this->getErrors()['email'] ?></span>
                    <?php endif; ?>
                    <br>

                    <!-- password-->
                    <input type="password" name="password" placeholder="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : "" ?>" required>
                    <p class="pass_validation">• more than 8 characters • uppercase • lowercase • number</p>
                    <?php if (isset($this->getErrors()['password'])): ?>
                        <span class="error"><?php echo $this->getErrors()['password'] ?></span>
                    <?php endif; ?>
                    <br>

                    <input type="password" name="confPassword" placeholder="confirm password" required>
                    <?php if (isset($this->getErrors()['confPassword'])): ?>
                        <span class="error"><?php echo $this->getErrors()['confPassword'] ?></span>
                    <?php endif; ?>
                    <br>


                    <select name="admin">
                        <option value="0" <?php echo isset($_POST['admin']) && $_POST['admin'] === "0" ? "selected" : ""?>>User</option>
                        <option value="1" <?php echo isset($_POST['admin']) && $_POST['admin'] === "1" ? "selected" : ""?>>Admin</option>
                    </select>
                    <?php if (isset($this->getErrors()['admin'])): ?>
                        <span class="error"><?php echo $this->getErrors()['admin'] ?></span>
                    <?php endif; ?>
                    <br>

                    <input type="submit" value="Register" name="registerNewUser">
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="editUserModal" class="editModal">
            <div class="form-container">
                <div class="modal-header">
                    <h4>Update username</h4>
                    <span class="closeEditUserModal">&times;</span>
                </div>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="userId" id="editUserId">

                    <input type="text" name="username" id="editUsername" placeholder="Username" required>
                    <?php if (isset($this->getErrors()['username'])): ?>
                        <span class="error"><?php echo $this->getErrors()['username']; ?></span>
                    <?php endif; ?>
                    <br>

                    <input type="email" name="email" id="editEmail" placeholder="Email" required>
                    <?php if (isset($this->getErrors()['email'])): ?>
                        <span class="error"><?php echo $this->getErrors()['email']; ?></span>
                    <?php endif; ?>
                    <br>


                    <div class="switch-wrapper">
                        <label for="editIsActive">Status:</label>
                        <input type="checkbox" name="is_active" id="editIsActive" class="tgl tgl-ios" value="1" <?php echo isset($_POST['is_active']) && $_POST['is_active'] === "1" ? "checked" : "" ?>>
                        <label class="tgl-btn" for="editIsActive"></label>
                    </div>

                    <input type="submit" value="Save changes" name="editUser">
                </form>
            </div>
        </div>

        <!-- Delete One user Modal -->
        <div id="deleteOneUserModal" class="modal deleteOneUserModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Confirm Deletion</h4>
                    <span class="cancelDeletion close">&times;</span>
                </div>
                <p>Are you sure you want to delete this account <span class="permanently">permanently</span>?</p>

                <div class="deleteOneUserModal-buttons">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="userId" id="deleteUserId">
                        <button class="delete-button" type="submit" name="deleteUser">Delete</button>
                    </form>

                    <button class="cancelConfirmDeleteUser cancelDeletion">Cancel</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="/assets/js/modal.js"></script>
