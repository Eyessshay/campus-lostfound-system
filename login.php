<?php
// 用户登录页面
$registered = isset($_GET['registered']) && $_GET['registered'] === '1';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户登录 - 校园失物招领系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 text-center mb-2">用户登录</h1>
                        <p class="text-center text-muted mb-4">校园失物招领系统</p>

                        <?php if ($registered): ?>
                            <div class="alert alert-success" role="alert">注册成功，请登录。</div>
                        <?php endif; ?>

                        <?php if ($error === 'empty'): ?>
                            <div class="alert alert-danger" role="alert">用户名和密码不能为空。</div>
                        <?php elseif ($error === 'invalid'): ?>
                            <div class="alert alert-danger" role="alert">用户名或密码错误。</div>
                        <?php elseif ($error === 'login_required'): ?>
                            <div class="alert alert-warning" role="alert">请先登录后再访问该页面。</div>
                        <?php endif; ?>

                        <form action="api/login_action.php" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">用户名</label>
                                <input type="text" class="form-control" id="username" name="username" maxlength="50" required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">密码</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">登录</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="register.php" class="text-decoration-none">没有账号？去注册</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
