document.addEventListener("DOMContentLoaded", function() {
    const addUserButton = document.getElementById("addUserBtn");
    const deleteUserButton = document.getElementById("deleteUserBtn");
    const registerModal = document.getElementById("registerModal");
    const deleteModal = document.getElementById("deleteModal");
    const closeButtons = document.querySelectorAll(".close");

    if (addUserButton && registerModal && closeButtons && deleteModal && deleteUserButton) {
        addUserButton.onclick = function() {
            registerModal.style.display = "flex";
        };

        deleteUserButton.onclick = function() {
            deleteModal.style.display = "flex";
        };

        closeButtons.forEach(button => {
            button.onclick = function() {
                registerModal.style.display = "none";
                deleteModal.style.display = "none";
            };
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
    }
});
