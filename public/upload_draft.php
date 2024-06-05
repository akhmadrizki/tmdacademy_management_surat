<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Department.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'department') {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$department = new Department($db);
$department->id = $_SESSION['user_id'];

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['draft_file']) && $_FILES['draft_file']['error'] == 0) {
        $file_name = $_FILES['draft_file']['name'];
        $file_tmp = $_FILES['draft_file']['tmp_name'];
        $file_destination = '../uploads/' . $file_name;

        if (move_uploaded_file($file_tmp, $file_destination)) {
            if ($department->uploadDraft($file_name)) {
                $message = 'Draft uploaded successfully.';
            } else {
                $message = 'Failed to upload draft.';
            }
        } else {
            $message = 'Failed to move uploaded file.';
        }
    } else {
        $message = 'No file uploaded or there was an upload error.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Draft</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Upload Draft</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="draft_file">Draft File</label>
            <input type="file" name="draft_file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
        <p class="mt-3"><?= $message ?></p>
    </form>
</div>
</body>
</html>
