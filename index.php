<?php
session_start();

$is_login = isset($_SESSION['user_id']);
$username = $is_login ? $_SESSION['username'] : '';
$role = $is_login ? $_SESSION['role'] : '';
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
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-3">校园失物招领系统</h1>
                        <p class="text-muted mb-4">这里将用于发布、查看和管理校园失物与招领信息。</p>

                        <?php if ($is_login): ?>
                            <div class="alert alert-success mb-4" role="alert">
                                当前已登录，身份：<?php echo htmlspecialchars($role); ?>
                            </div>
                            <a class="btn btn-primary" href="publish.php">发布信息</a>
                        <?php else: ?>
                            <div class="alert alert-warning mb-4" role="alert">
                                请先登录后再发布失物或招领信息。
                            </div>
                            <a class="btn btn-primary me-2" href="login.php">去登录</a>
                            <a class="btn btn-outline-primary" href="register.php">去注册</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
