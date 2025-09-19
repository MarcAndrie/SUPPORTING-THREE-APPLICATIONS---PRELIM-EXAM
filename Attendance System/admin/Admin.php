<?php
require_once __DIR__ . '/../core/User.php';

class Admin extends User {
    public function __construct($id = null) {
        parent::__construct($id);
    }

    protected function loadUser () {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
        $stmt->execute([$this->id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $this->username = $user['username'];
            $this->role = $user['role'];
        } else {
            throw new Exception("Admin user not found.");
        }
    }

    public function addCourse($courseName) {
        $stmt = $this->conn->prepare("INSERT INTO courses (course_name) VALUES (?)");
        return $stmt->execute([$courseName]);
    }

    public function getAllCourses() {
        $stmt = $this->conn->query("SELECT * FROM courses ORDER BY course_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
