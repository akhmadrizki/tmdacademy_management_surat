<?php
class User {
    protected $conn;
    protected $table_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTableName() {
        return $this->table_name;
    }
}
?>
