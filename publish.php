<?php
require_once 'config/auth.php';

$username = $_SESSION['username'];
$success = isset($_GET['success']) && $_GET['success'] === '1';
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

                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">发布成功！</div>
                        <?php endif; ?>

                        <form action="api/publish_action.php" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">发布类型</label>
                                <select class="form-select" name="type" required>
                                    <option value="lost">失物</option>
                                    <option value="found">招领</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">标题</label>
                                <input type="text" class="form-control" id="title" name="title" maxlength="100" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">描述</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">地点</label>
                                <input type="text" class="form-control" id="location" name="location" maxlength="100" required>
                            </div>

                            <div class="mb-3">
                                <label for="item_time" class="form-label">时间</label>
                                <input type="datetime-local" class="form-control" id="item_time" name="item_time" max="<?php echo date('Y-m-d\TH:i'); ?>"  required>                            
                            </div>

                            <div class="mb-3">
                                <label for="contact" class="form-label">联系方式</label>
                                <input type="text" class="form-control" id="contact" name="contact" maxlength="50" required>
                            </div>

                            <div class="mb-4">
                                <label for="image" class="form-label">图片</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary">提交发布</button>
                            <a class="btn btn-outline-secondary ms-2" href="index.php">返回首页</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); 
        document.getElementById('item_time').value = now.toISOString().slice(0, 16);
</script>

</html>
