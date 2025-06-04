<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=job_tracker", 'root', '');
    echo "Connected successfully to dev DB!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
$host = "localhost"; // Change this to your database host
$user = "root"; // Change this to your database username
$pass = ""; // Change this to your database password
$dbname = "todo_app"; // Change this to your database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add new task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $complete_by = $_POST['complete_by'] ?: null;
    if (!empty($title)) {
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, complete_by) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $complete_by);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: index.php");
    exit();
}

// Update a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $complete_by = $_POST['complete_by'] ?: null;

    $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, complete_by = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $description, $complete_by, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Mark as completed
if (isset($_GET['complete'])) {
    $id = intval($_GET['complete']);
    $stmt = $conn->prepare("UPDATE tasks SET completed = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit();
}

// Edit task
$edit_task = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_task = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get active and completed tasks
$activeTasks = $conn->query("SELECT * FROM tasks WHERE completed = 0 ORDER BY created_at DESC");
$completedTasks = $conn->query("SELECT * FROM tasks WHERE completed = 1 ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>To-Do App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleCompleted() {
            const section = document.getElementById('completed-tasks');
            section.classList.toggle('d-none');
        }
    </script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4 text-center"><?php echo $edit_task ? "Edit Task" : "To-Do List"; ?></h1>

        <!-- Form -->
        <form method="POST" class="mb-4 p-4 border rounded bg-white shadow-sm">
            <input type="hidden" name="id" value="<?php echo $edit_task['id'] ?? ''; ?>">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required
                    value="<?php echo $edit_task['title'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"
                    rows="3"><?php echo $edit_task['description'] ?? ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Complete By</label>
                <input type="datetime-local" name="complete_by" class="form-control"
                    value="<?php echo isset($edit_task['complete_by']) ? date('Y-m-d\TH:i', strtotime($edit_task['complete_by'])) : ''; ?>">
            </div>
            <button class="btn btn-<?php echo $edit_task ? 'success' : 'primary'; ?>" type="submit"
                name="<?php echo $edit_task ? 'update' : 'add'; ?>">
                <?php echo $edit_task ? 'Update Task' : 'Add Task'; ?>
            </button>
            <?php if ($edit_task): ?>
                <a href="index.php" class="btn btn-secondary ms-2">Cancel</a>
            <?php endif; ?>
        </form>

        <!-- Show Completed Button -->
        <button class="btn btn-outline-secondary mb-3" onclick="toggleCompleted()">Show/Hide Completed Tasks</button>

        <!-- Active Tasks -->
        <ul class="list-group mb-4">
            <?php while ($task = $activeTasks->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <h5><?php echo htmlspecialchars($task['title']); ?></h5>
                        <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                        <?php if ($task['complete_by']):
                            $isPast = strtotime($task['complete_by']) < time();
                            $color = $isPast ? 'text-danger' : 'text-success';
                            ?>
                            <small class="<?php echo $color; ?>">
                                Complete by: <?php echo date("M d, Y H:i", strtotime($task['complete_by'])); ?>
                                <?php if ($isPast): ?><strong>(Past Due)</strong><?php endif; ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div>
                        <a href="?edit=<?php echo $task['id']; ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                        <a href="?complete=<?php echo $task['id']; ?>" class="btn btn-sm btn-success me-1">Complete</a>
                        <a href="?delete=<?php echo $task['id']; ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Delete this task?');">Delete</a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <!-- Completed Tasks Section (hidden by default) -->
        <div id="completed-tasks" class="d-none">
            <h3>Completed Tasks</h3>
            <ul class="list-group">
                <?php while ($task = $completedTasks->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="text-decoration-line-through"><?php echo htmlspecialchars($task['title']); ?></h5>
                            <p class="mb-1 text-muted"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                            <?php if ($task['complete_by']): ?>
                                <small class="text-muted">
                                    Completed goal: <?php echo date("M d, Y H:i", strtotime($task['complete_by'])); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <a href="?delete=<?php echo $task['id']; ?>" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Delete this completed task?');">Delete</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>

</html>

<?php $conn->close(); ?>
