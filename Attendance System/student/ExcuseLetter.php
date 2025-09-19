<?php
require_once __DIR__ . '/../core/Database.php';

class ExcuseLetter {
    private $conn;
    private $studentId;

    public function __construct($studentId = null) {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->studentId = $studentId;
    }

    public function submitExcuse($courseId, $yearLevel, $reason) {
        $sql = "INSERT INTO excuse_letters (student_id, course_id, year_level, reason) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$this->studentId, $courseId, $yearLevel, $reason]);
    }

    public function getExcuseStatus() {
        $sql = "SELECT status FROM excuse_letters WHERE student_id = ? ORDER BY submitted_date DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->studentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['status'] : null;
    }

    public function getExcusesByCourseYear($courseId, $yearLevel) {
        $sql = "SELECT el.id, u.username, el.reason, el.submitted_date, el.status, el.reviewed_date, el.admin_id
                FROM excuse_letters el
                JOIN users u ON el.student_id = u.id
                WHERE el.course_id = ? AND el.year_level = ?
                ORDER BY el.submitted_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$courseId, $yearLevel]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($excuseId, $status, $adminId) {
        $sql = "UPDATE excuse_letters SET status = ?, reviewed_date = NOW(), admin_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $adminId, $excuseId]);
    }
}
?>
