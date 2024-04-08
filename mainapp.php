<?php
// Database connection setup
$host = 'localhost';
$dbname = 'todo_list';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Automatically update task status based on due date
function updateTaskStatus($conn) {
    $today = date('Y-m-d');
    $sql = "UPDATE tasks SET status = 'overdue' WHERE due_date < ? AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
}

// Fetch tasks by criteria
function fetchTasksByCriteria($conn, $criteria) {
    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $in7Days = date('Y-m-d', strtotime('+7 days'));
    $sql = "";

    switch ($criteria) {
        case 'overdue':
            $sql = "SELECT * FROM tasks WHERE due_date < '$today' AND status = 'overdue' ORDER BY priority ASC";
            break;
        case 'dueToday':
            $sql = "SELECT * FROM tasks WHERE due_date = '$today' ORDER BY priority ASC";
            break;
        case 'dueTomorrow':
            $sql = "SELECT * FROM tasks WHERE due_date = '$tomorrow' ORDER BY priority ASC";
            break;
        case 'dueNext7Days':
            $sql = "SELECT * FROM tasks WHERE due_date > '$today' AND due_date <= '$in7Days' ORDER BY due_date ASC, priority ASC";
            break;
        case 'active':
            $sql = "SELECT * FROM tasks WHERE status = 'active' ORDER BY due_date ASC, priority ASC";
            break;
        case 'completed':
            $sql = "SELECT * FROM tasks WHERE status = 'completed' ORDER BY due_date ASC";
            break;
        default:
            $sql = "SELECT * FROM tasks ORDER BY due_date ASC, priority ASC";
            break;
    }

    $result = $conn->query($sql);
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    return $tasks;
}

// Fetch categories
function fetchCategories($conn) {
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

// Modified fetchWeeklyReport function to accept start and end dates
function fetchWeeklyReport($conn, $startDate = null, $endDate = null) {
    if (!$startDate || !$endDate) {
        $startDate = date('Y-m-d', strtotime('monday this week'));
        $endDate = date('Y-m-d', strtotime('sunday this week'));
    }

    $sql = "SELECT DATE(due_date) as completed_date, COUNT(*) as task_count 
            FROM tasks 
            WHERE due_date BETWEEN ? AND ? AND status = 'completed' 
            GROUP BY completed_date ORDER BY completed_date ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $weeklyReport = [];
    while ($row = $result->fetch_assoc()) {
        $weeklyReport[$row['completed_date']] = $row['task_count'];
    }
    return $weeklyReport;
}


// Determine the current filter from the GET parameter or default to the combined overdue and due today view
$currentFilter = isset($_GET['filter']) ? $_GET['filter'] : 'default';

// Function to fetch tasks based on the current filter
function fetchTasksBasedOnFilter($conn, $currentFilter) {
    switch ($currentFilter) {
        case 'dueToday':
            return fetchTasksByCriteria($conn, 'dueToday');
        case 'dueTomorrow':
            return fetchTasksByCriteria($conn, 'dueTomorrow');
        case 'dueNext7Days':
            return fetchTasksByCriteria($conn, 'dueNext7Days');
        case 'default':
            $overdueTasks = fetchTasksByCriteria($conn, 'overdue');
            $dueTodayTasks = fetchTasksByCriteria($conn, 'dueToday');
            // Merging overdue and due today tasks for the default view
            return array_merge($overdueTasks, $dueTodayTasks);
        default:
            return [];
    }
}

// Update task statuses before any operations
updateTaskStatus($conn);

// Handle POST requests for adding tasks and categories
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add a new task
    if (isset($_POST['addTask'])) {
        $description = $_POST['description'];
        $dueDate = $_POST['dueDate'];
        $category = !empty($_POST['category']) ? $_POST['category'] : null;
        $priority = !empty($_POST['priority']) ? $_POST['priority'] : null;
        $status = 'active'; // Default status
        
        $sql = "INSERT INTO tasks (description, due_date, category_id, priority, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $description, $dueDate, $category, $priority, $status);
        $stmt->execute();
    }
    // Add a new category
    elseif (isset($_POST['addCategory'])) {
        $name = $_POST['categoryName'];
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to prevent form resubmission
    exit;
}

// Handle GET requests for actions like deleting or marking tasks as completed and deleting categories
if (isset($_GET['action'])) {
    $id = intval($_GET['id']);
    switch ($_GET['action']) {
        case 'deleteTask':
            $sql = "DELETE FROM tasks WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            break;
        case 'completeTask':
            $sql = "UPDATE tasks SET status = 'completed' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            break;
        case 'deleteCategory':
            // Update tasks to no category before deleting the category
            $sql = "UPDATE tasks SET category_id = NULL WHERE category_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            // Now delete the category
            $sql = "DELETE FROM categories WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            break;
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to prevent URL manipulation
    exit;
}

// Fetch tasks and categories for display
$tasksToShow = fetchTasksBasedOnFilter($conn, $currentFilter);
$categories = fetchCategories($conn);
$weeklyReport = fetchWeeklyReport($conn);

// Check for date range form submission to filter the weekly report
if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];
    $weeklyReport = fetchWeeklyReport($conn, $startDate, $endDate);
} else {
    $weeklyReport = fetchWeeklyReport($conn); // Fetch the weekly report for the current week by default
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        .task-form, .category-form {
            margin-bottom: 20px;
        }
        input[type="text"], input[type="date"], select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
        .task-list, .category-list {
            list-style: none;
            padding: 0;
        }
        .task, .category {
            background-color: #f9f9f9;
            border: 1px solid #eee;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .task-priority {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 4px;
            color: white;
        }
        .priority-1 { background-color: #ff4136; }
        .priority-2 { background-color: #ff851b; }
        .priority-3 { background-color: #0074d9; }
        .priority-4 { background-color: #2ecc40; }
        .task-completed { text-decoration: line-through; }
        .task-due { color: #999; }
        .container { max-width: 800px; margin: auto; }
        ul.task-list { list-style-type: none; padding-left: 0; }
        li.task { margin-bottom: 10px; }
        .task div { margin-bottom: 5px; }
        .no-tasks { color: #999; }
        footer {
    background: #333333;
    color: #ffffff;
    text-align: center;
    padding: 10px;
    position: floatval;
    bottom: 0;
    width: 100%;
}

footer a {
    color: #ffffff;
}
    </style>
</head>
<body>

<div class="container">
    <h2>Create Task</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="task-form">
        <input type="text" name="description" placeholder="Task description" required>
        <input type="date" name="dueDate" required>
        <select name="category">
            <option value="">Select Category (Optional)</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="priority">
            <option value="">Priority (Optional)</option>
            <option value="1">High</option>
            <option value="2">Medium-High</option>
            <option value="3">Medium</option>
            <option value="4">Low</option>
        </select>
        <button type="submit" name="addTask">Add Task</button>
    </form>

    <div class="container">
    <h1>Task Report</h1>

    <!-- Filter Selection Links -->
    <div class="filters">
        <a href="?filter=default">Overdue & Due Today</a> | 
        <a href="?filter=dueToday">Due Today</a> | 
        <a href="?filter=dueTomorrow">Due Tomorrow</a> | 
        <a href="?filter=dueNext7Days">Due Next 7 Days</a>
    </div>

    <!-- Tasks Display Based on Selected Filter -->
    <h2>Tasks</h2>
    <ul class="task-list">
        <?php if (empty($tasksToShow)): ?>
            <p class="no-tasks">No tasks matching this criteria.</p>
        <?php else: ?>
            <?php foreach ($tasksToShow as $task): ?>
                <li class="task">
                    <div><?php echo htmlspecialchars($task['description']); ?> - <span><?php echo ucfirst(htmlspecialchars($task['status'])); ?></span> - Due: <?php echo htmlspecialchars($task['due_date']); ?></div>
                    <div>
                        <a href="?action=deleteTask&id=<?php echo $task['id']; ?>">Delete Task</a>
                        <?php if ($task['status'] !== 'completed'): ?> | 
                        <a href="?action=completeTask&id=<?php echo $task['id']; ?>">Mark as Completed</a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

   <!-- Weekly Report -->
<h2>Weekly Report</h2>

<!-- Date Range Selection Form -->
<form method="get" action="">
    <div class="form-group">
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate" required>
    </div>
    <div class="form-group">
        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name="endDate" required>
    </div>
    <button type="submit">Filter</button>
</form>

<ul class="task-list">
    <?php if (empty($weeklyReport)): ?>
        <p class="no-tasks">No tasks found within the selected date range.</p>
    <?php else: ?>
        <?php foreach ($weeklyReport as $date => $count): ?>
            <li class="task">
                <div>Date: <?php echo $date; ?> - Tasks Completed: <?php echo $count; ?></div>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

</div>



    <h2>Create Category</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="category-form">
        <input type="text" name="categoryName" placeholder="Category name" required>
        <button type="submit" name="addCategory">Add Category</button>
    </form>

    <h2>Categories</h2>
    <ul class="category-list">
        <?php foreach ($categories as $category): ?>
        <li class="category">
            <span><?php echo htmlspecialchars($category['name']); ?></span> 
            <a href="?action=deleteCategory&id=<?php echo $category['id']; ?>">Delete Category</a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>

<footer>
        <!-- Link to Authors Page -->
        <p>Discover more about the authors behind this project by visiting the <a href="authors.php">Authors Page</a>.</p>
        
        <!-- Link to Index.php -->
        <p>Back to the <a href="index.php">Home Page</a>.</p>
    </footer>


</html>
