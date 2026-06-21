# Context Summary: CR-002

> 配置/验证型 CR。目标：启用并验证 Real Scanner v1 的 LLM Summary。

## Current Phase
context（准备进入 pre-dev 检查）

## Completed Phases

### Request
- 生产现状：Real Scanner API 已上线，`llm_summary.available=false`，fallback 正常。
- 目标：配置 LLM key，使 `llm_summary.available=true`，并验证 secret 不泄漏、LLM 不编造、fallback 仍可用。

### Spec
- PRD 锚点：`PRD-REAL-SCANNER-V1`
- hash：`65ad7b96a4d4`
- 场景：有效 key 返回 LLM summary；LLM 输出受 extracted 约束；无 key/错 key/超时 fallback；secret 不泄漏。

### Design
- ui_type: 无UI
- 决策：不改 UI；不提交 secret；复用 CR-001 OpenAI 适配；prompt 不含 PII。

## Key Decisions

| # | 决策 | 说明 |
|---|---|---|
| 1 | API key 不入仓 | 只能由 Kevin 在生产配置中注入 |
| 2 | 不改 UI | 复用 CR-001 已上线界面 |
| 3 | 复用 OpenAI 适配 | 代码已存在 |
| 4 | fallback 不阻断 | key 缺失/错误/超时仍返回规则结果 |

## Loaded Context

| 文件 | 加载方式 | 用途 |
|---|---|---|
| request.md | 全文 | 需求输入 |
| acceptance-spec.md | 全文 | 验收规格 |
| design/design-decisions.md | 全文 | 配置决策 |
| human-decisions.md | 全文 | key 配置阻断项 |

## Open Issues

| # | 问题 | 阻断级别 | 状态 |
|---|---|---|---|
| 1 | 生产 LLM API key 尚未配置 | P0 | pending |
