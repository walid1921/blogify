<div class="todoApp">

    <!-- Notification Container -->

    <?php if(isset($_SESSION["message"])): ?>
        <div class="notification-container">
            <div class="notification <?php echo $_SESSION["msg_type"]?>">
                <!-- Success message will go here -->
                <?php echo  $_SESSION["message"];?>
                <?php unset($_SESSION["message"]);?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content Container -->

    <div style="text-align:center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;">
            <h1 style="text-align:center; margin-bottom:10px;">
                Hi <?php echo htmlspecialchars($currentUser) ?>!
            </h1>
        <p> Every task you complete brings you closer to your goals 🚀</p>
    </div>


    <!-- Add Task Form -->
    <form method="POST">
        <input type="text" name="task" placeholder="Enter a new task" required>
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <!-- Display Tasks -->
    <ul>
        <?php foreach ($tasks as $task): ?>
        <li class="<?php echo $task['is_completed'] ? "completed" : ""; ?>">

            <div style="display: flex; flex-direction: column; gap: 5px; align-items: flex-start;">
                <span class="<?php echo $task['is_completed'] ? "completed" : ""; ?>"><?= htmlspecialchars($task['task']) ?></span>
                <span style="font-size: 10px"><?= htmlspecialchars($task["created_at"])?></span>
            </div>

            <div>
                <!-- Complete Task -->
                <?php if (!$task['is_completed']) :?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id'])?>">
                        <button class="complete" type="submit" name="complete_task">Complete</button>
                    </form>

                <?php else:?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id'])?>">
                        <button class="undo" type="submit" name="undo_complete_task">Undo</button>
                    </form>
                <?php endif;?>

                <!-- Delete Task -->
                <form onSubmit="return confirm('Are you sure you want to delete this Task?')" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id'])?>">
                    <button class="delete" type="submit" name="delete_task">Delete</button>
                </form>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
