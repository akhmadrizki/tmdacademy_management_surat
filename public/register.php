<?php
require_once '../config/Database.php';
require_once '../classes/Staff.php';
require_once '../classes/Department.php';

$database = new Database();
$db = $database->getConnection();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $name = $_POST['name'];

    if ($role == 'staff') {
        $user = new Staff($db);
    } else {
        $user = new Department($db);
    }

    $table_name = $user->getTableName();
    if ($role == 'staff') {
        $query = "INSERT INTO " . $table_name . " (username, password, name) VALUES (:username, :password, :name)";
    } else {
        $query = "INSERT INTO " . $table_name . " (username, password, department_name) VALUES (:username, :password, :name)";
    }

    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':name', $name);

    if ($stmt->execute()) {
        $message = 'Registration successful, you can now login.';
    } else {
        $message = 'Registration failed, please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center">Register</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="staff">Staff</option>
                        <option value="department">Department</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="name">Name / Department Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
                <p class="text-success mt-3"><?= $message ?></p>
            </form>
            <div class="text-center">
                <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
