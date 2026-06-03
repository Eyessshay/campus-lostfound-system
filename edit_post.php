<?php
require_once 'config/auth.php';
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

$page_title = '编辑信息';
$page_badge = '编辑信息';
$has_record = false;
$error_message = '信息不存在或无权限。';

$title = '';
$description = '';
$location = '';
$item_time = '';
$contact = '';
$current_image = '';

if ($type === 'lost') {
    $page_title = '编辑失物信息';
    $page_badge = '编辑失物信息';
} elseif ($type === 'found') {
    $page_title = '编辑招领信息';
    $page_badge = '编辑招领信息';
}

if ($type !== 'lost' && $type !== 'found' || !is_numeric($id)) {
    $type = '';
    $id = '';
} else {
    $id = intval($id);

    if ($type === 'lost') {
        $sql = "SELECT * FROM lost_items WHERE lost_id = ? AND user_id = ?";
    } else {
        $sql = "SELECT * FROM found_items WHERE found_id = ? AND user_id = ?";
    }

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $item_id, $owner_id, $title, $description, $location, $item_time, $contact, $current_image, $status, $created_at);
            mysqli_stmt_fetch($stmt);
            $has_record = true;
            $item_time_value = '';

            if (!empty($item_time)) {
                $timestamp = strtotime($item_time);
                if ($timestamp !== false) {
                    $item_time_value = date('Y-m-d\TH:i', $timestamp);
                }
            }
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - 校园失物招领系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php">校园失物招领系统</a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a class="btn btn-outline-primary btn-sm" href="index.php">首页</a>
                <a class="btn btn-outline-primary btn-sm" href="my_posts.php">我的发布</a>
                <span class="text-muted">欢迎，<?php echo htmlspecialchars($username); ?></span>
                <a class="btn btn-outline-secondary btn-sm" href="api/logout.php">退出登录</a>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-3"><?php echo htmlspecialchars($page_title); ?></h1>

                        <?php if (!$has_record): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                            <a class="btn btn-outline-secondary" href="my_posts.php">返回我的发布</a>
                        <?php else: ?>
                            <div class="mb-4">
                                <span class="badge text-bg-primary"><?php echo htmlspecialchars($page_badge); ?></span>
                            </div>

                            <?php if (!empty($current_image) && file_exists($current_image)): ?>
                                <div class="mb-4">
                                    <label class="form-label">当前图片</label>
                                    <div class="border rounded-3 p-2 bg-white">
                                        <img src="<?php echo htmlspecialchars($current_image); ?>" class="img-fluid rounded" alt="当前图片">
                                    </div>
                                </div>
                            <?php elseif (!empty($current_image)): ?>
                                <div class="mb-4">
                                    <label class="form-label">当前图片</label>
                                    <div class="alert alert-warning mb-0" role="alert">图片文件不存在，无法预览。</div>
                                </div>
                            <?php endif; ?>

                            <form action="api/update_post.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$id); ?>">

                                <div class="mb-3">
                                    <label for="title" class="form-label">标题</label>
                                    <input type="text" class="form-control" id="title" name="title" maxlength="100" required value="<?php echo htmlspecialchars($title); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">描述</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="location" class="form-label">地点</label>
                                    <input type="text" class="form-control" id="location" name="location" maxlength="100" required value="<?php echo htmlspecialchars($location); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="item_time" class="form-label">时间</label>
                                    <input type="datetime-local" class="form-control" id="item_time" name="item_time" required value="<?php echo htmlspecialchars(isset($item_time_value) ? $item_time_value : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="contact" class="form-label">联系方式</label>
                                    <input type="text" class="form-control" id="contact" name="contact" maxlength="50" required value="<?php echo htmlspecialchars($contact); ?>">
                                </div>

                                <div class="mb-4">
                                    <label for="image" class="form-label">更换图片（可选）</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                </div>

                                <button type="submit" class="btn btn-primary">保存修改</button>
                                <a class="btn btn-outline-secondary ms-2" href="my_posts.php">返回我的发布</a>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>