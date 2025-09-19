<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../admin/Admin.php';
require_once __DIR__ . '/../admin/Attendance.php';

$admin = new Admin($_SESSION['user_id']);
$attendanceAdmin = new AttendanceAdmin();

$courses = $admin->getAllCourses();

$selectedCourse = $_GET['course_id'] ?? null;
$selectedYear = $_GET['year_level'] ?? null;
$attendanceRecords = [];

if ($selectedCourse && $selectedYear) {
    $attendanceRecords = $attendanceAdmin->getAttendanceByCourseYear($selectedCourse, $selectedYear);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Attendance Report</h2>

    <form method="GET" action="">
        <div class="row mb-3">
            <div class="col-md-5">
                <select name="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>" <?= ($selectedCourse == $course['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <select name="year_level" class="form-select" required>
                    <option value="">Select Year Level</option>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= ($selectedYear == $i) ? 'selected' : '' ?>>Year <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <?php if ($attendanceRecords): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Username</th>
                    <th>Year Level</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Late</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['username']) ?></td>
                        <td><?= htmlspecialchars($record['year_level']) ?></td>
                        <td><?= htmlspecialchars($record['attendance_date']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($record['status'])) ?></td>
                        <td><?= $record['is_late'] ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($selectedCourse && $selectedYear): ?>
        <p>No attendance records found for this course and year level.</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>
