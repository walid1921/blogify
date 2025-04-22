<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "components/toast.php";

if(!isLoggedIn()) {
    redirect("login.php");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
//    $task = trim($_POST["task"]);
    echo "Ok";
}

include "./components/header.php";
?>

    <div class="todoApp">

        <!-- Notification Container -->
        <div class="notification-container">
            <div class="notification success">
                <!-- Success message will go here -->
            </div>
        </div>

        <!-- Main Content Container -->

            <h1>Todo App</h1>

            <!-- Add Task Form -->
            <form method="POST">
                <input type="text" name="task" placeholder="Enter a new task" required>
                <button type="submit" name="add_task">Add Task</button>
            </form>

            <!-- Display Tasks -->
            <ul>
                <li class="completed">
                    <span class="completed">Sample Task</span>
                    <div>
                        <!-- Complete Task -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="1">
                            <button class="complete" type="submit" name="complete_task">Complete</button>
                        </form>

                        <!-- Undo Completed Task -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="1">
                            <button class="undo" type="submit" name="undo_complete_task">Undo</button>
                        </form>

                        <!-- Delete Task -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="1">
                            <button class="delete" type="submit" name="delete_task">Delete</button>
                        </form>
                    </div>
                </li>

                <li>
                    <span>Another Task</span>
                    <div>
                        <!-- Complete Task -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="2">
                            <button class="complete" type="submit" name="complete_task">Complete</button>
                        </form>

                        <!-- Delete Task -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="2">
                            <button class="delete" type="submit" name="delete_task">Delete</button>
                        </form>
                    </div>
                </li>
            </ul>
    </div>


<?php include "./components/footer.php"; ?>
