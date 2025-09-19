<?php
// /student/register.php
session_start();
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/FormHandler.php';

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = FormHandler::sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $course_id = $_POST['course_id'] ?? null;
    $year_level = $_POST['year_level'] ?? null;

    if (empty($username) || empty($password) || empty($confirm_password) || empty($course_id) || empty($year_level)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, role, course_id, year_level) VALUES (?, ?, 'student', ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$username, $passwordHash, $course_id, $year_level])) {
                $success = "Registration successful. You can now <a href='../public/login.php'>login</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

// Fetch courses for dropdown
$stmt = $conn->query("SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Registration - Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Student Registration</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required />
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required />
        </div>

        <div class="mb-3">
            <label>Course/Program</label>
            <select name="course_id" class="form-select" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= (($_POST['course_id'] ?? '') == $course['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Year Level</label>
            <select name="year_level" class="form-select" required>
                <option value="">Select Year Level</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>" <?= (($_POST['year_level'] ?? '') == $i) ? 'selected' : '' ?>>Year <?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <button class="btn btn-primary" type="submit">Register</button>
        <a href="../public/login.php" class="btn btn-link">Back to Login</a>
    </form>
</body>
</html>
