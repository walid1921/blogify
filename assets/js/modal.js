console.log("Modal JS loaded");

document.addEventListener("DOMContentLoaded", function () {
    const addUserButton = document.getElementById("addUserBtn");
    const deleteAdminButton = document.getElementById("deleteAdminBtn");

    // Open Register Modal
    if (addUserButton) {
        addUserButton.addEventListener("click", function () {
            const modal = createRegisterModal();
            document.body.appendChild(modal);

            modal.querySelector(".close").addEventListener("click", function () {
                modal.remove();
            });

            // Optional: Close when clicking outside
            modal.addEventListener("click", function (e) {
                if (e.target === modal) modal.remove();
            });
        });
    }

    // Open Delete Modal
    if (deleteAdminButton) {
        deleteAdminButton.addEventListener("click", function () {
            const modal = createDeleteModal();
            document.body.appendChild(modal);

            modal.querySelector(".closeModal").addEventListener("click", function () {
                modal.remove();
            });

            modal.addEventListener("click", function (e) {
                if (e.target === modal) modal.remove();
            });
        });
    }

    function createRegisterModal() {
        const modal = document.createElement("div");
        modal.id = "registerModal";
        modal.className = "modal";
        modal.style.display = "flex";
        modal.innerHTML = `
            <div class="form-container">
                <div class="modal-header">
                    <h4>Register New User</h4>
                    <span class="close">&times;</span>
                </div>
                <form method="POST" action="">
                    <input type="text" name="username" placeholder="username" required><br>
                    <input type="email" name="email" placeholder="email" required><br>
                    <input type="password" name="password" placeholder="password" required><br>
                    <input type="password" name="confPassword" placeholder="confirm password" required><br>
                    <input type="number" name="age" placeholder="age" required><br>
                    <input type="number" name="phone" placeholder="phone number"><br>
                    <div>
                        <input type="radio" name="gender" value="Male"> Male
                        <input type="radio" name="gender" value="Female"> Female
                        <input type="radio" name="gender" value="Other"> Other
                    </div><br>
                    <label><input type="checkbox" name="terms" value="agree"> I agree to the terms and conditions</label><br>
                    <input type="submit" value="Register">
                </form>
            </div>
        `;
        return modal;
    }

    function createDeleteModal() {
        const modal = document.createElement("div");
        modal.id = "deleteModal";
        modal.className = "deleteAdminModal";
        modal.style.display = "flex";
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Delete Account</h4>
                    <span class="closeModal">&times;</span>
                </div>
                <p>No longer want to use our service? This action is not reversible.</p>
                <form method="post" onSubmit="return confirm('Are you sure you want to delete your account? After confirmation, you will be logged out');">
                    <button class="delete-button" type="submit" name="deleteAdmin">Yes, delete my account</button>
                </form>
            </div>
        `;
        return modal;
    }
});
