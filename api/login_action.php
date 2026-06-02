<?php

session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('非法请求');
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
    header("Location: ../login.php?error=empty");
    exit;
}

$sql = "SELECT user_id, username, password, role FROM users WHERE username = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('数据库查询失败');
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
mysqli_stmt_bind_result($stmt, $user_id, $db_username, $db_password, $role);

if (mysqli_stmt_num_rows($stmt) !== 1 || !mysqli_stmt_fetch($stmt) || !password_verify($password, $db_password)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../login.php?error=invalid");
    exit;
}

$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $db_username;
$_SESSION['role'] = $role;

mysqli_stmt_close($stmt);
mysqli_close($conn);

header("Location: ../index.php");
exit;

?>
