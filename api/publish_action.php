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

$item_time = str_replace('T', ' ', $item_time) . ':00';

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = time() . '.' . $ext;
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
