# Context Summary: CR-001

> 由 Context Agent 产出。滑动窗口机制：最多携带 2 个阶段全文，阶段切换时必须更新本摘要。

## Current Phase
context（准备进入 dev handoff）

## Completed Phases

### Spec (已完成)
- 核心功能：把首页 GEO Scanner 从前端模拟分数升级为基于真实首页内容的轻量诊断——后端抓取首页 HTML、解析信号、规则评分、可选 LLM 解释、降级保护、lead snapshot 保存。
- 关键验收条件：
  1. 结果必须基于真实网页内容（不再依赖 URL 字符串特征）
  2. LLM 不可用时仍返回完整规则版结果（不卡死）
  3. SSRF 黑名单不可被绕过（含重定向后命中）
  4. lead 提交时携带 scan snapshot（8 个 post meta + 邮件 5 行）
  5. feature flag 关闭时无错回退到模拟分数
- 已解决的模糊点：Q1 评分公式（规则加权）、Q2 LLM provider（OpenAI gpt-4o-mini）、Q3 rate limit（6/min + 12/5min）、Q4 flag 默认（生产 true / 本地 false）、Q5 后台徽章（v1 仅文本）

### Design (已完成)
- 设计方案：复用现有 scanner UI 框架，替换数据源；新增 evidence 区 + 错误状态区；后端新增 6 个模块文件（scanner-api / fetch / parse / score / llm / ratelimit）；REST route `POST /wp-json/usegeo/v1/scan`；feature flag 通过 wp_localize_script 传前端。
- 关键决策：
  - D1 wp_remote_get 抓取（零依赖）
  - D2 DOMDocument 解析（PHP 原生）
  - D3 Transient rate limit（不引入 Redis）
  - D5 REST route（不用 admin-post）
  - D7 旧模拟逻辑保留 30 天

## Key Decisions

| # | 决策 | 原因 | 影响范围 |
|---|---|---|---|
| 1 | 规则加权评分（非 ML） | 可解释、可审计、零依赖 | scanner-score.php |
| 2 | OpenAI gpt-4o-mini（filter 可切换） | 性价比高、成熟 | scanner-llm.php |
| 3 | WP Transient rate limit | 不引入 Redis | scanner-ratelimit.php |
| 4 | REST route 而非 admin-post | 原生 REST API、JSON 友好 | scanner-api.php |
| 5 | feature flag = 常量 + filter | 最简方案 | scanner.js + scanner-api.php |
| 6 | 旧模拟逻辑保留 30 天 | 回退安全 | scanner.js |

## Open Issues

| # | 问题 | 阻断级别 | 负责人 | 状态 |
|---|---|---|---|---|
| 1 | 评分公式权重等权，上线后需调优 | P2 | Kevin | open |
| 2 | LLM 只适配 OpenAI，后续可扩展 | P2 | Kevin | open |
| 3 | 后台 weakest_dimension 无视觉徽章 | P2 | Kevin | open |

## Key Files

| 文件 | 阶段 | 说明 |
|---|---|---|
| acceptance-spec.md | spec | 493 行 SDD 五段（Data/Interface/Behavior/NFR/依赖） |
| design/metadata.yml | design | ui_type: B端 |
| design/design-decisions.md | design | D1-D7 + Q1-Q5 裁决 |
| design/hi-fi-spec.md | design | 前端/后端/后台/邮件/feature flag 设计规格 |
| request.md | request | 需求输入（背景/目标/功能/非功能/约束/验收） |

## Loaded Context

> 当前阶段需要加载的文件及其用途。遵循滑动窗口：最多携带 2 个阶段全文 + 本摘要。

| 文件 | 阶段 | 加载方式 | 说明 |
|---|---|---|---|
| acceptance-spec.md | spec | 全文 | 493 行 SDD 五段，Dev 阶段的验收真相源 |
| design/hi-fi-spec.md | design | 全文 | 前端/后端/后台/邮件设计规格，Dev 阶段的实现蓝图 |
| design/design-decisions.md | design | 摘要 | D1-D7 + Q1-Q5 已折叠到上方 Key Decisions |
| request.md | request | 摘要 | 需求背景已折叠到上方 Spec 摘要 |
| context-summary.md | context | 全文 | 本文件 |

## Touch Points (代码触点)

| 文件 | 改动类型 |
|---|---|
| `wordpress/theme/usegeo/assets/js/scanner.js` | 修改：API 调用替换模拟逻辑 + evidence 区 + 错误状态 |
| `wordpress/theme/usegeo/template-parts/scanner/scanner.php` | 修改：新增 evidence 区 + 错误状态区 + lead 表单隐藏字段 |
| `wordpress/theme/usegeo/inc/forms.php` | 修改：lead 保存 scan snapshot post meta + 邮件追加 5 行 |
| `wordpress/theme/usegeo/inc/admin.php` | 修改：lead 列表新增 2 列 |
| `wordpress/theme/usegeo/inc/scanner-api.php` | 新增：REST route 编排 |
| `wordpress/theme/usegeo/inc/scanner-fetch.php` | 新增：URL 校验 + SSRF + 抓取 |
| `wordpress/theme/usegeo/inc/scanner-parse.php` | 新增：HTML 解析 |
| `wordpress/theme/usegeo/inc/scanner-score.php` | 新增：规则评分 |
| `wordpress/theme/usegeo/inc/scanner-llm.php` | 新增：LLM 调用 + 降级 |
| `wordpress/theme/usegeo/inc/scanner-ratelimit.php` | 新增：Transient 限流 |
