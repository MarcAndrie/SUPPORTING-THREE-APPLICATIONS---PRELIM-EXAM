<?php
require_once __DIR__ . '/../core/User.php';

class Student extends User {
    private $courseId;
    private $yearLevel;

    public function __construct($id = null) {
        parent::__construct($id);
    }

    protected function loadUser () {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
        $stmt->execute([$this->id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $this->username = $user['username'];
            $this->role = $user['role'];
            $this->courseId = $user['course_id'];
            $this->yearLevel = $user['year_level'];
        } else {
            throw new Exception("Student user not found.");
        }
    }

    public function getCourseId() {
        return $this->courseId;
    }

    public function getYearLevel() {
        return $this->yearLevel;
    }
}
?>
