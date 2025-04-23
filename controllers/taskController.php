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
        // echo "<pre>";
        // var_dump($tasks);
        // echo "</pre>";



        if(!isLoggedIn()) {
            redirect("login.php");
        }


        if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //    $task = trim($_POST["task"]);
        //    echo "Ok";

            if(isset($_POST["add_task"])) {
                // to test
                // $task = trim($_POST["task"]);
                // echo "Task added: " . htmlspecialchars($task);


                // Handle adding a new task
                $task = trim($_POST["task"]);
                $taskModel->task = $task;
                $taskModel->createTask();
                $_SESSION["message"] = "Task added successfully!";
                $_SESSION["msg_type"] = "success";
                redirect("todo.php");

            } elseif (isset($_POST["complete_task"])){
                // to test:    echo "complete";    OR    var_dump($_POST);   OR   var_dump($_POST["id"]);

                $taskModel->complete($_POST["id"]);
                $_SESSION["message"] = "Task completed successfully!";
                $_SESSION["msg_type"] = "success";
                redirect("todo.php");

            } elseif (isset($_POST["undo_complete_task"])) {
                $taskModel->undoComplete($_POST["id"]);
                $_SESSION["message"] = "Task marked as incomplete!";
                $_SESSION["msg_type"] = "success";
                redirect("todo.php");

            } elseif (isset($_POST["delete_task"])) {
                $taskModel->deleteTask($_POST["id"]);
                $_SESSION["message"] = "Task deleted successfully!";
                $_SESSION["msg_type"] = "success";
                redirect("todo.php");
            }
        }



        // Pass data to the view
        require __DIR__ . '/../view/tasksView.php';
    }
}
