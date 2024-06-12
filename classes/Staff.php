<?php
require_once 'User.php';
require_once 'Department.php';

class Staff extends User {
    protected $table_name = "staff";

    public function __construct($db) {
        parent::__construct($db);
    }

    public function approveDraft($draftId) {
        $department = new Department($this->conn);

        // Get department ID
        $query = "SELECT department_id FROM surat WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $draftId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $departmentId = $row['department_id'];

        // Get department name
        $departmentName = $department->getDepartmentNameById($departmentId);

        // Clean department name
        $departmentName = $department->cleanDepartmentName($departmentName);

        // Generate no_surat
        $noSurat = $this->generateNoSurat($departmentName);
        $query = "UPDATE surat SET status = 'approved', no_surat = :no_surat, approved_at = :approved_at WHERE id = :id";

        $date = date('Y-m-d');

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $draftId);
        $stmt->bindParam(':no_surat', $noSurat);
        $stmt->bindParam(':approved_at', $date);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    private function generateNoSurat($departmentName) {
        // Get the current month and year
        $month = date('n');
        $year = date('Y');

        // Convert Bulan
        $romawi = $this->getRomawiNumber($month);

        // Generate the first part of the number
        $query = "SELECT COUNT(id) as count FROM surat WHERE status = 'approved'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $row['count'] + 1;

        // Format the number
        $noSurat = sprintf("%02d/%s/ITBSTIKOM/%s/%s", $count, $departmentName, $romawi, $year);

        return $noSurat;
    }

    public function getRomawiNumber($month)
    {
        $romawi = [
            1  => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romawi[$month];
    }

    public function getAllDrafts() {
        $query = "SELECT surat.*, department.department_name FROM surat 
                JOIN department ON surat.department_id = department.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $drafts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $drafts;
    }

    public function reject($draftId)
    {
        $query = "UPDATE surat SET status = 'reject' WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $draftId);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

}
?>
