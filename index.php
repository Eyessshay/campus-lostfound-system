<?php
session_start();
require_once 'config/db.php';

$is_login = isset($_SESSION['user_id']);
$username = $is_login ? $_SESSION['username'] : '';

$keyword = trim($_GET['keyword'] ?? '');
$search_param = '';

if ($keyword !== '') {
    $search_param = '%' . $keyword . '%';
    
    $lost_sql = "SELECT * FROM lost_items WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC";
    $lost_stmt = mysqli_prepare($conn, $lost_sql);
    mysqli_stmt_bind_param($lost_stmt, "ss", $search_param, $search_param);
    mysqli_stmt_execute($lost_stmt);
    $lost_result = mysqli_stmt_get_result($lost_stmt);
    
    $found_sql = "SELECT * FROM found_items WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC";
    $found_stmt = mysqli_prepare($conn, $found_sql);
    mysqli_stmt_bind_param($found_stmt, "ss", $search_param, $search_param);
    mysqli_stmt_execute($found_stmt);
    $found_result = mysqli_stmt_get_result($found_stmt);
} else {
    $lost_sql = "SELECT * FROM lost_items ORDER BY created_at DESC";
    $found_sql = "SELECT * FROM found_items ORDER BY created_at DESC";
    
    $lost_result = mysqli_query($conn, $lost_sql);
    $found_result = mysqli_query($conn, $found_sql);
}

$lost_count = mysqli_num_rows($lost_result);
$found_count = mysqli_num_rows($found_result);

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
    <title>校园失物招领系统</title>
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
                    <a class="btn btn-outline-primary btn-sm" href="my_posts.php">我的发布</a>
                    <a class="btn btn-outline-secondary btn-sm" href="api/logout.php">退出登录</a>
                <?php else: ?>
                    <a class="btn btn-outline-primary btn-sm" href="login.php">登录</a>
                    <a class="btn btn-primary btn-sm" href="register.php">注册</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="mb-4">
            <h1 class="h3 mb-2">校园失物招领系统</h1>
            <p class="text-muted mb-0">查看校园失物和招领信息，登录后可以发布新的信息。</p>
        </div>

        <div class="mb-4">
            <form method="GET" action="index.php" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control" placeholder="搜索失物或招领信息..." value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit" class="btn btn-primary">搜索</button>
                <?php if ($keyword !== ''): ?>
                    <a href="index.php" class="btn btn-outline-secondary">清空</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($keyword !== ''): ?>
            <div class="alert alert-info" role="alert">
                正在搜索："<?php echo htmlspecialchars($keyword); ?>"<br>
                共找到 <?php echo $lost_count + $found_count; ?> 条相关信息
            </div>
        <?php endif; ?>

        <?php if ($keyword !== '' && $lost_count === 0 && $found_count === 0): ?>
            <div class="alert alert-warning" role="alert">
                未找到相关信息。
            </div>
        <?php endif; ?>

        <section class="mb-5">
            <h2 class="h5 mb-3">失物信息</h2>
            <div class="row g-3">
                <?php if ($lost_count > 0): ?>
                    <?php while ($lost = mysqli_fetch_assoc($lost_result)): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <?php if ($lost['image'] !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($lost['image']); ?>" class="card-img-top" alt="失物图片">
                                <?php endif; ?>
                                <div class="card-body">
                                    <?php [$lost_status_label, $lost_status_class] = getStatusLabelAndClass($lost['status'], 'lost'); ?>
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                        <h3 class="h6 card-title mb-0"><?php echo htmlspecialchars($lost['title']); ?></h3>
                                        <span class="badge bg-<?php echo $lost_status_class; ?> text-nowrap"><?php echo htmlspecialchars($lost_status_label); ?></span>
                                    </div>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($lost['description']); ?></p>
                                    <p class="mb-1">地点：<?php echo htmlspecialchars($lost['lost_location']); ?></p>
                                    <p class="mb-1">时间：<?php echo htmlspecialchars($lost['lost_time']); ?></p>
                                    <a class="btn btn-outline-primary btn-sm mt-2" href="detail.php?type=lost&id=<?php echo $lost['lost_id']; ?>">查看详情</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-light border">暂无失物信息。</div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h2 class="h5 mb-3">招领信息</h2>
            <div class="row g-3">
                <?php if ($found_count > 0): ?>
                    <?php while ($found = mysqli_fetch_assoc($found_result)): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <?php if ($found['image'] !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($found['image']); ?>" class="card-img-top" alt="招领图片">
                                <?php endif; ?>
                                <div class="card-body">
                                    <?php [$found_status_label, $found_status_class] = getStatusLabelAndClass($found['status'], 'found'); ?>
                                    <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                        <h3 class="h6 card-title mb-0"><?php echo htmlspecialchars($found['title']); ?></h3>
                                        <span class="badge bg-<?php echo $found_status_class; ?> text-nowrap"><?php echo htmlspecialchars($found_status_label); ?></span>
                                    </div>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($found['description']); ?></p>
                                    <p class="mb-1">地点：<?php echo htmlspecialchars($found['found_location']); ?></p>
                                    <p class="mb-1">时间：<?php echo htmlspecialchars($found['found_time']); ?></p>
                                    <a class="btn btn-outline-primary btn-sm mt-2" href="detail.php?type=found&id=<?php echo $found['found_id']; ?>">查看详情</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-light border">暂无招领信息。</div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if (isset($lost_stmt)) {
    mysqli_stmt_close($lost_stmt);
}
if (isset($found_stmt)) {
    mysqli_stmt_close($found_stmt);
}
mysqli_close($conn);
?>
