<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Staff.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$staff = new Staff($db);

$draftId = $_GET['id'];
if ($staff->approveDraft($draftId)) {
    $message = "Draft approved successfully.";
} else {
    $message = "Failed to approve draft.";
}

header("Location: index.php?message=" . urlencode($message));
exit;
