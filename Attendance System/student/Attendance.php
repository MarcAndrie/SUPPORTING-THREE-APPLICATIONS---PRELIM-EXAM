<?php
class AttendanceStudent {
    private $conn;
    private $userId;

    public function __construct($userId) {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->userId = $userId;
    }

    public function getAttendanceHistory() {
        $sql = "SELECT attendance_date, status, is_late FROM attendance WHERE user_id = ? ORDER BY attendance_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
