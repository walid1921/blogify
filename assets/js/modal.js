document.addEventListener("DOMContentLoaded", function () {
    // === DOM Elements ===
    const addUserButton = document.getElementById("addUserBtn");
    const registerModal = document.getElementById("registerModal");
    const editUserBtn = document.getElementById("editUserBtn");
    const deleteUserButton = document.getElementById("deleteUserBtn");
    const deleteModal = document.getElementById("deleteModal");
    const passwordModal = document.getElementById("confirmDeleteModal");
    const userInfo = document.getElementById("userInfo");

    const closeButtons = document.querySelectorAll(".close");
    const closeDeleteModal = document.querySelector(".closeDeleteModal");
    const closePasswordModal = document.querySelector(".closePasswordModal");

    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
    const cancelConfirm = document.querySelector(".cancelConfirmDelete");

    const passwordBtn = document.getElementById("passwordBtn");
    const passwordConfirm = document.getElementById("passwordConfirm");
    const cancelPasswordConfirm = document.getElementById("cancelConfirm");

    const editButtons = document.querySelectorAll(".editUserBtn");
    const editUserModal = document.getElementById("editUserModal");
    const closeEditUserModal = document.querySelector(".closeEditUserModal");

    const editUserIdInput = document.getElementById("editUserId");
    const editUsernameInput = document.getElementById("editUsername");
    const editEmailInput = document.getElementById("editEmail");

    // === Register Modal ===
    if (addUserButton && registerModal) {
        addUserButton.onclick = () => registerModal.style.display = "flex";
        closeButtons.forEach(button => button.onclick = () => registerModal.style.display = "none");
    }

    // === Edit User Modal ===
    if (editUserBtn && editUserModal && closeEditUserModal) {
        editUserBtn.onclick = () => editUserModal.style.display = "flex";
        closeEditUserModal.onclick = () => editUserModal.style.display = "none";
    }

    editButtons.forEach(button => {
        button.addEventListener("click", () => {
            const userId = button.getAttribute("data-user-id");
            const username = button.getAttribute("data-username");
            const email = button.getAttribute("data-email");

            editUserIdInput.value = userId;
            editUsernameInput.value = username;
            editEmailInput.value = email;

            editUserModal.style.display = "flex";
        });
    });

    // === Password Change Two-Step Confirm ===
    if (passwordBtn && passwordConfirm && cancelPasswordConfirm) {
        passwordBtn.addEventListener("click", () => {
            passwordBtn.style.display = "none";
            passwordConfirm.style.display = "inline-block";
            cancelPasswordConfirm.style.display = "inline-block";
        });

        cancelPasswordConfirm.addEventListener("click", () => {
            passwordBtn.style.display = "inline-block";
            passwordConfirm.style.display = "none";
            cancelPasswordConfirm.style.display = "none";
        });
    }

    // === Delete User (Step 1) Modal ===
    if (deleteUserButton && deleteModal) {
        deleteUserButton.onclick = () => {
            deleteModal.style.display = "flex";
            userInfo.style.display = "none";
        };

        if (closeDeleteModal) {
            closeDeleteModal.onclick = () => deleteModal.style.display = "none";
        }

        if (cancelConfirm) {
            cancelConfirm.onclick = () => {
                deleteModal.style.display = "none";
            userInfo.style.display = "flex";
            }
        }

        if (confirmDeleteBtn && passwordModal) {
            confirmDeleteBtn.onclick = () => {
                deleteModal.style.display = "none";
                passwordModal.style.display = "flex";
                userInfo.style.display = "flex";
            };
        }
    }

    // === Delete User (Step 2) Password Modal ===
    if (closePasswordModal) {
        closePasswordModal.onclick = () => {
            passwordModal.style.display = "none";
            userInfo.style.display = "flex";
        };
    }

    // === Global Modal Close When Clicking Outside ===
    window.onclick = function (event) {
        if (event.target === registerModal) registerModal.style.display = "none";
        if (event.target === deleteModal) deleteModal.style.display = "none";
        if (event.target === passwordModal) passwordModal.style.display = "none";
        if (event.target === editUserModal) editUserModal.style.display = "none";
    };
});
