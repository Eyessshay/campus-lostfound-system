<?php

session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?error=login_required");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('非法请求');
}

$user_id = $_SESSION['user_id'];
$type = isset($_POST['type']) ? $_POST['type'] : '';
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$item_time = isset($_POST['item_time']) ? $_POST['item_time'] : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$image_path = '';

if ($type === '' || $title === '' || $description === '' || $location === '' || $item_time === '' || $contact === '') {
    exit('请填写完整信息');
}

// Convert time format from HTML5 datetime-local to MySQL datetime format
$item_time = date('Y-m-d H:i:s', strtotime($item_time));
if ($item_time === '1970-01-01 08:00:00' || !$item_time) {
    exit('时间格式错误');
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        exit('图片大小不能超过2MB');
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($ext, $allowed, true)) {
        exit('仅支持 JPG、JPEG、PNG、GIF 图片');
    }

    // Validate that the uploaded file is actually an image
    $image_info = getimagesize($_FILES['image']['tmp_name']);
    if ($image_info === false) {
        exit('上传的文件不是有效的图片');
    }

    $file_name = uniqid('img_', true) . '.' . $ext;
    $save_path = '../uploads/' . $file_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $save_path)) {
        $image_path = 'uploads/' . $file_name;
    }
}

if ($type === 'lost') {
    $sql = "INSERT INTO lost_items (user_id, title, description, lost_location, lost_time, contact, image)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
} elseif ($type === 'found') {
    $sql = "INSERT INTO found_items (user_id, title, description, found_location, found_time, contact, image)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
} else {
    exit('发布类型错误');
}

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('数据库操作失败');
}

mysqli_stmt_bind_param($stmt, "issssss", $user_id, $title, $description, $location, $item_time, $contact, $image_path);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../publish.php?success=1");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

exit('发布失败');

?>
