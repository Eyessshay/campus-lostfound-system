<?php
// 用户注册页面
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>用户注册 - 校园失物招领系统</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-12 col-md-8 col-lg-6 col-xl-5">
				<div class="card shadow-sm border-0">
					<div class="card-body p-4 p-md-5">
						<h1 class="h3 text-center mb-2">用户注册</h1>
						<p class="text-center text-muted mb-4">校园失物招领系统</p>

						<form action="api/register_action.php" method="post">
							<div class="mb-3">
								<label for="username" class="form-label">用户名</label>
								<input type="text" class="form-control" id="username" name="username" required>
							</div>

							<div class="mb-3">
								<label for="password" class="form-label">密码</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>

							<div class="mb-3">
								<label for="confirm_password" class="form-label">确认密码</label>
								<input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
							</div>

							<div class="mb-4">
                 <label for="phone" class="form-label">手机号</label>
                 <input type="tel" class="form-control" id="phone" name="phone" maxlength="11" required>
              </div>

							<button type="submit" class="btn btn-primary w-100">注册</button>
						</form>

						<div class="text-center mt-3">
							<a href="login.php" class="text-decoration-none">已有账号？去登录</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
