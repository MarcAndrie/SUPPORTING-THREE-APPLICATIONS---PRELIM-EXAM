<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../student/Student.php';
require_once __DIR__ . '/../core/FormHandler.php';

$student = new Student($_SESSION['user_id']);
$conn = (new Database())->getConnection();

$message = '';
$error = '';

// Default attendance date is today
$attendance_date = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_date = $_POST['attendance_date'] ?? $attendance_date;
    $status = $_POST['status'] ?? '';
    $is_late = isset($_POST['is_late']) ? 1 : 0;

    // Validate date
    if (!FormHandler::validateDate($attendance_date)) {
        $error = "Invalid date format.";
    } elseif (!in_array($status, ['present', 'absent'])) {
        $error = "Invalid attendance status.";
    } else {
        // Check if attendance already filed for this date
        $stmt = $conn->prepare("SELECT id FROM attendance WHERE user_id = ? AND attendance_date = ?");
        $stmt->execute([$student->getId(), $attendance_date]);
        if ($stmt->fetch()) {
            $error = "You have already filed attendance for this date.";
        } else {
            // Insert attendance record
            $stmt = $conn->prepare("INSERT INTO attendance (user_id, attendance_date, status, is_late) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$student->getId(), $attendance_date, $status, $is_late])) {
                $message = "Attendance filed successfully for {$attendance_date}.";
            } else {
                $error = "Failed to file attendance. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>File Attendance - Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>File Attendance</h2>
    <p>Welcome, <?= htmlspecialchars($student->getUsername()) ?></p>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="attendance_date">Date</label>
            <input type="date" id="attendance_date" name="attendance_date" class="form-control" value="<?= htmlspecialchars($attendance_date) ?>" max="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select" required>
                <option value="">Select Status</option>
                <option value="present" <?= (($_POST['status'] ?? '') === 'present') ? 'selected' : '' ?>>Present</option>
                <option value="absent" <?= (($_POST['status'] ?? '') === 'absent') ? 'selected' : '' ?>>Absent</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input" id="is_late" name="is_late" <?= (isset($_POST['is_late'])) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_late">I am late</label>
        </div>

        <button type="submit" class="btn btn-primary">Submit Attendance</button>
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</body>
</html>
