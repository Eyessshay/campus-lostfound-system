# 校园失物招领系统接口说明

本文档用于前端页面和后端 PHP 接口对接。

## 1. 注册接口

提交地址：

```text
api/register_action.php
```

提交方式：

```text
POST
```

表单字段：

| 字段名 | 含义 | 是否必填 |
| --- | --- | --- |
| username | 用户名 | 是 |
| password | 密码 | 是 |
| confirm_password | 确认密码 | 是 |
| phone | 手机号 | 是 |

注意：

- 前端可以修改页面样式。
- 不要修改表单的 `name`。

## 2. 登录接口

提交地址：

```text
api/login_action.php
```

提交方式：

```text
POST
```

表单字段：

| 字段名 | 含义 | 是否必填 |
| --- | --- | --- |
| username | 用户名 | 是 |
| password | 密码 | 是 |

## 3. 发布接口

提交地址：

```text
api/publish_action.php
```

提交方式：

```text
POST
```

表单必须包含：

```html
enctype="multipart/form-data"
```

表单字段：

| 字段名 | 含义 | 是否必填 |
| --- | --- | --- |
| type | 发布类型，`lost` 表示失物，`found` 表示招领 | 是 |
| title | 标题 | 是 |
| description | 描述 | 是 |
| location | 地点 | 是 |
| item_time | 时间 | 是 |
| contact | 联系方式 | 是 |
| image | 图片 | 否 |

## 4. 退出登录

链接地址：

```text
api/logout.php
```

说明：

- 点击后清除登录状态。
- 页面会跳转回登录页。

## 5. 前端对接注意事项

前端可以修改：

- 页面布局
- 颜色
- 字体
- Bootstrap class
- CSS 文件

前端不要随意修改：

- `form action`
- `method="post"`
- 表单字段的 `name`
- 发布表单的 `enctype="multipart/form-data"`

如果必须修改字段名，需要同步通知后端修改 PHP 接收代码。
