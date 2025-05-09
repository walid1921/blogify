document.addEventListener("DOMContentLoaded", function () {
    const addUserButton = document.getElementById("addUserBtn");
    const registerModal = document.getElementById("registerModal");
    const editUserBtn = document.getElementById("editUserBtn");
    const deleteUserButton = document.getElementById("deleteUserBtn");
    const deleteModal = document.getElementById("deleteModal");
    const userInfo = document.getElementById("userInfo");
    const closeButtons = document.querySelectorAll(".close");
    const closeDeleteModal = document.querySelector(".closeDeleteModal");


    const editButtons = document.querySelectorAll(".editUserBtn");
    const editUserModal = document.getElementById("editUserModal");
    const closeEditUserModal = document.querySelector(".closeEditUserModal");

    const editUserIdInput = document.getElementById("editUserId");
    const editUsernameInput = document.getElementById("editUsername");
    const editEmailInput = document.getElementById("editEmail");

    // Register Modal Logic
    if (addUserButton && registerModal) {
        addUserButton.onclick = function () {
            registerModal.style.display = "flex";
        };

        closeButtons.forEach(button => {
            button.onclick = function () {
                registerModal.style.display = "none";
            };
        });
    }

    // Edit Modal Logic
    if (editUserBtn && editUserModal && closeEditUserModal) {
        editUserBtn.onclick = function () {
            editUserModal.style.display = "flex";
        };

        closeEditUserModal.onclick = function () {
            editUserModal.style.display = "none";
        };
    }

    // Delete Modal Logic
    if (deleteUserButton && deleteModal && userInfo && closeDeleteModal) {
        deleteUserButton.onclick = function () {
            deleteModal.style.display = "flex";
            requestAnimationFrame(() => {
                deleteModal.classList.add("show");
            });
            userInfo.classList.add("hide");
        };

        closeDeleteModal.onclick = function () {
            deleteModal.classList.remove("show");
            userInfo.classList.remove("hide");

            deleteModal.addEventListener("transitionend", function handler() {
                deleteModal.style.display = "none";
                deleteModal.removeEventListener("transitionend", handler);
            });
        };
    }


    editButtons.forEach(button => {
        button.addEventListener("click", () => {
            const userId = button.getAttribute("data-user-id");
            const username = button.getAttribute("data-username");
            const email = button.getAttribute("data-email");

            // Fill modal inputs
            editUserIdInput.value = userId;
            editUsernameInput.value = username;
            editEmailInput.value = email;

            // Show modal
            editUserModal.style.display = "flex";
        });
    });

    if (closeEditUserModal) {
        closeEditUserModal.addEventListener("click", () => {
            editUserModal.style.display = "none";
        });
    }
});


// Close when clicking outside modal
// window.onclick = function(event) {
//     if (event.target === registerModal) {
//         registerModal.style.display = "none";
//     }
//     if (event.target === deleteModal) {
//         deleteModal.style.display = "none";
//     }
// };
