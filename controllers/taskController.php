<?php
require_once "includes/session.php";
require_once "includes/database.php";
require_once "includes/helpers.php";
require_once "components/toast.php";
require_once __DIR__ . '/../models/Task.php';


class TaskController {

    public function index() {

        $currentUser = $_SESSION['username'];


        $taskModel = new Task();
        $tasks = $taskModel->getAllTasks();

        // to display the tasks
//         echo "<pre>";
//         var_dump($tasks);
//         echo "</pre>";



        if(!isLoggedIn()) {
            redirect("login.php");
        }


        if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //    $task = trim($_POST["task"]);
        //    echo "Ok";

            if(isset($_POST["add_task"])) {
                // Handle adding a new task
                $task = trim($_POST["task"]);
                $taskModel->task = $task;
                $taskModel->createTask();
                redirect("todo.php");

                // Display success message
                // echo "Task added: " . htmlspecialchars($task);
                // Use the toast notification function
                // toast("Task added: " . htmlspecialchars($task), "success");


                // to test
                // $task = trim($_POST["task"]);
                // echo "Task added: " . htmlspecialchars($task);
            }
        }



        // Pass data to the view
        require __DIR__ . '/../view/tasksView.php';
    }
}
