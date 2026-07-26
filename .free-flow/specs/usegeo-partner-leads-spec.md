# UseGEO Partner Leads 规格文档

## 1. 概述

Partner Lead 转介与售卖系统，把 UseGEO 的内容流量和 Scanner 诊断结果转化为可分级、可授权转介给 GEO/SEO 专家、工具商或 agency 的高质量 lead 池。

## 2. 实现状态

**状态**: ✅ 已完成

**涉及文件**:
- `wordpress/theme/usegeo/inc/forms.php` - 表单处理与 lead 保存
- `wordpress/theme/usegeo/inc/admin.php` - 后台管理与筛选

## 3. 功能规格

### 3.1 Lead 质量计算

```php
function usegeo_calculate_lead_quality($partner_consent, $help_intent, $support_level, $timeline, $scan_total_score)
```

**Hot Lead 条件**:
- partner_consent = 'yes'
- help_intent ∈ ['prioritized_next_steps', 'open_to_specialist']
- support_level ∈ ['small_project', 'ongoing_support']
- timeline ∈ ['next_quarter', 'need_help_soon']

**Cold Lead 条件**:
- partner_consent ≠ 'yes'
- help_intent = 'exploring_geo' 且 support_level = 'not_hiring'

**其他情况**: Warm Lead（scan_total_score < 70 时也是 Warm）

### 3.2 Partner 字段

**表单收集**:
- partner_consent: 'yes' | 'no' (默认)
- budget_range: 预算范围
- region: 地区

**后台管理**:
- partner_consent: Partner 授权状态
- partner_status: 处理状态 (new|reviewing|contacted|sold|rejected)
- partner_notes_log: Partner 备注历史
- lead_quality_auto: 自动计算的 lead 质量
- lead_quality_override: 手动覆盖的质量
- lead_quality: 最终生效的质量

### 3.3 Lead 质量级别

```php
$options = usegeo_get_lead_quality_labels();
// '' | 'hot' | 'warm' | 'cold'
```

### 3.4 Partner 状态

```php
$options = usegeo_get_partner_status_labels();
// 'new' | 'reviewing' | 'contacted' | 'sold' | 'rejected'
```

## 4. 后台管理

### 4.1 Lead 列表列

| 列名 | 说明 |
|------|------|
| Work Email | 邮箱 |
| Company Type | 公司类型 |
| Company Size | 公司规模 |
| Help Intent | 帮助意图（带颜色标签）|
| Support Level | 支持级别（带颜色标签）|
| Timeline | 时间线（带颜色标签）|
| Website Input | Scanner 输入的网站 |
| GEO Score | Scanner 分数 |
| Weakest Dimension | 最弱维度 |
| Partner Consent | Partner 授权 |
| Lead Quality | Lead 质量（支持覆盖）|
| Budget | 预算范围 |
| Region | 地区 |
| Partner Status | Partner 状态 |
| Notification | 邮件通知状态 |

### 4.2 筛选功能

支持按以下条件筛选:
- partner_consent: yes/no
- lead_quality: hot/warm/cold
- partner_status: new/reviewing/contacted/sold/rejected
- 日期范围: start_date / end_date

### 4.3 批量操作

支持批量修改 Partner Status:
- Mark as Reviewing
- Mark as Contacted
- Mark as Sold
- Mark as Rejected

### 4.4 CSV 导出

**入口**: Lead 列表页筛选后点击 "Export Partner Leads CSV"

**导出条件**:
- partner_consent = 'yes'
- lead_quality ∈ ['hot', 'warm']

**导出字段**:
lead_id, work_email, company_type, company_size, website_input, geo_score, weakest_dimension, partner_consent, lead_quality, lead_quality_auto, lead_quality_override, budget_range, region, help_intent, support_level, timeline, partner_status, submitted_at

## 5. 邮件通知

邮件包含完整 lead 信息:
- 联系信息
- 公司信息
- Scanner 数据（URL、分数、最弱维度、Top Finding）
- Partner 授权和质量评估
- 管理员编辑链接

## 6. 验收标准

- [x] Partner consent 明确授权字段
- [x] Budget/Region 等 qualification 字段
- [x] 后台 lead 列表显示所有 partner 相关列
- [x] Partner status 状态管理
- [x] CSV 导出（仅 consent=yes 的 lead）
- [x] Lead quality 自动计算和手动覆盖
- [x] Partner notes 历史记录
- [x] 批量状态更新
- [x] 筛选功能（consent/quality/status/日期）
