# Request: LLM Summary 启用与验证

> Change Request 需求输入文档。由需求方/产品经理填写。

## CR-ID
CR-002

## 提出人
Kevin

## 提出日期
2026-06-21

## 来源
- PRD 锚点：`PRD-REAL-SCANNER-V1`
- 前置 CR：`CR-001 Real Scanner v1 真实诊断升级`
- 生产现状：Real Scanner API 已上线，`llm_summary.available=false`，规则版 fallback 已验证。

## 需求背景

CR-001 已完成 Real Scanner v1：后端真实抓取首页、解析信号、规则评分、findings 生成、fallback summary、lead snapshot 保存和生产部署。

当前生产验证中，`/wp-json/usegeo/v1/scan` 对 `https://example.com` 返回：

```json
{
  "ok": true,
  "total": 27,
  "title": "Example Domain",
  "findings": 4,
  "llm_available": false
}
```

这说明 fallback 正常，但 LLM 解释层尚未启用。为了让 Scanner 更像“真实诊断产品”，需要启用 LLM summary，并验证其输出受真实提取内容约束。

## 业务目标

启用 Real Scanner v1 的 LLM 解释层，使成功 scan 返回：

- `llm_summary.available = true`
- 自然语言 summary
- top recommendations
- audit recommendation

同时保持 CR-001 已确认的硬约束：

- LLM 不能覆盖规则评分；
- LLM 不能编造页面中不存在的客户、案例、竞品；
- LLM prompt 不能包含 lead 邮箱、表单字段、扫描者 IP、cookie、session；
- LLM 失败时必须继续 fallback，不阻断 scanner。

## 功能描述

本 CR 是配置/验证型 CR，不重做 Scanner 主逻辑。

范围：

1. 确认生产环境 LLM API key 注入方式。
2. 配置 `USEGEO_LLM_API_KEY` 或等价 WordPress option/filter。
3. 配置 OpenAI-compatible 代理地址 `USEGEO_LLM_BASE_URL=https://chat.aiprox.net`。
4. 默认模型使用 `gpt-5.4-mini`，需要更强模型时可改 `USEGEO_LLM_MODEL=gpt-5.5`。
3. 如有必要，配置 `USEGEO_LLM_MODEL` / `USEGEO_LLM_TIMEOUT`。
5. 验证 `POST /wp-json/usegeo/v1/scan` 在合法 URL 下返回 `llm_summary.available=true`。
6. 验证 LLM summary 只基于 extracted/scores/findings，不编造。
7. 验证 API key 不出现在前端源码、错误响应、邮件和日志输出中。
8. 验证无 key / 错 key / 超时时 fallback 仍可用。

## 非功能需求

- **安全**：API key 不入仓，不出现在浏览器、REST 响应、邮件正文或公开页面源码。
- **PII**：LLM prompt 不得包含 lead 邮箱、表单字段、扫描者 IP、cookie、session。
- **性能**：LLM 调用 timeout ≤ 8s；LLM 失败路径整体 ≤ 12s。
- **可回退**：移除/禁用 API key 后，scanner 仍返回规则版结果。
- **可观测性**：LLM 失败需要在服务器日志可定位，但不得记录 API key 原文。

## 约束条件

- 不修改 CR-001 的评分规则、API schema、SSRF 防护、lead 保存结构。
- 不改首页 UI，不新增前端设计。
- 不提交任何真实 secret。
- 生产配置需要 Kevin 在 WordPress/Hostinger 环境中手动填写或授权。

## 优先级

P1

## 验收标准（高层）

1. 生产环境配置 LLM key 后，对公开 URL scan 返回 `llm_summary.available=true`。
2. LLM summary 包含 summary、top recommendations、audit recommendation。
3. LLM summary 不包含 extracted 中不存在的具体客户/案例/竞品名。
4. 页面源码、scanner.js、REST 错误响应中不暴露 API key。
5. 移除/禁用 LLM key 后，scanner fallback 仍返回完整规则结果。

## 附件

- CR-001 acceptance spec：`DeliverHQ/change-requests/CR-001/acceptance-spec.md`
- CR-001 LLM 实现：`wordpress/theme/usegeo/inc/scanner-llm.php`
- PRD 锚点：`DeliverHQ/docs/PRD.md` 中的 `PRD-REAL-SCANNER-V1`
