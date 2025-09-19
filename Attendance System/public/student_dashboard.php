<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../student/Student.php';
require_once __DIR__ . '/../student/Attendance.php';
require_once __DIR__ . '/../student/ExcuseLetter.php';

$student = new Student($_SESSION['user_id']);
$attendance = new AttendanceStudent($student->getId());
$history = $attendance->getAttendanceHistory();
$excuseLetter = new ExcuseLetter($student->getId());
$excuseStatus = $excuseLetter->getExcuseStatus();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Welcome, <?= htmlspecialchars($student->getUsername()) ?></h2>
    <a href="student_mark_attendance.php" class="btn btn-success mb-3">File Attendance</a>
    <a href="submit_excuse.php" class="btn btn-warning mb-3">Submit Excuse Letter</a>

    <?php if ($excuseStatus): ?>
        <div class="alert alert-info mb-3">
            <strong>Excuse Letter Status:</strong> <?= ucfirst(htmlspecialchars($excuseStatus)) ?>
        </div>
    <?php endif; ?>

    <h4>Attendance History</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Late</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($history): ?>
                <?php foreach ($history as $record): ?>
                    <tr>
                        <td><?= htmlspecialchars($record['attendance_date']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($record['status'])) ?></td>
                        <td><?= $record['is_late'] ? 'Yes' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No attendance records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
</body>
</html>
