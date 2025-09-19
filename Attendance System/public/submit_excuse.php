<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../student/Student.php';
require_once __DIR__ . '/../student/ExcuseLetter.php';
require_once __DIR__ . '/../core/FormHandler.php';

$student = new Student($_SESSION['user_id']);
$excuseLetter = new ExcuseLetter($student->getId());

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = FormHandler::sanitize($_POST['reason'] ?? '');

    if (empty($reason)) {
        $error = "Please provide a reason for the excuse.";
    } else {
        if ($excuseLetter->submitExcuse($student->getCourseId(), $student->getYearLevel(), $reason)) {
            $message = "Excuse letter submitted successfully.";
        } else {
            $error = "Failed to submit excuse letter. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Excuse Letter - Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Submit Excuse Letter</h2>
    <p>Welcome, <?= htmlspecialchars($student->getUsername()) ?></p>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="reason">Reason for Excuse</label>
            <textarea id="reason" name="reason" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Excuse</button>
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</body>
</html>
