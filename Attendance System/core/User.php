<?php
require_once 'Database.php';

abstract class User {
    protected $conn;
    protected $id;
    protected $username;
    protected $role;

    public function __construct($id = null) {
        $database = new Database();
        $this->conn = $database->getConnection();
        if ($id) {
            $this->id = $id;
            $this->loadUser ();
        }
    }

    abstract protected function loadUser ();

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getRole() {
        return $this->role;
    }
}
?>
