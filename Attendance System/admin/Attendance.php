<?php
class AttendanceAdmin {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get attendance by course and year level
    public function getAttendanceByCourseYear($courseId, $yearLevel) {
        $sql = "SELECT u.username, u.year_level, a.attendance_date, a.status, a.is_late
                FROM attendance a
                JOIN users u ON a.user_id = u.id
                WHERE u.course_id = ? AND u.year_level = ?
                ORDER BY a.attendance_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$courseId, $yearLevel]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
