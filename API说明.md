# 校园失物招领系统 API 对接说明

本文档用于前端与后端 PHP 接口对接。

---

# 一、注册接口

接口地址：

```text
api/register_action.php
```

请求方式：

```text
POST
```

字段说明：

| 字段名           | 类型   | 必填 | 说明     |
| ---------------- | ------ | ---- | -------- |
| username         | string | 是   | 用户名   |
| password         | string | 是   | 密码     |
| confirm_password | string | 是   | 确认密码 |
| phone            | string | 是   | 联系电话 |

---

# 二、登录接口

接口地址：

```text
api/login_action.php
```

请求方式：

```text
POST
```

字段说明：

| 字段名   | 类型   | 必填 | 说明   |
| -------- | ------ | ---- | ------ |
| username | string | 是   | 用户名 |
| password | string | 是   | 密码   |

---

# 三、退出登录

接口地址：

```text
api/logout.php
```

请求方式：

```text
GET
```

说明：

- 清除 Session
- 返回登录页面

---

# 四、发布信息接口

接口地址：

```text
api/publish_action.php
```

请求方式：

```text
POST
```

表单必须包含：

```html
enctype="multipart/form-data"
```

字段说明：

| 字段名      | 类型           | 必填 | 说明                    |
| ----------- | -------------- | ---- | ----------------------- |
| type        | string         | 是   | 发布类型：lost 或 found |
| title       | string         | 是   | 标题                    |
| description | string         | 是   | 描述                    |
| location    | string         | 是   | 地点                    |
| item_time   | datetime-local | 是   | 时间                    |
| contact     | string         | 是   | 联系方式                |
| image       | file           | 否   | 图片                    |

图片上传规则：

- 支持 jpg
- 支持 jpeg
- 支持 png
- 支持 gif
- 最大 2MB

发布成功后：

```text
publish.php?success=1
```

---

# 五、编辑信息接口

接口地址：

```text
api/update_post.php
```

请求方式：

```text
POST
```

表单必须包含：

```html
enctype="multipart/form-data"
```

字段说明：

| 字段名      | 类型           | 必填 | 说明          |
| ----------- | -------------- | ---- | ------------- |
| type        | string         | 是   | lost 或 found |
| id          | int            | 是   | 信息ID        |
| title       | string         | 是   | 标题          |
| description | string         | 是   | 描述          |
| location    | string         | 是   | 地点          |
| item_time   | datetime-local | 是   | 时间          |
| contact     | string         | 是   | 联系方式      |
| image       | file           | 否   | 图片          |

图片规则：

- jpg
- jpeg
- png
- gif
- 最大2MB

编辑成功后：

```text
my_posts.php?update=success
```

---

# 六、修改状态接口

接口地址：

```text
api/update_status.php
```

请求方式：

```text
GET
```

参数：

| 参数名 | 说明          |
| ------ | ------------- |
| type   | lost 或 found |
| id     | 信息ID        |

状态规则：

失物：

```text
pending → found
```

显示：

```text
未找回 → 已找回
```

招领：

```text
unclaimed → claimed
```

显示：

```text
未认领 → 已认领
```

成功后：

```text
my_posts.php?status=success
```

---

# 七、删除信息接口

接口地址：

```text
api/delete_action.php
```

请求方式：

```text
GET
```

参数：

| 参数名 | 说明          |
| ------ | ------------- |
| type   | lost 或 found |
| id     | 信息ID        |

删除成功：

```text
my_posts.php?delete=success
```

失败：

```text
my_posts.php?error=delete_failed
```

---

# 八、页面参数说明

## 详情页

页面：

```text
detail.php
```

参数：

```text
detail.php?type=lost&id=1
```

或者：

```text
detail.php?type=found&id=1
```

---

## 编辑页

页面：

```text
edit_post.php
```

参数：

```text
edit_post.php?type=lost&id=1
```

或者：

```text
edit_post.php?type=found&id=1
```

---

# 九、前端可修改内容

允许修改：

- 页面布局
- CSS样式
- Bootstrap样式
- 字体
- 颜色
- 图标
- 卡片布局
- 导航栏样式

---

# 十、前端禁止修改内容

以下内容修改后会导致后端失效：

不要修改：

```html
form action
```

不要修改：

```html
method="post"
```

不要修改：

```html
enctype="multipart/form-data"
```

不要修改以下字段名：

```text
type
title
description
location
item_time
contact
image
username
password
confirm_password
phone
id
```

如需修改字段名，必须同步通知后端修改 PHP 接收代码。

---

# 十一、数据库状态值说明

失物状态：

```text
pending
found
```

招领状态：

```text
unclaimed
claimed
```

前端显示时应转换为：

| 数据库存储值 | 页面显示 |
| ------------ | -------- |
| pending      | 未找回   |
| found        | 已找回   |
| unclaimed    | 未认领   |
| claimed      | 已认领   |
|              |          |

```

```
