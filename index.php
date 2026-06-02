<?php
session_start();
require_once 'config/db.php';

$is_login = isset($_SESSION['user_id']);
$username = $is_login ? $_SESSION['username'] : '';

$lost_sql = "SELECT * FROM lost_items ORDER BY created_at DESC";
$found_sql = "SELECT * FROM found_items ORDER BY created_at DESC";

$lost_result = mysqli_query($conn, $lost_sql);
$found_result = mysqli_query($conn, $found_sql);
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

        <section class="mb-5">
            <h2 class="h5 mb-3">失物信息</h2>
            <div class="row g-3">
                <?php if ($lost_result && mysqli_num_rows($lost_result) > 0): ?>
                    <?php while ($lost = mysqli_fetch_assoc($lost_result)): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <?php if ($lost['image'] !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($lost['image']); ?>" class="card-img-top" alt="失物图片">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h3 class="h6 card-title"><?php echo htmlspecialchars($lost['title']); ?></h3>
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
                <?php if ($found_result && mysqli_num_rows($found_result) > 0): ?>
                    <?php while ($found = mysqli_fetch_assoc($found_result)): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <?php if ($found['image'] !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($found['image']); ?>" class="card-img-top" alt="招领图片">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h3 class="h6 card-title"><?php echo htmlspecialchars($found['title']); ?></h3>
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
mysqli_close($conn);
?>
