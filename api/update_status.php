<?php
require_once '../config/auth.php';
require_once '../config/db.php';

$user_id = intval($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header("Location: ../my_posts.php?error=invalid_params");
    exit;
}

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if (!in_array($type, ['lost', 'found'], true) || filter_var($id, FILTER_VALIDATE_INT) === false) {
    header("Location: ../my_posts.php?error=invalid_params");
    exit;
}

$id = intval($id);


if ($type === 'lost') {
    $sql = "UPDATE lost_items SET status='found' WHERE lost_id=? AND user_id=? AND status='pending'";
    $notFoundSql = "SELECT 1 FROM lost_items WHERE lost_id=? AND user_id=? LIMIT 1";
} else {
    $sql = "UPDATE found_items SET status='claimed' WHERE found_id=? AND user_id=? AND status='unclaimed'";
    $notFoundSql = "SELECT 1 FROM found_items WHERE found_id=? AND user_id=? LIMIT 1";
}

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    header("Location: ../my_posts.php?error=status_failed");
    mysqli_close($conn);
    exit;
}

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);

if (!mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    header("Location: ../my_posts.php?error=status_failed");
    exit;
}

$affected_rows = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if ($affected_rows === 0) {
    $checkStmt = mysqli_prepare($conn, $notFoundSql);

    if (!$checkStmt) {
        mysqli_close($conn);
        header("Location: ../my_posts.php?error=status_failed");
        exit;
    }

    mysqli_stmt_bind_param($checkStmt, "ii", $id, $user_id);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) === 0) {
        mysqli_stmt_close($checkStmt);
        mysqli_close($conn);
        header("Location: ../my_posts.php?error=not_found");
        exit;
    }

    mysqli_stmt_close($checkStmt);
}

mysqli_close($conn);
header("Location: ../my_posts.php?status=success");
exit;
?>