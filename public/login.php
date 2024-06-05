<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/Staff.php';
require_once '../classes/Department.php';

$database = new Database();
$db = $database->getConnection();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role == 'staff') {
        $user = new Staff($db);
    } else {
        $user = new Department($db);
    }

    $query = "SELECT * FROM " . $user->getTableName() . " WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $role;
        header("Location: index.php");
    } else {
        $message = 'Login failed, please check your username and password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center">Login</h2>
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
                <button type="submit" class="btn btn-primary">Login</button>
                <p class="text-danger mt-3"><?= $message ?></p>
            </form>
            <div class="text-center">
                <a href="register.php">Register</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
