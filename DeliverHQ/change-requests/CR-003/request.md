# Request: Partner Lead Monetization Foundation

## CR-ID
CR-003

## 提出人
Kevin

## 提出日期
2026-06-21

## 来源
- PRD 锚点：`PRD-PARTNER-LEADS`
- 商业定位：UseGEO 通过 GEO 学习内容获取流量，用 Scanner 进行 lead qualification，通过广告、赞助、affiliate、partner referral / lead resale 变现；不由 Kevin 亲自交付完整 GEO 服务。

## 需求背景

CR-001/CR-002 已完成真实 Scanner + LLM summary。当前 lead 表单更偏“请求 Full Diagnostic / 自营服务跟进”，但 UseGEO 的真实变现目标是沉淀可分级、可授权转介的 partner lead。

如果要售卖/转介 lead，必须补齐两个基础能力：

1. 明确 consent：用户是否同意 UseGEO 将提交信息分享给相关 GEO/SEO 合作伙伴。
2. qualification：后台能判断 lead 是否 hot/warm/cold、是否可转介、预算/地区/时间线如何。

## 业务目标

- 把 Scanner 产生的普通 lead 升级为“可运营/可转介”的结构化 lead。
- 不直接对接外部 partner，不自动发送用户信息；只先在后台沉淀字段和质量判断。
- 避免无 consent 的用户被误转介或误售卖。

## 功能描述

### 前端表单

在 Scanner 的 Full Diagnostic / Expert Follow-up 表单中新增：

- partner_consent checkbox：用户明确授权 UseGEO 可将提交信息分享给相关 GEO/SEO 合作伙伴。
- budget_range select：预算区间。
- region select：地区/时区。

表单文案从“UseGEO 自己提供完整服务”的暗示，调整为“UseGEO 可帮助匹配相关 GEO/SEO partner”。

### 后端保存

`usegeo_handle_lead_capture` 保存：

- partner_consent = yes/no
- budget_range
- region
- lead_quality = hot/warm/cold

### lead_quality 规则

最小规则：

- hot：partner_consent=yes 且 help_intent ∈ {prioritized_next_steps, open_to_specialist} 且 support_level ∈ {small_project, ongoing_support} 且 timeline ∈ {next_quarter, need_help_soon}
- warm：partner_consent=yes 且有 business email，或 score < 70
- cold：partner_consent=no 或 just_researching / not_hiring

### 后台展示

Lead 列表新增：

- Partner Consent
- Lead Quality
- Budget
- Region

邮件通知增加：

- Partner Consent
- Lead Quality
- Budget
- Region

## 非功能需求

- 合规：未勾选 partner_consent 的 lead 不得被标记为可转介。
- 隐私：表单必须清楚说明用户授权后，UseGEO 才可能将信息分享给相关合作伙伴。
- 安全：不放宽现有 nonce / 同源 / email 校验。
- 范围：本 CR 不实际对接外部 partner、不发送 webhook、不做 marketplace。只做字段、保存、展示、质量判断。

## 约束条件

- 不修改 Scanner API。
- 不修改 LLM prompt。
- 不改已上线的 score/evidence 主流程。
- 不提交任何 partner 名单或外部系统 key。

## 优先级

P0

## 验收标准（高层）

1. 表单出现 partner consent checkbox、budget、region。
2. 未勾选 consent 时，lead 保存 partner_consent=no，lead_quality=cold。
3. 勾选 consent 且满足高意向规则时，lead_quality=hot。
4. 后台 lead 列表能看到 Partner Consent / Lead Quality / Budget / Region。
5. 邮件正文包含 Partner Consent / Lead Quality / Budget / Region。
6. 原有 lead 提交、scan snapshot、邮件通知、后台 score 列不被破坏。

## 附件

- PRD：`DeliverHQ/docs/PRD.md` 中 `PRD-PARTNER-LEADS`
- 相关实现：`wordpress/theme/usegeo/template-parts/scanner/scanner.php`、`inc/forms.php`、`inc/admin.php`
