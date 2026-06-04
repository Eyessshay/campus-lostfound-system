<?php
require_once '../config/auth.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('非法请求');
}

$user_id = $_SESSION['user_id'];
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$item_time = isset($_POST['item_time']) ? trim($_POST['item_time']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';

if (($type !== 'lost' && $type !== 'found') || !is_numeric($id)) {
    exit('信息不存在或无权限。');
}

if ($title === '' || $description === '' || $location === '' || $item_time === '' || $contact === '') {
    exit('请填写完整信息');
}

$id = intval($id);
$item_time = str_replace('T', ' ', $item_time) . ':00';

if ($type === 'lost') {
    $select_sql = "SELECT image FROM lost_items WHERE lost_id = ? AND user_id = ?";
    $update_sql = "UPDATE lost_items SET title = ?, description = ?, lost_location = ?, lost_time = ?, contact = ?, image = ? WHERE lost_id = ? AND user_id = ?";
} else {
    $select_sql = "SELECT image FROM found_items WHERE found_id = ? AND user_id = ?";
    $update_sql = "UPDATE found_items SET title = ?, description = ?, found_location = ?, found_time = ?, contact = ?, image = ? WHERE found_id = ? AND user_id = ?";
}

$select_stmt = mysqli_prepare($conn, $select_sql);
if (!$select_stmt) {
    exit('数据库操作失败');
}

mysqli_stmt_bind_param($select_stmt, 'ii', $id, $user_id);
mysqli_stmt_execute($select_stmt);
mysqli_stmt_store_result($select_stmt);

if (mysqli_stmt_num_rows($select_stmt) !== 1) {
    mysqli_stmt_close($select_stmt);
    exit('信息不存在或无权限。');
}

mysqli_stmt_bind_result($select_stmt, $existing_image);
mysqli_stmt_fetch($select_stmt);
mysqli_stmt_close($select_stmt);

$image_path = $existing_image;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        exit('图片大小不能超过2MB');
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($ext, $allowed, true)) {
        exit('仅支持 JPG、JPEG、PNG、GIF 图片');
    }

    $file_name = uniqid('img_', true) . '.' . $ext;
    $save_path = '../uploads/' . $file_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $save_path)) {
        $image_path = 'uploads/' . $file_name;
    }
}

$update_stmt = mysqli_prepare($conn, $update_sql);
if (!$update_stmt) {
    exit('数据库操作失败');
}

mysqli_stmt_bind_param($update_stmt, 'ssssssii', $title, $description, $location, $item_time, $contact, $image_path, $id, $user_id);

if (mysqli_stmt_execute($update_stmt)) {
    mysqli_stmt_close($update_stmt);
    mysqli_close($conn);
    header('Location: ../my_posts.php?update=success');
    exit;
}

mysqli_stmt_close($update_stmt);
mysqli_close($conn);
exit('更新失败');