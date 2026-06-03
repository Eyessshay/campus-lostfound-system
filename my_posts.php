<?php
require_once 'config/auth.php';
require_once 'config/db.php';

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 获取消息
$delete_success = isset($_GET['delete']) && $_GET['delete'] === 'success';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// 错误消息映射
$error_messages = [
    'invalid_params' => '无效的参数',
    'invalid_id' => '无效的ID',
    'not_found' => '记录不存在或您无权删除',
    'delete_failed' => '删除失败，请稍后重试'
];

// 获取失物信息
$lost_sql = "SELECT lost_id, title, description, lost_location, lost_time, contact, status, image, created_at 
             FROM lost_items 
             WHERE user_id = ? 
             ORDER BY created_at DESC";

$lost_stmt = mysqli_prepare($conn, $lost_sql);
mysqli_stmt_bind_param($lost_stmt, "i", $user_id);
mysqli_stmt_execute($lost_stmt);
$lost_result = mysqli_stmt_get_result($lost_stmt);
$lost_items = mysqli_fetch_all($lost_result, MYSQLI_ASSOC);
mysqli_stmt_close($lost_stmt);

// 获取招领信息
$found_sql = "SELECT found_id, title, description, found_location, found_time, contact, status, image, created_at 
              FROM found_items 
              WHERE user_id = ? 
              ORDER BY created_at DESC";

$found_stmt = mysqli_prepare($conn, $found_sql);
mysqli_stmt_bind_param($found_stmt, "i", $user_id);
mysqli_stmt_execute($found_stmt);
$found_result = mysqli_stmt_get_result($found_stmt);
$found_items = mysqli_fetch_all($found_result, MYSQLI_ASSOC);
mysqli_stmt_close($found_stmt);

// 状态转换函数
function getStatusBadge($status, $type) {
    $statusMap = [
        'lost' => [
            'pending' => ['未找回', 'warning'],
            'found' => ['已找回', 'success']
        ],
        'found' => [
            'unclaimed' => ['未认领', 'warning'],
            'claimed' => ['已认领', 'success']
        ]
    ];
    
    $display = $statusMap[$type][$status][0] ?? $status;
    $color = $statusMap[$type][$status][1] ?? 'secondary';
    return $display;
}

function getStatusColor($status, $type) {
    $statusMap = [
        'lost' => [
            'pending' => 'warning',
            'found' => 'success'
        ],
        'found' => [
            'unclaimed' => 'warning',
            'claimed' => 'success'
        ]
    ];
    
    return $statusMap[$type][$status] ?? 'secondary';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的发布信息 - 校园失物招领系统</title>
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
            <div class="col-12 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">我的发布信息</h1>
                    <a href="publish.php" class="btn btn-primary">+ 发布新信息</a>
                </div>

                <?php if ($delete_success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        删除成功！
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error && isset($error_messages[$error])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>错误！</strong><?php echo htmlspecialchars($error_messages[$error]); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- 失物信息部分 -->
                <div class="mb-5">
                    <h2 class="h4 mb-3">我发布的失物</h2>
                    <?php if (count($lost_items) > 0): ?>
                        <div class="row g-3">
                            <?php foreach ($lost_items as $item): ?>
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="row g-0">
                                            <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                                <div class="col-md-3">
                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                         class="img-fluid rounded-start" 
                                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                         style="height: 200px; object-fit: cover;">
                                                </div>
                                                <div class="col-md-9">
                                            <?php else: ?>
                                                <div class="col-12">
                                            <?php endif; ?>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <h5 class="card-title mb-1">
                                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                                </h5>
                                                                <span class="badge bg-<?php echo getStatusColor($item['status'], 'lost'); ?>">
                                                                    <?php echo getStatusBadge($item['status'], 'lost'); ?>
                                                                </span>
                                                            </div>
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($item['created_at']))); ?>
                                                            </small>
                                                        </div>

                                                        <p class="card-text text-muted">
                                                            <?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>
                                                            <?php if (strlen($item['description']) > 100): ?>...<?php endif; ?>
                                                        </p>

                                                        <div class="row text-sm mb-3">
                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>丢失地点：</strong>
                                                                    <?php echo htmlspecialchars($item['lost_location']); ?>
                                                                </p>
                                                                <p class="mb-1">
                                                                    <strong>丢失时间：</strong>
                                                                    <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($item['lost_time']))); ?>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>联系方式：</strong>
                                                                    <?php echo htmlspecialchars($item['contact']); ?>
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2">
                                                            <a href="detail.php?type=lost&id=<?php echo $item['lost_id']; ?>" 
                                                               class="btn btn-sm btn-outline-primary">查看详情</a>
                                                                                <a href="edit_post.php?type=lost&id=<?php echo $item['lost_id']; ?>" 
                                                               class="btn btn-sm btn-outline-secondary">编辑</a>
                                                            <a href="api/delete_action.php?type=lost&id=<?php echo $item['lost_id']; ?>" 
                                                               class="btn btn-sm btn-outline-danger"
                                                               onclick="return confirm('确定删除此信息吗？');">删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            您还没有发布任何失物信息，<a href="publish.php">立即发布</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- 招领信息部分 -->
                <div class="mb-5">
                    <h2 class="h4 mb-3">我发布的招领</h2>
                    <?php if (count($found_items) > 0): ?>
                        <div class="row g-3">
                            <?php foreach ($found_items as $item): ?>
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="row g-0">
                                            <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                                <div class="col-md-3">
                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                                         class="img-fluid rounded-start" 
                                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                         style="height: 200px; object-fit: cover;">
                                                </div>
                                                <div class="col-md-9">
                                            <?php else: ?>
                                                <div class="col-12">
                                            <?php endif; ?>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <h5 class="card-title mb-1">
                                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                                </h5>
                                                                <span class="badge bg-<?php echo getStatusColor($item['status'], 'found'); ?>">
                                                                    <?php echo getStatusBadge($item['status'], 'found'); ?>
                                                                </span>
                                                            </div>
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($item['created_at']))); ?>
                                                            </small>
                                                        </div>

                                                        <p class="card-text text-muted">
                                                            <?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>
                                                            <?php if (strlen($item['description']) > 100): ?>...<?php endif; ?>
                                                        </p>

                                                        <div class="row text-sm mb-3">
                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>发现地点：</strong>
                                                                    <?php echo htmlspecialchars($item['found_location']); ?>
                                                                </p>
                                                                <p class="mb-1">
                                                                    <strong>发现时间：</strong>
                                                                    <?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($item['found_time']))); ?>
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1">
                                                                    <strong>联系方式：</strong>
                                                                    <?php echo htmlspecialchars($item['contact']); ?>
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2">
                                                            <a href="detail.php?type=found&id=<?php echo $item['found_id']; ?>" 
                                                               class="btn btn-sm btn-outline-primary">查看详情</a>
                                                                                <a href="edit_post.php?type=found&id=<?php echo $item['found_id']; ?>" 
                                                               class="btn btn-sm btn-outline-secondary">编辑</a>
                                                            <a href="api/delete_action.php?type=found&id=<?php echo $item['found_id']; ?>" 
                                                               class="btn btn-sm btn-outline-danger"
                                                               onclick="return confirm('确定删除此信息吗？');">删除</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="alert">
                            您还没有发布任何招领信息，<a href="publish.php">立即发布</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-outline-secondary">返回首页</a>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
