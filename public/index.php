<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Staff.php';
require_once '../classes/Department.php';
require_once '../classes/Surat.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role == 'department') {
    $user = new Department($db);
    $user->id = $user_id;
    $drafts = $user->getDrafts();
} else {
    $user = new Staff($db);
    $drafts = $user->getAllDrafts();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Management Surat</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Management Surat</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <h2 class="text-center">Draft Surat</h2>
    <?php if ($role == 'department'): ?>
        <div class="mb-3 text-right">
            <a href="upload_draft.php" class="btn btn-primary">Upload Draft</a>
        </div>
    <?php endif; ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Department</th>
                <th>Draft File</th>
                <th>Status</th>
                <th>No. Surat</th>
                <?php if ($role == 'staff'): ?>
                    <th>Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($drafts as $index => $draft): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $draft['department_name'] ?></td>
                    <td><a href="../uploads/<?= $draft['draft_file'] ?>" target="_blank"><?= $draft['draft_file'] ?></a></td>
                    <td><?= $draft['status'] ?></td>
                    <td><?= $draft['no_surat'] ?></td>
                    <?php if ($role == 'staff' && $draft['status'] == 'pending'): ?>
                        <td>
                            <a href="approved_draft.php?id=<?= $draft['id'] ?>" class="btn btn-success">Approve</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
