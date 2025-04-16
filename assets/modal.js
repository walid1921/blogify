document.addEventListener("DOMContentLoaded", function() {
    const addUserButton = document.getElementById("addUserBtn");
    const modal = document.getElementById("registerModal");
    const closeButton = document.querySelector(".close");

    if (addUserButton && modal && closeButton) {
        addUserButton.onclick = function() {
            modal.style.display = "flex";
        };

        closeButton.onclick = function() {
            modal.style.display = "none";
        };

        // Optional: Close the modal when clicking outside of it
        // window.onclick = function(event) {
        //     if (event.target === modal) {
        //         modal.style.display = "none";
        //     }
        // };
    }
});