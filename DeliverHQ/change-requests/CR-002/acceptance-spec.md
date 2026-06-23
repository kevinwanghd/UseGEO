# Acceptance Spec: LLM Summary 启用与验证

> 本 CR 是配置/验证型 CR。目标是在不改动 Real Scanner 主逻辑的前提下，启用并验证 LLM summary。

## CR-ID
CR-002

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-REAL-SCANNER-V1
  prd_hash: bb44da62253e
```

---

## 1. Data Spec（数据规格）

### Entities

| 实体名 | 说明 | 关键字段 |
|---|---|---|
| LLMConfig | 生产 LLM 配置 | provider / model / api_key / timeout |
| LLMSummary | scanner 返回的 LLM 解释 | available / summary / top_recommendations / audit_recommendation |
| ScanResult | CR-001 已定义的 scan 结果 | scores / extracted / findings / llm_summary |

### Fields & Constraints

| 字段 | 类型 | 约束 | 说明 |
|---|---|---|---|
| LLMConfig.provider | string | optional, 默认 openai | v1 只要求 OpenAI 或等价 provider |
| LLMConfig.model | string | optional, 默认 gpt-4o-mini | 可通过 filter/常量调整 |
| LLMConfig.api_key | secret string | required for available=true，不入仓 | 只能在生产配置中注入 |
| LLMConfig.base_url | string | required for proxy, 默认 `https://api.openai.com/v1` | OpenAI-compatible API base URL；本 CR 使用 `https://chat.aiprox.net` |
| LLMConfig.timeout | int | 3–8 秒 | 超时后 fallback |
| LLMSummary.available | bool | required | true 表示 LLM 成功 |
| LLMSummary.summary | string | available=true 时 required | 诊断摘要 |
| LLMSummary.top_recommendations | string[] | available=true 时 required，1–5 条 | 建议动作 |
| LLMSummary.audit_recommendation | object | available=true 时 required | 是否建议 Manual GEO Audit |

### Data Invariants

1. `LLMSummary.available=true` 时，`summary` 和 `top_recommendations` 必须非空。
2. `LLMSummary.available=false` 时，scanner 仍必须返回 scores / extracted / findings。
3. `api_key` 不得出现在 Git 仓库、前端源码、REST 响应、邮件正文或公开 HTML 中。
4. LLM prompt 输入仅允许包含目标 URL、scores、extracted、findings，不包含 lead 邮箱、表单字段、IP、cookie、session。

---

## 2. Interface Spec（接口规格）

### 配置接口

| 配置项 | 形式 | 默认 | 说明 |
|---|---|---|---|
| USEGEO_LLM_API_KEY | WP 常量 / option / filter | 空 | 生产 secret，不入仓 |
| USEGEO_LLM_BASE_URL | WP 常量 / filter | `https://api.openai.com/v1` | OpenAI-compatible base URL，本项目代理为 `https://chat.aiprox.net` |
| USEGEO_LLM_MODEL | WP 常量 / filter | `gpt-5.4-mini` | 模型名；可切 `gpt-5.5` |
| USEGEO_LLM_TIMEOUT | filter | 8 | 秒 |
| USEGEO_LLM_PROVIDER | option / filter | openai | v1 默认 OpenAI |

### Scanner API 期望

`POST /wp-json/usegeo/v1/scan` 在合法 URL 下：

- 有有效 LLM key：HTTP 200 + `ok=true` + `llm_summary.available=true`。
- 无 key / 错 key / LLM 超时：HTTP 200 + `ok=true` + `llm_summary.available=false`。
- API key 不在响应体中出现。

---

## 3. Behavior Spec（行为规格）

### 场景 A：配置有效 LLM key 后返回 LLM summary

- Given：生产环境配置有效 `USEGEO_LLM_API_KEY`。
- When：调用 `POST /wp-json/usegeo/v1/scan` 扫描 `https://example.com` 或其它公开页面。
- Then：返回 HTTP 200，`ok=true`，`llm_summary.available=true`，`summary` 非空，`top_recommendations` 至少 1 条。

### 场景 B：LLM 输出受 extracted 约束

- Given：扫描结果中的 `extracted.title` 为 `Example Domain`，无客户/案例/竞品信号。
- When：LLM summary 生成。
- Then：summary/findings/recommendations 不得出现 extracted 中不存在的具体客户名、案例名或竞品名。

### 场景 C：无 key 时 fallback 不阻断

- Given：生产环境移除或禁用 LLM key。
- When：调用 scanner API。
- Then：返回 HTTP 200，`ok=true`，`llm_summary.available=false`，scores/extracted/findings 仍完整。

### 场景 D：错误 key / 超时 fallback 不阻断

- Given：LLM key 错误、provider 返回 4xx/5xx，或 LLM 调用超时。
- When：调用 scanner API。
- Then：返回 HTTP 200，`ok=true`，`llm_summary.available=false`，前端不显示内部错误。

### 场景 E：secret 不泄漏

- Given：生产配置了 LLM key。
- When：查看首页源码、scanner.js、scan API 响应、错误响应、邮件正文。
- Then：不得出现完整 API key 或明显 secret 片段。

---

## 4. 非功能验收

- LLM timeout ≤ 8 秒。
- fallback 路径整体 ≤ 12 秒。
- 不新增前端 UI。
- 不修改 CR-001 的 API schema、评分规则、SSRF 防护。
- 生产配置必须由 Kevin 手工填入或授权，agent 不生成、不猜测 secret。

---

## 5. 依赖、风险与待裁决

### 依赖

- 有效 LLM API key。
- OpenAI-compatible 代理 base_url：`https://chat.aiprox.net`。
- 默认模型：`gpt-5.4-mini`。
- 可修改生产 WordPress 配置（wp-config.php / option / filter）。
- 生产站点可访问外部 LLM API。

### 风险

| 风险 | 收敛方式 |
|---|---|
| API key 泄漏 | secret 不入仓；验证源码/响应/邮件不含 key |
| LLM 编造 | prompt 限制 + 场景 B 验证 |
| 成本失控 | rate limit 已由 CR-001 控制；LLM timeout 限制 |
| LLM 故障影响 scanner | fallback 不阻断 |

### 待 Kevin 操作

1. 提供或在生产环境填入 LLM API key。
2. 决定 key 配置位置：`wp-config.php` 常量 / 后台 option / Hostinger 环境变量（若支持）。
