<?php
require_once 'User.php';

class Department extends User {
    protected $table_name = "department";

    public $id;
    public $department_name;
    public $username;
    public $password;

    public function __construct($db) {
        parent::__construct($db);
    }

    public function uploadDraft($draftFile) {
        $query = "INSERT INTO surat (department_id, draft_file, status) VALUES (:department_id, :draft_file, 'pending')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $this->id);
        $stmt->bindParam(':draft_file', $draftFile);
        
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getDepartmentNameById($id) {
        $query = "SELECT department_name FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['department_name'];
    }

    public function cleanDepartmentName($name) {
        // Ganti spasi dengan underscore, dan karakter lain yang tidak diinginkan bisa ditambahkan sesuai kebutuhan
        return preg_replace('/\s+/', '_', $name);
    }
    
    public function getDrafts() {
        $query = "SELECT surat.*, department.department_name FROM surat 
                JOIN department ON surat.department_id = department.id 
                WHERE department_id = :department_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department_id', $this->id);
        $stmt->execute();

        $drafts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $drafts;
    }

}
?>
