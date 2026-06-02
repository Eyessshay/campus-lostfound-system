<?php
require_once 'config/auth.php';

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发布信息 - 校园失物招领系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="index.php">校园失物招领系统</a>
            <div class="ms-auto d-flex align-items-center gap-2">
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
                        <h1 class="h3 mb-3">发布信息</h1>
                        <p class="text-muted mb-4">你已经登录，可以继续开发失物或招领信息发布表单。</p>
                        <a class="btn btn-outline-primary" href="index.php">返回首页</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
