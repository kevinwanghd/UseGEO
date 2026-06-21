# Request: Partner Lead Export / Pipeline

## CR-ID
CR-004

## 提出人
Kevin

## 提出日期
2026-06-21

## 来源
- PRD 锚点：`PRD-PARTNER-LEADS`
- 前置 CR：CR-003 Partner Lead Monetization Foundation

## 需求背景

CR-003 已经能收集 partner_consent、budget、region、lead_quality，但后台还只能查看 lead，不能高效运营可转介 lead。要把 lead 变成可售/可转介资产，需要最小运营管线：筛选、导出、标记状态、记录备注。

## 业务目标

- 运营可以筛选 consent=yes 的 hot/warm lead。
- 运营可以导出 CSV，手动发给合作伙伴或用于销售沟通。
- 运营可以标记 lead 的 partner_status，避免重复联系或重复售卖。
- 不能导出未授权 lead。

## 功能描述

### 后台列 / 筛选

在 usegeo_lead 后台增加：

- partner_status 列
- partner_notes 简短备注列（详情页显示）
- 筛选：partner_consent=yes/no、lead_quality、partner_status

### 详情页编辑

Lead meta box 中允许编辑：

- partner_status：new / reviewing / contacted / sold / rejected
- partner_notes：textarea

### CSV 导出

新增后台动作：导出 partner leads CSV。

导出范围：

- 只导出 partner_consent=yes
- 默认导出 lead_quality ∈ {hot, warm}
- 不导出 partner_consent=no

CSV 字段：

- lead_id
- work_email
- company_type
- company_size
- website_input
- geo_score
- weakest_dimension
- partner_consent
- lead_quality
- budget_range
- region
- help_intent
- support_level
- timeline
- partner_status
- submitted_at

## 非功能需求

- 导出必须有管理员权限，不能公开访问。
- 导出必须带 nonce。
- CSV 不包含 scan_findings_summary / llm_summary 全文，避免泄漏过多上下文。
- 不自动发送给外部 partner。

## 约束条件

- 不改前端表单。
- 不改 Scanner API。
- 不改 lead_quality 规则。
- 不引入外部 CRM。

## 优先级

P0

## 验收标准（高层）

1. 后台可以按 partner_consent、lead_quality、partner_status 筛选 lead。
2. lead 详情页可以设置 partner_status 和 partner_notes。
3. CSV 导出只包含 consent=yes 的 hot/warm lead。
4. 导出 URL 未带 nonce 或非管理员访问时必须拒绝。
5. 导出 CSV 字段完整且不包含 scan_findings_summary / llm_summary 全文。
