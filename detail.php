<?php
session_start();
require_once 'config/db.php';

$is_login = isset($_SESSION['user_id']);
$username = $is_login ? $_SESSION['username'] : '';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($type === 'lost') {
    $sql = "SELECT li.*, u.username FROM lost_items li LEFT JOIN users u ON li.user_id = u.user_id WHERE li.lost_id = ?";
    $page_title = '失物详情';
} elseif ($type === 'found') {
    $sql = "SELECT fi.*, u.username FROM found_items fi LEFT JOIN users u ON fi.user_id = u.user_id WHERE fi.found_id = ?";
    $page_title = '招领详情';
} else {
    exit('类型错误');
}

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('数据库查询失败');
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$item = mysqli_fetch_assoc($result);

if (!$item) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit('信息不存在');
}

$item_id = $item[$type === 'lost' ? 'lost_id' : 'found_id'];
$user_id = $item['user_id'];
$title = $item['title'];
$description = $item['description'];
$location = $item[$type === 'lost' ? 'lost_location' : 'found_location'];
$item_time = $item[$type === 'lost' ? 'lost_time' : 'found_time'];
$contact = $item['contact'];
$image = $item['image'];
$status = $item['status'];
$created_at = $item['created_at'];
$author_username = $item['username'];

function getStatusLabelAndClass($status, $type) {
    $map = [
        'lost' => [
            'pending' => ['未找回', 'warning'],
            'found' => ['已找回', 'success'],
        ],
        'found' => [
            'unclaimed' => ['未认领', 'warning'],
            'claimed' => ['已认领', 'success'],
        ],
    ];

    return $map[$type][$status] ?? [$status, 'secondary'];
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - 校园失物招领系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php">校园失物招领系统</a>

            <div class="ms-auto d-flex align-items-center gap-2">
                <?php if ($is_login): ?>
                    <span class="text-muted">欢迎，<?php echo htmlspecialchars($username); ?></span>
                    <a class="btn btn-primary btn-sm" href="publish.php">发布信息</a>
                    <a class="btn btn-outline-secondary btn-sm" href="api/logout.php">退出登录</a>
                <?php else: ?>
                    <a class="btn btn-outline-primary btn-sm" href="login.php">登录</a>
                    <a class="btn btn-primary btn-sm" href="register.php">注册</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <?php if (!empty($image)): ?>
                        <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="物品图片">
                    <?php endif; ?>

                    <div class="card-body p-4 p-md-5">
                        <div class="mb-3">
                            <span class="badge text-bg-primary"><?php echo $page_title; ?></span>
                        </div>

                        <h1 class="h3 mb-2"><?php echo htmlspecialchars($title); ?></h1>
                        <?php [$status_label, $status_class] = getStatusLabelAndClass($status, $type); ?>
                        <div class="mb-3">
                            <span class="badge bg-<?php echo $status_class; ?>"><?php echo htmlspecialchars($status_label); ?></span>
                        </div>

                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($description)); ?></p>

                        <hr>

                        <p class="mb-2">地点：<?php echo htmlspecialchars($location); ?></p>
                        <p class="mb-2">时间：<?php echo htmlspecialchars($item_time); ?></p>
                        <p class="mb-2">联系方式：<?php echo htmlspecialchars($contact); ?></p>
                        <p class="mb-2">发布者：<?php echo htmlspecialchars($author_username ?? '用户'); ?></p>
                        <p class="mb-4">发布时间：<?php echo htmlspecialchars($created_at); ?></p>

                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary" href="index.php">返回首页</a>
                            <?php if ($is_login && (int)$user_id === (int)$_SESSION['user_id']): ?>
                                <a class="btn btn-outline-warning" href="edit_post.php?type=<?php echo htmlspecialchars($type); ?>&id=<?php echo htmlspecialchars($item_id); ?>">编辑</a>
                                <a class="btn btn-outline-danger" href="api/delete_action.php?type=<?php echo htmlspecialchars($type); ?>&id=<?php echo htmlspecialchars($item_id); ?>" onclick="return confirm('确定要删除吗？');">删除</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
