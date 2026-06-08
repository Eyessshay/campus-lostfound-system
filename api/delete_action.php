<?php
require_once '../config/auth.php';
require_once '../config/db.php';

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if (empty($type) || empty($id) || !in_array($type, ['lost', 'found'])) {
    header("Location: ../my_posts.php?error=invalid_params");
    exit;
}

if (!is_numeric($id)) {
    header("Location: ../my_posts.php?error=invalid_id");
    exit;
}

$id = intval($id);

if ($type === 'lost') {

    $sql = "DELETE FROM lost_items WHERE lost_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        header("Location: ../my_posts.php?error=delete_failed");
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../my_posts.php?error=delete_failed");
        exit;
    }
    
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    if ($affected_rows === 0) {
        header("Location: ../my_posts.php?error=not_found");
        exit;
    }
} else if ($type === 'found') {
    $sql = "DELETE FROM found_items WHERE found_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        header("Location: ../my_posts.php?error=delete_failed");
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../my_posts.php?error=delete_failed");
        exit;
    }
    
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    
    if ($affected_rows === 0) {
        header("Location: ../my_posts.php?error=not_found");
        exit;
    }
}

header("Location: ../my_posts.php?delete=success");
exit;
?>
