<?php
include 'db.php'; // Include the database connection file

// Create Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_task'])) {
    $task_name = $_POST['task_name'];

    // Basic validation
    if (empty($task_name)) {
        $error = "Task name cannot be empty.";
    } else {
        $sql = "INSERT INTO tasks (task_name) VALUES ('$task_name')";
        if ($conn->query($sql) === TRUE) {
            $success = "New task created successfully.";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Update Task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_task'])) {
    $task_id = $_POST['task_id'];
    $task_name = $_POST['task_name'];
    $status = $_POST['status'];

    // Basic validation
    if (empty($task_name)) {
        $error = "Task name cannot be empty.";
    } else {
        $sql = "UPDATE tasks SET task_name='$task_name', status='$status' WHERE id=$task_id";
        if ($conn->query($sql) === TRUE) {
            $success = "Task updated successfully.";
        } else {
            $error = "Error updating task: " . $conn->error;
        }
    }
}

// Delete Task
if (isset($_GET['delete_id'])) {
    $task_id = $_GET['delete_id'];
    $sql = "DELETE FROM tasks WHERE id=$task_id";
    if ($conn->query($sql) === TRUE) {
        $success = "Task deleted successfully.";
    } else {
        $error = "Error deleting task: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>To-Do Task management system </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <h1>To-Do Task management system </h1>

    <?php if (isset($error)) {
        echo "<p class='error'>$error</p>";
    } ?>
    <?php if (isset($success)) {
        echo "<p class='success'>$success</p>";
    } ?>

    <!-- Create Task Form -->
    <h2>Add New Task</h2>
    <form method="post">
        <label for="task_name">Task Name:</label>
        <input type="text" name="task_name" id="task_name" required>
        <button type="submit" name="create_task">Add Task</button>
    </form>

    <!-- Display Tasks -->
    <h2>Tasks</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Task Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM tasks";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["task_name"] . "</td>";
                    echo "<td>" . $row["status"] . "</td>";
                    echo "<td>";
                    echo "<a href='?edit_id=" . $row['id'] . "'>Edit</a> | ";
                    echo "<a href='?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this task?\")'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No tasks found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Edit Task Form (displayed when edit_id is set in URL) -->
    <?php
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $sql = "SELECT * FROM tasks WHERE id=$edit_id";
        $result = $conn->query($sql);
        $task = $result->fetch_assoc();
        ?>
        <h2>Edit Task</h2>
        <form method="post">
            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
            <label for="task_name">Task Name:</label>
            <input type="text" name="task_name" id="task_name" value="<?php echo $task['task_name']; ?>" required>
            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="Pending" <?php if ($task['status'] === 'Pending')
                    echo 'selected'; ?>>Pending</option>
                <option value="In-Progress" <?php if ($task['status'] === 'In-Progress')
                    echo 'selected'; ?>>In-Progress
                </option>
                <option value="Completed" <?php if ($task['status'] === 'Completed')
                    echo 'selected'; ?>>Completed</option>
            </select>
            <button type="submit" name="update_task">Update Task</button>
        </form>
    <?php } ?>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>