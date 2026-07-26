# UseGEO Community Case 规格文档

## 1. 概述

社区案例投稿与编辑审核系统，收集真实 GEO 执行案例。

## 2. 实现状态

**状态**: ✅ 已完成

**涉及文件**:
- `wordpress/theme/usegeo/inc/post-types.php` - 自定义内容类型
- `wordpress/theme/usegeo/inc/forms.php` - 表单处理
- `wordpress/theme/usegeo/inc/admin.php` - 后台管理
- `wordpress/theme/usegeo/template-parts/community/community-intro.php` - 前端表单
- `wordpress/theme/usegeo/template-parts/cases/case-preview.php` - 案例展示

## 3. 自定义内容类型

### 3.1 usegeo_case

**Post Type**: `usegeo_case`
**公开**: 否（仅后台管理）
**Show UI**: 是

**Meta 字段**:
| 字段 | 类型 | 说明 |
|------|------|------|
| contributor_name | text | 贡献者名称 |
| contact_email | email | 联系邮箱 |
| company_type | text | 公司类型 |
| industry | text | 行业 |
| business_stage | text | 发展阶段 |
| core_problem | textarea | 核心问题 |
| geo_actions | textarea | GEO 动作 |
| pages_updated | textarea | 更新的页面 |
| results_seen | textarea | 看到的结果 |
| lessons_learned | textarea | 学到的经验 |
| allow_anonymous | checkbox | 允许匿名 |
| review_status | select | 审核状态 |
| processing_lane | select | 处理通道 |
| editorial_notes | textarea | 编辑备注 |
| submitted_at | datetime | 提交时间 |
| source | text | 来源 |

### 3.2 审核状态

```php
$options = usegeo_get_case_review_status_options();
// 'pending_review' | 'reviewing' | 'approved' | 'needs_followup' | 'archived'
```

### 3.3 处理通道

```php
$options = usegeo_get_case_processing_lane_options();
// 'unassigned' | 'featured_pool' | 'article_pipeline' | 'followup_pool' | 'archive_only'
```

## 4. 前端表单

**位置**: 首页 #community 区块

**必填字段**:
- contact_email
- company_type
- core_problem
- geo_actions
- results_seen

**可选字段**:
- contributor_name
- industry
- business_stage
- pages_updated
- lessons_learned
- allow_anonymous

## 5. 后台管理

### 5.1 列表视图

显示列:
- Contact Email
- Company Type
- Industry
- Review Status
- Processing Lane
- Anonymous

### 5.2 详情视图

Meta Box 显示所有字段，支持:
- 修改 Review Status
- 修改 Processing Lane
- 添加 Editorial Notes

## 6. 公开显示

**案例预览**: 仅显示 `review_status = 'approved'` 的案例

```php
$args = array(
    'post_type' => 'usegeo_case',
    'meta_query' => array(
        array(
            'key' => 'review_status',
            'value' => 'approved'
        )
    )
);
```

## 7. 验收标准

- [x] 案例投稿表单完整实现
- [x] 后台案例列表和审核界面
- [x] Review Status 和 Processing Lane 管理
- [x] Editorial Notes 支持
- [x] 案例预览仅显示 approved 案例
- [x] Anonymous 选项支持
