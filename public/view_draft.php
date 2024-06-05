<?php
require_once '../config/Database.php';
require_once '../classes/Surat.php';

$database = new Database();
$db = $database->getConnection();

$surat = new Surat($db);
$stmt = $surat->getDrafts();
$num = $stmt->rowCount();

if($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        echo "ID: {$id} - Draft: {$draft_file} - Status: {$status}<br>";
    }
} else {
    echo "Tidak ada draft yang perlu persetujuan.";
}
?>
