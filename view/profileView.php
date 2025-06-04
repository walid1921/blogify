<div class="profilePage">
    <?php if(isset($_SESSION["message"])): ?>
        <div class="notification-container">
            <div class="notification <?php echo $_SESSION["msg_type"]?>">
                <?php echo $_SESSION["message"];?>
                <?php unset($_SESSION["message"]);?>
            </div>
        </div>
    <?php endif; ?>

    <div>
        <h2>Account</h2>
        <p class="account-info"><span>Hi, <?php echo $this->getCurrentUser(); ?>!</span> Update your account information here.</p>

        <div class="user-header">
            <!-- Edit Profile -->
            <div class="update-user">
                <div class="form-container">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="text" name="username" placeholder="username"
                               value="<?php echo htmlspecialchars($this->getUser()['username']); ?>" required>
                        <?php if (!empty($this->getErrors()['username'])): ?>
                            <span class="error"><?php echo $this->getErrors()['username'] ?></span>
                        <?php endif; ?><br>

                        <input type="email" name="email" placeholder="email"
                               value="<?php echo htmlspecialchars($this->getUser()['email']); ?>" required>
                        <?php if (!empty($this->getErrors()['email'])): ?>
                            <span class="error"><?php echo $this->getErrors()['email'] ?></span>
                        <?php endif; ?><br>

                        <button class="primary-button" type="submit" name="editProfileUser">Save changes</button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div id="passwordForm">
                <div class="password-user">
                    <div class="form-container">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="password" name="password" placeholder="New password" required>
                            <?php if (!empty($this->getErrors()['password'])): ?>
                                <span class="error"><?php echo $this->getErrors()['password']; ?></span>
                            <?php endif; ?><br>

                            <input type="password" name="confPassword" placeholder="Confirm password" required>
                            <?php if (!empty($this->getErrors()['confPassword'])): ?>
                                <span class="error"><?php echo $this->getErrors()['confPassword']; ?></span>
                            <?php endif; ?><br>

                            <button class="primary-button" type="button" id="passwordBtn">Save</button>

                            <div class="confirmationStep">
                                <button class="delete-button" id="passwordConfirm" style="display: none;"
                                        type="submit" name="passwordProfileUser">Confirm</button>
                                <button id="cancelConfirm" class="cancelConfirm" style="display: none;"
                                        type="button">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User Info + Delete Button -->
            <div id="userInfo">
                <div class="user-info-content">
                    <div>
                        <span>Username: <?php echo htmlspecialchars($this->getUser()['username']); ?></span>
                    </div>
                    <div>
                        <span>Email: <?php echo htmlspecialchars($this->getUser()['email']); ?></span>
                    </div>
                    <div>
                        <span>Age: <?php echo htmlspecialchars($this->getUser()['age']); ?></span>
                    </div>
                    <div>
                        <span>Phone: <?php echo htmlspecialchars($this->getUser()['phone']); ?></span>
                    </div>
                    <div>
                        <span>Gender: <?php echo htmlspecialchars($this->getUser()['gender']); ?></span>
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
                        <?php if (!empty($this->getErrors()['deletePassword'])): ?>
                            <span class="error"><?php echo $this->getErrors()['deletePassword']; ?></span>
                        <?php endif; ?>
                        <button class="delete-button" type="submit" name="deleteProfileUser">Confirm Deletion</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/modal.js"></script>
