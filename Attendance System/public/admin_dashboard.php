<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../admin/Admin.php';

$admin = new Admin($_SESSION['user_id']);
$courses = $admin->getAllCourses();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['course_name'])) {
    $courseName = trim($_POST['course_name']);
    if ($admin->addCourse($courseName)) {
        $message = "Course added successfully.";
        $courses = $admin->getAllCourses(); // refresh list
    } else {
        $message = "Failed to add course or course already exists.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Welcome, <?= htmlspecialchars($admin->getUsername()) ?></h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h3>Add New Course</h3>
    <form method="POST" action="">
        <div class="mb-3">
            <input type="text" name="course_name" class="form-control" placeholder="Course Name" required />
        </div>
        <button class="btn btn-success" type="submit">Add Course</button>
    </form>

    <h3 class="mt-5">Courses</h3>
    <ul>
        <?php foreach ($courses as $course): ?>
            <li><?= htmlspecialchars($course['course_name']) ?></li>
        <?php endforeach; ?>
    </ul>

    <a href="attendance_report.php" class="btn btn-primary mt-3">View Attendance Report</a>
    <a href="manage_excuses.php" class="btn btn-info mt-3">Manage Excuse Letters</a>
    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
</body>
</html>
