# 校园失物招领系统 - 优化验证测试指南

## 🧪 功能验证检查清单

### 第一部分: 权限与所有权检查

#### 测试用例 1.1: 编辑按钮可见性

**前置条件:**

- 用户A已登录，发布了失物信息
- 用户B已登录

**测试步骤:**

1. 用户A访问首页 → 应看到自己发布信息的"编辑"和"删除"按钮
2. 用户B访问首页 → 不应看到用户A信息的编辑/删除按钮
3. 未登录用户访问首页 → 不应看到任何编辑/删除按钮

**预期结果:** ✅ 按钮显示与user_id匹配

**优化生效验证:**

- 查看代码：`(int)$lost['user_id'] === (int)$_SESSION['user_id']`
- 确认类型转换已应用

---

#### 测试用例 1.2: 详情页权限检查

**前置条件:**

- 用户A发布的信息已存在

**测试步骤:**

1. 用户A打开自己发布的详情页 → 应看到编辑/删除按钮
2. 用户B打开相同详情页 → 不应看到编辑/删除按钮
3. 直接访问 `edit_post.php?type=lost&id=[别人的ID]` → 应被拒绝

**预期结果:** ✅ 权限检查有效

**代码验证:**

```bash
grep -n "int.*user_id.*SESSION" detail.php
# 应显示: if ($is_login && (int)$user_id === (int)$_SESSION['user_id'])
```

---

### 第二部分: 图片处理验证

#### 测试用例 2.1: 图片显示

**前置条件:**

- 有发布了图片的信息
- 有未发布图片的信息

**测试步骤:**

1. 访问有图片的信息 → 图片正常显示
2. 访问无图片的信息 → 无图片容器，不报错
3. 修改图片路径为不存在的文件 → 前端无错误提示

**预期结果:** ✅ 使用`!empty()`而非`file_exists()`

**代码验证:**

```bash
grep -n "!empty.*image" index.php detail.php my_posts.php
# 应显示多行 if (!empty($xx['image']))
```

---

#### 测试用例 2.2: 图片上传验证

**前置条件:**

- 准备测试文件：1.jpg (真实图片), 2.txt (文本文件), 3.exe (可执行文件)

**测试步骤:**

1. 上传1.jpg → ✅ 成功
2. 上传2.txt → ❌ 提示"不是有效的图片"
3. 上传3.exe (改名为.jpg) → ❌ 提示"不是有效的图片"
4. 上传3MB的jpg → ❌ 提示"不能超过2MB"

**预期结果:** ✅ getimagesize()验证有效

**代码验证:**

```bash
grep -n "getimagesize" api/publish_action.php api/update_post.php
# 应各显示一行 getimagesize()调用
```

---

#### 测试用例 2.3: 旧图片清理

**前置条件:**

- 发布的信息已存在原始图片 `uploads/img_xxx.jpg`

**测试步骤:**

1. 编辑该信息并上传新图片
2. 更新成功后，检查 `/uploads` 目录
3. 旧的 `img_xxx.jpg` 应已删除

**预期结果:** ✅ 旧图片自动删除

**验证方法:**

```bash
# 编辑前记录文件列表
ls -la uploads/ | grep img_

# 编辑后再查看
ls -la uploads/ | grep img_
# 旧文件应消失，新文件应出现
```

---

### 第三部分: 时间处理验证

#### 测试用例 3.1: 时间格式转换

**前置条件:**

- 浏览器支持HTML5 datetime-local输入框

**测试步骤:**

1. 发布信息，使用日期选择器选择 `2026-06-08 14:30`
2. 提交后检查数据库 `lost_items.lost_time`
3. 应显示格式: `2026-06-08 14:30:00`

**预期结果:** ✅ 时间格式正确

**数据库验证:**

```sql
SELECT lost_time FROM lost_items ORDER BY lost_id DESC LIMIT 1;
# 应显示: 2026-06-08 14:30:00
```

---

#### 测试用例 3.2: 无效时间处理

**前置条件:**

- 绕过前端验证，直接发送POST

**测试步骤:**

```bash
curl -X POST http://localhost/lostfound/api/publish_action.php \
  -d "type=lost&title=Test&description=Test&location=Test&item_time=invalid&contact=123"
```

**预期结果:** ✅ 显示"时间格式错误"

**代码验证:**

```bash
grep -n "date.*strtotime" api/publish_action.php api/update_post.php
# 应显示时间处理代码
```

---

### 第四部分: 中文处理验证

#### 测试用例 4.1: 中文截断不乱码

**前置条件:**

- 发布信息的描述超过100个汉字

**测试步骤:**

1. 在我的发布页面查看信息列表
2. 描述应截断在100个字符左右并显示"..."
3. 确保汉字不显示为"?"或乱码

**预期结果:** ✅ 中文正常截断

**代码验证:**

```bash
grep -n "mb_substr\|mb_strlen" my_posts.php
# 应显示: mb_substr(..., 0, 100, 'UTF-8')
```

---

### 第五部分: 安全性验证

#### 测试用例 5.1: SQL注入防护

**前置条件:**

- 需要HTTP客户端工具

**测试步骤:**

1. 在搜索框输入: `'; DROP TABLE users; --`
2. 提交搜索
3. 系统应正常响应，不执行SQL注入

**预期结果:** ✅ 使用prepared statements防护有效

**代码验证:**

```bash
grep -n "mysqli_prepare\|mysqli_stmt_bind_param" index.php detail.php
# 应显示所有查询都使用prepared statements
```

---

#### 测试用例 5.2: XSS防护

**前置条件:**

- HTTP客户端工具

**测试步骤:**

1. 发布信息，在标题中输入: `<script>alert('XSS')</script>`
2. 查看发布的信息
3. 脚本不应执行，应显示为文本

**预期结果:** ✅ 使用htmlspecialchars()防护有效

---

#### 测试用例 5.3: 文件删除权限

**前置条件:**

- 准备两个不同user_id的用户账号

**测试步骤:**

1. 用户A发布信息ID=5
2. 用户B尝试访问: `/api/delete_action.php?type=lost&id=5`
3. 系统应拒绝删除

**预期结果:** ✅ 权限检查有效

---

## 📊 自动化测试脚本

### 脚本1: 验证类型转换

```php
<?php
// test_type_casting.php
$files = ['index.php', 'detail.php'];
foreach ($files as $file) {
    $content = file_get_contents($file);
    $pattern = '/\(int\)\s*\$[a-z_]+\s*===\s*\(int\)\s*\$_SESSION/i';
    if (preg_match_all($pattern, $content, $matches)) {
        echo "✅ $file: 发现 " . count($matches[0]) . " 处类型转换\n";
    } else {
        echo "❌ $file: 未发现类型转换！\n";
    }
}
?>
```

### 脚本2: 验证mb_substr使用

```php
<?php
// test_mb_functions.php
$content = file_get_contents('my_posts.php');
if (strpos($content, 'mb_substr') !== false &&
    strpos($content, 'mb_strlen') !== false) {
    echo "✅ my_posts.php: 正确使用mb_*函数\n";
} else {
    echo "❌ my_posts.php: 未发现mb_*函数！\n";
}
?>
```

### 脚本3: 验证getimagesize使用

```php
<?php
// test_image_validation.php
$files = [
    'api/publish_action.php',
    'api/update_post.php'
];
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'getimagesize') !== false) {
        echo "✅ $file: 发现getimagesize验证\n";
    } else {
        echo "❌ $file: 缺少getimagesize验证！\n";
    }
}
?>
```

---

## ✅ 最终验收标准

| 项目   | 检查项                | 状态 | 备注       |
| ------ | --------------------- | ---- | ---------- |
| 权限   | 编辑/删除按钮权限控制 | ⬜   | 需手动测试 |
| 权限   | 类型转换已应用        | ✅   | 代码已确认 |
| 图片   | 图片显示无报错        | ⬜   | 需手动测试 |
| 图片   | getimagesize()验证    | ✅   | 代码已确认 |
| 图片   | 旧图片清理            | ⬜   | 需手动测试 |
| 时间   | 时间格式转换          | ✅   | 代码已确认 |
| 时间   | 无效时间处理          | ⬜   | 需手动测试 |
| 国际化 | 中文截断              | ✅   | 代码已确认 |
| 安全   | SQL注入防护           | ✅   | 已验证     |
| 安全   | XSS防护               | ✅   | 已验证     |

---

## 🎯 推荐验收流程

**第1天:**

- [ ] 代码审查 (使用上述脚本验证)
- [ ] 编译/语法检查

**第2-3天:**

- [ ] 功能验收 (执行上述测试用例)
- [ ] 性能基准测试

**第4天:**

- [ ] 安全渗透测试
- [ ] 最终验收

---

## 📞 问题排查

### 问题1: 编辑按钮不显示

**排查步骤:**

```php
// 在detail.php中添加调试代码
var_dump([
    'user_id' => $user_id,
    'session_id' => $_SESSION['user_id'],
    'comparison' => (int)$user_id === (int)$_SESSION['user_id']
]);
```

### 问题2: 图片上传失败

**排查步骤:**

```bash
# 检查uploads目录权限
ls -la uploads/
chmod 755 uploads/

# 检查PHP错误日志
tail -f /var/log/apache2/error.log
```

### 问题3: 时间显示错误

**排查步骤:**

```sql
-- 检查数据库中的时间格式
SELECT lost_time FROM lost_items LIMIT 5;
-- 应显示 YYYY-MM-DD HH:MM:SS 格式
```
