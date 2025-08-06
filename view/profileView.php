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

    <!-- The style of this file is from Bootstrap -->
    <div class=" mt-4">
        <div class="row">
            <div class="col-xl-4">
                <!-- Profile picture card-->
                <div class="card mb-4 mb-xl-0">
                    <div class="">Profile Picture</div>
                    <div class="card-body text-center">
                        <!-- Profile picture image-->
                        <img class="img-account-profile rounded-circle mb-2" src="https://placehold.co/50x50@2x.png" alt="Profile Image">
                        <!-- Profile picture help block-->
                        <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                        <!-- Profile picture upload button-->
                        <input class="form-control" type="file" name="profile_image" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <!-- Account details card-->
                <div class="card mb-4">
                    <div class="card-header">Account Details</div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Form Group (username)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="inputUsername">Username</label>
                                <input class="form-control" id="inputUsername" type="text" placeholder="Please enter Username" value="edwindiaz" disabled>
                            </div>
                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (first name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputFirstName">First name</label>
                                    <input class="form-control" id="inputFirstName" type="text" name="first_name" placeholder="Please enter First name" value="Edwin">
                                </div>
                                <!-- Form Group (last name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="inputLastName">Last name</label>
                                    <input class="form-control" id="inputLastName" type="text" name="last_name" placeholder="Please enter Last name" value="Diaz">
                                </div>
                            </div>
                            <!-- Form Group (email address)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="inputEmailAddress">Email address</label>
                                <input class="form-control" id="inputEmailAddress" type="email" name="email" placeholder="Please enter Email address" value="EdwinDiaz@edwindiaz.com">
                            </div>
                            <!-- Save changes button-->
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/modal.js"></script>
