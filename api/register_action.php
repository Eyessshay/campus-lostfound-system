<?php

require_once '../config/db.php';

// 只允许POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('非法请求');
}

// 获取表单数据
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

// 判空
if ($username === '' || $password === '' || $confirm_password === '' || $phone === '') {
    exit('所有字段不能为空');
}

// 密码一致性检查
if ($password !== $confirm_password) {
    exit('两次密码输入不一致');
}

// 手机号长度检查
if (strlen($phone) != 11) {
    exit('手机号格式不正确');
}

// 检查用户名是否已存在
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
    exit('用户名已存在');
}

mysqli_stmt_close($stmt);

// 密码加密
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 默认角色
$role = 'user';

// 插入用户数据
$sql = "INSERT INTO users (username, password, phone, role)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('数据库插入失败');
}

mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $username,
    $hashed_password,
    $phone,
    $role
);

// 执行插入
if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    header("Location: ../login.php");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

exit('注册失败');

?>