# UseGEO Sponsor Inventory 规格文档

## 1. 概述

手动赞助位与广告库存系统，允许在指定位置展示赞助内容。

## 2. 实现状态

**状态**: ✅ 已完成

**涉及文件**:
- `wordpress/theme/usegeo/inc/admin.php` - 后台管理界面
- `wordpress/theme/usegeo/front-page.php` - 首页调用
- `wordpress/theme/usegeo/template-parts/scanner/scanner.php` - Scanner 结果页调用
- `wordpress/theme/usegeo/single.php` - 文章页调用

## 3. 功能规格

### 3.1 后台管理

**入口**: WordPress 后台 → Appearance → UseGEO Sponsors

**可配置字段**:
- Active: 是否启用
- Title: 赞助标题
- Description: 赞助描述
- CTA Label: 按钮文字
- URL: 赞助链接
- Start Date: 开始日期（可选）
- End Date: 结束日期（可选）

### 3.2 赞助位置

| Placement | 位置 | 调用函数 |
|------------|------|----------|
| homepage | 首页 | `usegeo_render_sponsor_slot('homepage')` |
| scanner_result | Scanner 结果页 | `usegeo_render_sponsor_slot('scanner_result')` |
| article_sidebar | 文章侧边栏 | `usegeo_render_sponsor_slot('article_sidebar')` |

### 3.3 安全要求

- 所有外链使用 `esc_url()`
- 外链标注 `rel="noopener sponsored nofollow"`
- 支持日期窗口控制显示/隐藏

## 4. API

### 4.1 获取赞助位配置

```php
$sponsor_slots = usegeo_get_sponsor_slots();
// 返回: array[placement => slot_config]
```

### 4.2 渲染赞助卡片

```php
usegeo_render_sponsor_slot('homepage');
```

## 5. 验收标准

- [x] 后台可配置三个位置的赞助内容
- [x] 支持日期窗口控制
- [x] 外链安全标注 sponsored
- [x] Active=false 时不显示
- [x] 前台三个位置正确渲染
