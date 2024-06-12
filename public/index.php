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

// Get status filter from query parameters
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Function to get drafts based on the filter
function getDrafts($db, $statusFilter, $role, $user_id) {
    if ($role == 'department') {
        $query = "SELECT s.*, d.department_name FROM surat s JOIN department d ON s.department_id = d.id WHERE d.id = :department_id";
        if ($statusFilter != 'all') {
            $query .= " AND s.status = :status";
        }
    } else {
        $query = "SELECT s.*, d.department_name FROM surat s JOIN department d ON s.department_id = d.id";
        if ($statusFilter != 'all') {
            $query .= " WHERE s.status = :status";
        }
    }

    $stmt = $db->prepare($query);

    if ($role == 'department') {
        $stmt->bindParam(':department_id', $user_id);
    }

    if ($statusFilter != 'all') {
        $stmt->bindParam(':status', $statusFilter);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$drafts = getDrafts($db, $statusFilter, $role, $user_id);

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

    <!-- Filter Form -->
    <form method="GET" class="form-inline mb-3">
        <label class="mr-2" for="status">Filter by Status: </label>
        <select name="status" id="status" class="form-control mr-2">
            <option value="all" <?= $statusFilter == 'all' ? 'selected' : '' ?>>All</option>
            <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $statusFilter == 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="reject" <?= $statusFilter == 'reject' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Department</th>
                <th>Draft File</th>
                <th>Uploaded At</th>
                <th>Approved At</th>
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
                    <td><?= $draft['uploaded_at'] ?></td>
                    <td class="text-center"><?= $draft['approved_at'] ?? '-' ?></td>
                    <td><?= $draft['status'] == 'reject' ? "Maaf surat Anda ditolak, mohon menghubungi staff untuk revisi lebih lanjut" : $draft['status']  ?></td>
                    <td><?= $draft['no_surat'] ?></td>
                    <?php if ($role == 'staff' && $draft['status'] == 'pending'): ?>
                        <td class="text-center">
                            <a href="approved_draft.php?id=<?= $draft['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                            <a href="reject.php?id=<?= $draft['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
