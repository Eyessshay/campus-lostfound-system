<?php

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('非法请求');
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

if ($username === '' || $password === '' || $confirm_password === '' || $phone === '') {
    exit('所有字段不能为空');
}

if ($password !== $confirm_password) {
    exit('两次密码输入不一致');
}

if (!preg_match('/^1[0-9]{10}$/', $phone)) {
    exit('手机号格式不正确');
}

$sql = "SELECT user_id FROM users WHERE username = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('数据库查询失败');
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit('用户名已存在');
}

mysqli_stmt_close($stmt);

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'user';

$sql = "INSERT INTO users (username, password, phone, role) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('数据库插入失败');
}

mysqli_stmt_bind_param($stmt, "ssss", $username, $hashed_password, $phone, $role);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../login.php?registered=1");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

exit('注册失败');

?>
