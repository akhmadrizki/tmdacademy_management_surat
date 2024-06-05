<?php
class Surat {
    private $conn;
    private $table_name = "surat";

    public $id;
    public $department_id;
    public $draft_file;
    public $no_surat;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDrafts() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?>
