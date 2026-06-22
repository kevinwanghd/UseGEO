# Request: Commercial Conversion Layer

## CR-ID
CR-023

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
竞品调研显示，GEO/AI visibility 商业化页面普遍不是单纯内容页，而是清晰的“免费诊断 → 样例报告 → 明确下一步 → partner/sponsor 承接”路径。UseGEO 当前已有内容、scanner、partner lead、sponsor 和后台运营能力，但前台缺少商业承接页。

## 业务目标
新增最小商业转化层，让用户和 partner 都能理解 UseGEO 的商业路径：学习 GEO、运行免费 scan、查看样例报告、明确 consent 后可获得 partner follow-up；partner/sponsor 能理解 lead 定义和 sponsor 库存。

## 功能描述
- 新增主题路由 `/ai-visibility-audit/`，作为免费 scan + partner match 商业落地页。
- 新增主题路由 `/sample-report/`，展示示例 scan report / partner handoff evidence。
- 新增主题路由 `/partners/`，展示 partner lead definition、sponsor inventory、beta offer。
- 首页 scanner 后新增 “From free scan to the right next step” 商业闭环模块。
- 不新增真实支付、不发 outreach、不自动转发 lead。

## 非功能需求
- 不改变现有 URL/content/form fields。
- 不承诺 AI ranking，不承诺 UseGEO 自营完整服务。
- 所有页面需保留 scanner CTA 和 consent 说明。
- 新页面由 theme render，部署后无需 WP admin 创建页面也能访问。

## 约束条件
- 生产上传/缓存清理仍需 Kevin 确认。
- `/geo-audit/` 已被现有文章占用，不使用该 slug。
- 所有 DeliverHQ 产物必须在 `DeliverHQ/` 内。

## 优先级
P0

## 验收标准（高层）
- `/ai-visibility-audit/`、`/sample-report/`、`/partners/` 均返回 200。
- 三个页面均包含 scanner CTA、商业路径说明、no fake ranking/consent 保护语义。
- `/partners/` 包含 lead definition 和 sponsor inventory 三个 placement。
- 首页包含商业闭环模块。
- 本地 verifier PASS；生产上传后 production verifier PASS。
