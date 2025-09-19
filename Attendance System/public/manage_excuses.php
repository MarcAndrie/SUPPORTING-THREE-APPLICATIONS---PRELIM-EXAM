<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../admin/Admin.php';
require_once __DIR__ . '/../student/ExcuseLetter.php';

$admin = new Admin($_SESSION['user_id']);
$excuseLetter = new ExcuseLetter();

$courses = $admin->getAllCourses();

$selectedCourse = $_GET['course_id'] ?? null;
$selectedYear = $_GET['year_level'] ?? null;
$excuses = [];

if ($selectedCourse && $selectedYear) {
    $excuses = $excuseLetter->getExcusesByCourseYear($selectedCourse, $selectedYear);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excuse_id'], $_POST['action'])) {
    $excuseId = $_POST['excuse_id'];
    $action = $_POST['action'];
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    if ($excuseLetter->updateStatus($excuseId, $status, $admin->getId())) {
        $message = "Excuse letter " . $status . " successfully.";
        // Refresh list
        if ($selectedCourse && $selectedYear) {
            $excuses = $excuseLetter->getExcusesByCourseYear($selectedCourse, $selectedYear);
        }
    } else {
        $message = "Failed to update excuse letter.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Excuse Letters - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Manage Excuse Letters</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

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

    <?php if ($excuses): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reason</th>
                    <th>Submitted Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($excuses as $excuse): ?>
                    <tr>
                        <td><?= htmlspecialchars($excuse['username']) ?></td>
                        <td><?= htmlspecialchars($excuse['reason']) ?></td>
                        <td><?= htmlspecialchars($excuse['submitted_date']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($excuse['status'])) ?></td>
                        <td>
                            <?php if ($excuse['status'] === 'pending'): ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="excuse_id" value="<?= $excuse['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($selectedCourse && $selectedYear): ?>
        <p>No excuse letters found for this course and year level.</p>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>
