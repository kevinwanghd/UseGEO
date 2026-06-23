# Acceptance Spec: Real Scanner v1 真实诊断升级

> 由 Spec Agent 产出。将 `request.md` 转化为可测试的验收规格（SDD 三段式）。
> 严格遵循 PRD 锚点 `PRD-REAL-SCANNER-V1`，禁止扩展 PRD 未承诺的功能；具体实现细节（公式、UI 文案、命名）由后续 Design / Dev 阶段细化。

## CR-ID
CR-001

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-REAL-SCANNER-V1
  prd_hash: bb44da62253e
```

## 规格框架

- 第 1 节 Data Spec：实体、字段约束、状态转换、不变式。
- 第 2 节 Interface Spec：API 签名、入参/出参、错误码、WordPress 内部接口、邮件、后台展示。
- 第 3 节 Behavior Spec：可机检的行为场景与“绝不允许”。
- 第 4 节 非功能验收：性能、安全、合规、可观测性、回退。
- 第 5 节 依赖与风险、需求不明确点（待裁决）、附录。

---

## 1. Data Spec（数据规格）

### 1.1 Entities（实体）

| 实体名 | 说明 | 关键字段 |
|---|---|---|
| ScanRequest | 一次 scan 的入参 | url |
| ScanResult | 一次 scan 的结构化结果（API 响应主体） | ok / url / final_url / scores / extracted / findings / llm_summary / error |
| ScanScores | 5 维度评分 | total / entity_clarity / content_structure / proof_signals / comparison_readiness / ai_extractability |
| ScanExtracted | 从首页解析得到的原始信号摘要 | title / meta_description / h1 / h2 / schema_types / canonical / word_count / detected_signals |
| ScanFinding | 一条基于真实信号的发现 | severity / dimension / title / evidence / recommendation |
| LLMSummary | LLM 解释结果（可缺省） | available / summary / top_recommendations / audit_recommendation |
| ScanError | 失败响应 | error_code / message |
| UseGEOLead.scan_snapshot | 现有 `usegeo_lead` 上挂的本次 scan 摘要（post meta 子集） | scan_total_score / scan_entity_clarity / scan_content_structure / scan_proof_signals / scan_comparison_readiness / scan_ai_extractability / scan_extracted_title / scan_extracted_h1 / scan_findings_summary / scan_llm_summary / scan_completed_at |

### 1.2 Fields & Constraints（字段与约束）

| 实体.字段 | 类型 | 约束 | 默认值 | 说明 |
|---|---|---|---|---|
| ScanRequest.url | string | required, http/https only, 最长 2048 字符 | — | 用户输入的网站 URL |
| ScanResult.ok | bool | required | — | true 表示成功响应；false 表示错误响应 |
| ScanResult.url | string | required | — | 用户原始输入 URL |
| ScanResult.final_url | string | optional | null | 跟随 redirect 后的最终 URL，最多 3 跳 |
| ScanScores.total | int | required, 0–100 | — | 5 维度的加权或归一总分（公式不在本 spec 锁定） |
| ScanScores.entity_clarity | int | required, 0–100 | — | 维度分 |
| ScanScores.content_structure | int | required, 0–100 | — | 维度分 |
| ScanScores.proof_signals | int | required, 0–100 | — | 维度分 |
| ScanScores.comparison_readiness | int | required, 0–100 | — | 维度分 |
| ScanScores.ai_extractability | int | required, 0–100 | — | 维度分 |
| ScanExtracted.title | string | optional, 最长 512 | "" | 实际抓到的 title |
| ScanExtracted.meta_description | string | optional, 最长 1024 | "" | meta description |
| ScanExtracted.h1 | string[] | optional, 每条最长 512，最多 10 条 | [] | 真实 h1 文本 |
| ScanExtracted.h2 | string[] | optional, 每条最长 512，最多 30 条 | [] | 真实 h2 文本 |
| ScanExtracted.schema_types | string[] | optional, 最多 50 条 | [] | JSON-LD `@type` 列表 |
| ScanExtracted.canonical | string | optional | null | canonical URL |
| ScanExtracted.word_count | int | optional, ≥ 0 | 0 | 正文 word count |
| ScanExtracted.detected_signals | object | required（成功响应时） | {} | 关键词信号布尔位图（具体键名留待 Design 阶段） |
| ScanFinding.severity | enum | required, ∈ {high, medium, low} | — | 优先级 |
| ScanFinding.dimension | enum | required, ∈ {entity_clarity, content_structure, proof_signals, comparison_readiness, ai_extractability} | — | 归属维度 |
| ScanFinding.title | string | required, 最长 200 | — | 简短标题 |
| ScanFinding.evidence | string | required, 最长 600 | — | 来自真实抓取的证据描述（不允许编造） |
| ScanFinding.recommendation | string | required, 最长 600 | — | 建议动作 |
| LLMSummary.available | bool | required | — | LLM 是否启用且成功 |
| LLMSummary.summary | string | required when available=true | "" | 自然语言诊断 |
| LLMSummary.top_recommendations | string[] | required when available=true，1–5 条 | [] | 推荐动作 |
| LLMSummary.audit_recommendation | object | required when available=true | — | 是否建议申请 Manual GEO Audit |
| ScanError.error_code | enum | required, ∈ {invalid_url, blocked_url, fetch_timeout, fetch_failed, non_html_response, parse_failed, rate_limited, internal_error} | — | 错误分类 |
| ScanError.message | string | required, 最长 300 | — | 用户友好提示，不含 stack trace / 服务器路径 |
| UseGEOLead.scan_total_score | int | optional（无 scan 的 lead 允许缺省） | null | 本次 scan 总分 |
| UseGEOLead.scan_entity_clarity | int | optional | null | 维度分 |
| UseGEOLead.scan_content_structure | int | optional | null | 维度分 |
| UseGEOLead.scan_proof_signals | int | optional | null | 维度分 |
| UseGEOLead.scan_comparison_readiness | int | optional | null | 维度分 |
| UseGEOLead.scan_ai_extractability | int | optional | null | 维度分 |
| UseGEOLead.scan_extracted_title | string | optional | "" | 抓到的 title |
| UseGEOLead.scan_extracted_h1 | string | optional | "" | 抓到的 H1（首条） |
| UseGEOLead.scan_findings_summary | string | optional, 最长 2000 | "" | findings 的精简摘要文本 |
| UseGEOLead.scan_llm_summary | string | optional, 最长 4000 | "" | LLM summary 全文（如有） |
| UseGEOLead.scan_completed_at | datetime | optional | null | scan 完成时间（站点本地时间） |

### 1.3 State Transitions（一次 scan 的内部状态机）

| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| Scan | requested | requested → fetched | 抓取成功，content-type=HTML，size ≤ 2MB | — |
| Scan | requested | requested → failed | 抓取超时 / 非 HTML / SSRF 命中 / rate limit / 内部异常 | failed |
| Scan | fetched | fetched → parsed | HTML 解析成功（至少 title 或 h1 之一可读出） | — |
| Scan | parsed | parsed → scored | 5 维度规则评分完成 | — |
| Scan | scored | scored → llm_summarized | LLM 调用成功且响应符合 schema | done |
| Scan | scored | scored → llm_skipped | LLM 未启用 / API key 缺失 / 超时 / 报错 / 响应不合 schema | done |
| Scan | failed | failed → returned | 错误响应已发回前端 | returned |

### 1.4 Data Invariants（数据不变式）

1. 任何 `ok=true` 的响应必须包含 5 个维度分（total / entity_clarity / content_structure / proof_signals / comparison_readiness / ai_extractability），且每项 ∈ [0, 100]。
2. 任何 `ok=true` 的响应必须包含 `extracted` 与至少 3 条 `findings`；`findings[*].evidence` 必须能从 `extracted` 中找到来源——不允许出现 `extracted` 中不存在的具体客户、案例或竞品名。
3. 当 `llm_summary.available=false` 时，前端仍须能展示 5 维度分 + findings + evidence；当 `llm_summary.available=true` 时，`summary` 与 `top_recommendations` 必须非空。
4. `ok=false` 时必须有 `error.error_code`，且 `error.message` 不含 PHP warning / stack trace / 服务器路径。
5. lead 一旦带有任何 `scan_*` post meta，必须同时具备：`scan_total_score` 非空、`scan_completed_at` 非空、`scan_findings_summary` 非空，以保证后台展示和邮件至少能可信地引用一个状态点。
6. lead 没有 scan 的旧记录可以缺省所有 `scan_*` meta；**不得**被强制写默认 0 分以避免污染数据。
7. 重定向后落地的 `final_url` 不能落入 SSRF 黑名单——若命中视为 `blocked_url`，按 `failed` 处理。

---

## 2. Interface Spec（接口规格）

### 2.1 Public Scan API

| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| usegeo.scan | POST | `/wp-json/usegeo/v1/scan` | 给定 URL 返回 GEO readiness scan |

鉴权 / 来源：

- 免登录可调用。
- 必须通过 nonce 校验或同源 origin 校验之一（与 PRD-LEAD-CAPTURE 中 `usegeo_verify_public_form_nonce` 行为一致）。
- 仅接受 `Content-Type: application/json`。

Input Schema：

```json
{
  "url": "string (required, http/https only, max 2048)"
}
```

其它字段被忽略；Real Scanner v1 不接受 `model` / `prompt` / `mode` 等扩展字段。

Output Schema（成功）：

```json
{
  "ok": true,
  "url": "string",
  "final_url": "string|null",
  "scores": {
    "total": 72,
    "entity_clarity": 75,
    "content_structure": 68,
    "proof_signals": 54,
    "comparison_readiness": 42,
    "ai_extractability": 79
  },
  "extracted": {
    "title": "string",
    "meta_description": "string",
    "h1": ["string"],
    "h2": ["string"],
    "schema_types": ["string"],
    "canonical": "string|null",
    "word_count": 0,
    "detected_signals": { "...": true }
  },
  "findings": [
    {
      "severity": "high|medium|low",
      "dimension": "entity_clarity|content_structure|proof_signals|comparison_readiness|ai_extractability",
      "title": "string",
      "evidence": "string",
      "recommendation": "string"
    }
  ],
  "llm_summary": {
    "available": true,
    "summary": "string",
    "top_recommendations": ["string"],
    "audit_recommendation": {
      "recommended": true,
      "reason": "string"
    }
  }
}
```

Output Schema（错误）：

```json
{
  "ok": false,
  "error": {
    "error_code": "invalid_url|blocked_url|fetch_timeout|fetch_failed|non_html_response|parse_failed|rate_limited|internal_error",
    "message": "string"
  }
}
```

HTTP 状态码与 `ok` 的对应关系：

| 场景 | HTTP | ok | error_code |
|---|---|---|---|
| 正常成功 | 200 | true | — |
| 用户输入非法 / SSRF 命中 | 400 | false | invalid_url / blocked_url |
| 抓取超时 | 504 | false | fetch_timeout |
| 抓取失败 / 非 HTML | 502 | false | fetch_failed / non_html_response |
| 解析失败 | 422 | false | parse_failed |
| 触发 rate limit | 429 | false | rate_limited |
| 服务器内部异常 | 500 | false | internal_error |

LLM 不可用不视为错误：HTTP 200 + `ok: true` + `llm_summary.available: false`。

### 2.2 Frontend Contract（前端调用契约）

- 前端 `wordpress/theme/usegeo/assets/js/scanner.js` 必须从“前端模拟分数”切换为调用 `/wp-json/usegeo/v1/scan`。
- loading 阶段沿用既有三段进度（Read site structure / Map AI signals / Prepare next steps），不得出现无穷加载。
- 成功响应必须渲染：总分、5 维度分、雷达图、≥3 条 findings、evidence 区、LLM summary 或 fallback rule summary。
- 错误响应必须按 `error_code` 映射到友好文案，且**不得**展示 raw `message` 中的服务器路径 / 堆栈细节。
- 提交 Full Diagnostic 表单时，必须随 POST 携带：
  - `scanner_query`：用户输入 URL；
  - `scan_total_score`、`scan_entity_clarity`、`scan_content_structure`、`scan_proof_signals`、`scan_comparison_readiness`、`scan_ai_extractability`；
  - `scan_completed_at`；
  - `scan_findings_summary`、`scan_llm_summary`（可选）。
- 后端在保存 lead 时按 `UseGEOLead.scan_*` post meta 名称落库（命名见 1.2）。
- Feature flag `USEGEO_REAL_SCANNER_ENABLED`（WP 常量或 filter）：
  - true：调用真实 API；
  - false：保留当前前端模拟分数路径，前端必须根据该 flag 渲染对应模式（具体实现由 Design 阶段决定）。

### 2.3 WordPress 内部接口

- Lead 写入：复用 `usegeo_handle_lead_capture`（PRD-LEAD-CAPTURE）。新增 scan 相关 post meta，不修改 nonce / 同源 / 邮箱 / 必填字段校验逻辑。
- 错误处理：lead 提交时如果缺失 `scan_total_score` 或 `scan_completed_at`，但其它字段合法，仍允许保存 lead，并将该 lead 标记为 “scan snapshot missing”（保留转化）。
- 邮件通知：在现有 `usegeo_send_lead_notification` 邮件正文中追加：
  - Website
  - GEO total score
  - Weakest dimension（5 维度中分最低者）
  - Top finding（`findings[0].title`）
  - Recommended next step（LLM 可用时取 `llm_summary.top_recommendations[0]`，否则取 fallback 规则文案）
- 后台列表：`usegeo_lead_admin_columns` 至少新增两列：
  - `scan_total_score`
  - `weakest_dimension`
  两列默认可见，且允许通过 filter `usegeo_lead_admin_columns_visible` 关闭。

### 2.4 配置接口

| 配置项 | 形式 | 默认 | 说明 |
|---|---|---|---|
| `USEGEO_REAL_SCANNER_ENABLED` | WP 常量 / filter `usegeo_real_scanner_enabled` | true | feature flag |
| `USEGEO_SCAN_FETCH_TIMEOUT` | filter `usegeo_scan_fetch_timeout` | 10s | 抓取 timeout，允许 5–15s |
| `USEGEO_SCAN_MAX_BYTES` | filter `usegeo_scan_max_bytes` | 2 MB | 抓取大小上限 |
| `USEGEO_SCAN_USER_AGENT` | filter `usegeo_scan_user_agent` | `UseGEO/1.0` | 出站 User-Agent |
| `USEGEO_SCAN_RATE_LIMIT_PER_IP` | filter `usegeo_scan_rate_limit_per_ip` | 由 Behavior/NFR 段量化 | 每 IP 限流（单位：次/分钟） |
| `USEGEO_LLM_PROVIDER` | option / filter | 由 Design 选择（OpenAI / 其它） | LLM 提供方标识 |
| `USEGEO_LLM_API_KEY` | WP 常量 / option | 空 | 不入仓 |
| `USEGEO_LLM_TIMEOUT` | filter | 8s | LLM 超时 |
| `USEGEO_LLM_MODEL` | filter | 由 Design 决定 | 模型名 |

> 运行时秘密（如 LLM API key）必须通过 WP 常量或 option 注入，不得入仓；`*.env` / 配置文件不可承载真实 key。

---

## 3. Behavior Spec（行为规格）

每个场景采用 Given / When / Then，要求可机检；本节是后续 `verification-manifest.yml` 的来源。

### 场景 A：合法公开 URL 真实抓取

- Given：`USEGEO_REAL_SCANNER_ENABLED = true`，用户输入合法 http/https URL（非 SSRF 黑名单），目标站点返回 200 + HTML。
- When：用户点击 Run assessment。
- Then：
  - 后端真实请求该 URL；
  - 响应 HTTP 200，`ok = true`；
  - `scores` 含 5 维度分，且每项 ∈ [0, 100]；
  - `extracted` 至少包含 `title` 或 `h1[0]` 中的一项真实文本；
  - `findings` 至少 3 条，且 `findings[*].evidence` 中出现的关键事实均能在 `extracted` 中找到来源；
  - 不再依赖用户 URL 的字符串特征生成分数。

### 场景 B：LLM 不可用

- Given：LLM 未配置 API key，或 LLM 调用 5xx / timeout / 响应不合 schema。
- When：用户运行 scanner。
- Then：
  - 仍返回 HTTP 200；
  - `ok = true`，5 维度分 + ≥3 findings + `extracted` 仍正常；
  - `llm_summary.available = false`，`summary` 字段不出现 LLM 错误提示；
  - 前端展示完整规则版结果，loading 状态不持续超过 12s；
  - 服务器日志可定位 LLM 失败原因（不静默）。

### 场景 C：URL 非法 / SSRF 命中

- Given：用户输入以下任一：`http://localhost`、`http://127.0.0.1`、`http://10.0.0.1`、`file:///etc/passwd`、`ftp://example.com`、明显非 URL 字符串。
- When：用户点击 Run assessment。
- Then：
  - 后端**不发起**任何外部请求；
  - HTTP 400，`ok = false`，`error.error_code ∈ {invalid_url, blocked_url}`；
  - `error.message` 是用户友好文案，不含 stack trace；
  - 不向用户暴露内部 IP / 服务器路径；
  - 该次请求在服务器日志中被记录为 SSRF block。

### 场景 D：抓取超时 / 非 HTML / 超大文件

- Given：目标站点 8–12s 不响应；或返回 `Content-Type: application/pdf`；或响应大于 2 MB。
- When：用户运行 scanner。
- Then：
  - 后端拒绝继续解析；
  - 超时 → HTTP 504 + `error.error_code = fetch_timeout`；
  - 非 HTML → HTTP 502 + `error.error_code = non_html_response`；
  - 抓取失败 / 体积超限 / 5xx 上游 → HTTP 502 + `error.error_code = fetch_failed`；
  - 前端显示分类化的友好提示。

### 场景 E：触发 rate limit

- Given：同一 IP 在配置时间窗内超过 `USEGEO_SCAN_RATE_LIMIT_PER_IP` 次。
- When：用户继续点击 Run assessment。
- Then：
  - HTTP 429，`ok = false`，`error.error_code = rate_limited`；
  - `error.message` 提示稍后重试，不暴露剩余配额内部值；
  - rate limit 计数与 lead 表单提交、`/wp-json/` 其他端点互不影响。

### 场景 F：lead 提交携带 scan snapshot

- Given：用户已成功跑出 scan（场景 A 或 场景 B 之一），现在提交 Full Diagnostic 表单。
- When：表单 POST 到 `admin-post.php?action=usegeo_capture_lead`。
- Then：
  - 复用 PRD-LEAD-CAPTURE 的 nonce / 同源 / 邮箱校验，不放宽；
  - lead 写入成功后，下列 post meta 全部存在：
    - `scan_total_score`
    - `scan_entity_clarity`
    - `scan_content_structure`
    - `scan_proof_signals`
    - `scan_comparison_readiness`
    - `scan_ai_extractability`
    - `scan_findings_summary`
    - `scan_completed_at`
  - 邮件正文新增 5 行，按以下顺序：
    1. Website
    2. GEO total score
    3. Weakest dimension
    4. Top finding
    5. Recommended next step
  - 后台 lead 列表显示 `scan_total_score` 与 `weakest_dimension` 两列。

### 场景 G：Feature flag 关闭真实 scanner

- Given：`USEGEO_REAL_SCANNER_ENABLED = false`。
- When：用户进入首页 scanner，输入 URL 并点击 Run assessment。
- Then：
  - 前端不调用 `/wp-json/usegeo/v1/scan`；
  - 退回到当前“前端模拟分数”路径，UI 行为与 v0 一致；
  - 前端不报错，不显示空白结果；
  - lead 表单仍可正常提交（不携带新的 `scan_*` meta，也不报错）。

### 场景 H：scan 失败时 lead 表单仍可提交

- Given：用户运行 scanner 失败（场景 C / D / E 之一），但仍想填写 Full Diagnostic 表单。
- When：用户填写 lead 表单并提交。
- Then：
  - lead 写入成功，不要求 scan snapshot；
  - lead 标记为 “scan snapshot missing”（通过 `notification_status` 或新加 `scan_status` post meta，最终命名留 Design）；
  - 邮件正文中 GEO total score / Weakest dimension / Top finding / Recommended next step 四行写为 `(scan unavailable)`；
  - 后台列表 `scan_total_score` 显示为空（不写 0）。

### 3.x 绝不能发生（不变式负例，全部可机检）

1. 绝不允许任何文案声称已检测 ChatGPT / Claude / Gemini 推荐结果或排名——前端 UI、findings、LLM summary、邮件、后台同此约束。
2. 绝不允许 LLM 输出中出现 `extracted` 里没有出现过的具体客户名 / 案例 / 竞品名（编造）。
3. 绝不允许 SSRF 黑名单 IP / 协议被绕过：包括重定向后落到内网、DNS rebinding、`http://` 后域名解析到内网 IP。
4. 绝不允许 evidence 区为空但仍展示 5 维度分（这是“营销假分数”的典型形态）。
5. 绝不允许任何失败路径以模拟分数蒙混过关：抓取失败 / 解析失败 / SSRF 命中 / rate limit，必须返回 `ok=false`，不得返回伪造 `scores`。
6. 绝不允许向第三方 LLM 发送用户的 PII：lead 邮箱、公司类型、扫描者 IP、scanner_query 之外的表单字段一律不进 LLM prompt；prompt 输入仅限用户输入的目标 URL 与本次 scan 已抓取/解析的页面信号。

---

## 4. 非功能验收

### 4.1 性能预算

| 指标 | 阈值 | 来源 |
|---|---|---|
| 单次 scan 用户感知耗时（成功路径，含 LLM） | ≤ 15 s | request 已确认 |
| 单次 scan 用户感知耗时（LLM 失败路径，规则版） | ≤ 12 s | request 已确认 |
| 后端 fetch timeout | 8–12 s（默认 10 s） | request + interface 段 |
| LLM 调用 timeout | ≤ 8 s | interface 段 `USEGEO_LLM_TIMEOUT` |
| 抓取响应大小上限 | 2 MB | interface 段 |
| 单 IP 限流（默认） | 6 次 / 分钟 | 本节量化（可由 filter 调） |
| 单会话限流（默认） | 12 次 / 5 分钟 | 本节量化（可由 filter 调） |

### 4.2 安全验收

- SSRF 黑名单（最少集合）：
  - 协议白名单：仅 http / https；
  - 主机黑名单：localhost、127.0.0.1、0.0.0.0、`::1`、私有 IPv4（10.0.0.0/8、172.16.0.0/12、192.168.0.0/16）、IPv6 ULA `fc00::/7`；
  - 重定向后命中黑名单 → 视为 `blocked_url`；
  - 不解析 `file://` / `ftp://` / `gopher://` / `data:` 等。
- 内容大小：实际下载量超过 2 MB 必须中断（不可只看 Content-Length）。
- Content-Type：必须以 `text/html` 开头；其它一律 `non_html_response`。
- HTTP 客户端：
  - User-Agent 必须以 `UseGEO/` 开头；
  - 不发送 cookies；
  - 不携带任何 lead PII；
  - 不接受 `Set-Cookie` 影响后续行为；
  - 最多 3 次 redirect。
- 鉴权：所有公开端点必须沿用 PRD-LEAD-CAPTURE 的 nonce / 同源 origin 校验，不放宽。
- 配置：API key、LLM provider 通过 WP 常量或 option 注入；不入仓；不出现在前端 / 错误响应 / 邮件。

### 4.3 合规与文案

- 所有面向用户的文案（前端、邮件、后台）：
  - 必须使用 directional / readiness / signal scan 等措辞；
  - 不得使用 "AI ranking" / "ChatGPT recommendation tracking" / "guaranteed AI mentions" 等措辞。
- LLM prompt 输入（PII 边界）：
  - 仅允许：用户输入的目标 URL、本次 scan 已抓取/解析的页面信号；
  - 不允许：lead 邮箱、表单字段、扫描者 IP、cookie、session 信息。
- 错误响应中：
  - `error.message` 不得包含路径 / SQL / stack trace / WP debug log；
  - 不得回显原始 `Authorization` / API key / SQL fragment。

### 4.4 可观测性

- 服务器侧必须能定位以下事件（日志或 WP debug log，最终落点由 Design 决定）：
  - SSRF block；
  - fetch timeout / fetch failed / non-html / size exceeded；
  - LLM 失败（不写 API key 原文）；
  - rate limit 触发；
  - lead 写入时 scan snapshot 缺失。
- lead 必须保留 `scan_completed_at` 与 `notification_status`（PRD-LEAD-CAPTURE 已有）。
- 不要求引入新的指标系统或第三方 APM。

### 4.5 回退（Feature Flag）

- `USEGEO_REAL_SCANNER_ENABLED = false` 时，前端必须无错回退到当前模拟分数路径。
- 该 flag 切换不需要部署，只需 WP 常量 / option 调整。
- 至少在 Real Scanner v1 上线 30 天内保留旧逻辑代码路径，不删除。

### 4.6 数据迁移

- 旧 lead 不强制回填 `scan_*` post meta。
- 后台列表 `scan_total_score` 列对旧 lead 显示空字符串，不写 0。
- 后续如需回填，作为单独运营动作处理，不在本 CR。

---

## 5. 依赖、风险与需求不明确点

### 5.1 依赖

- 外部依赖：
  - 目标网站（用户输入 URL）：可达、返回 HTML；
  - LLM 服务（OpenAI / 等同提供方）：可选依赖；
  - WordPress 内置邮件通道：用于 lead 通知。
- 内部依赖：
  - PRD-LEAD-CAPTURE / PRD-OPS-ADMIN：lead 表单、后台列表、邮件路径；
  - PRD-FREE-SCANNER：现有 scanner UI 与 lead 表单；
  - PRD-SITE-FOUNDATION：主题资源加载、统一页头页脚。

### 5.2 主要风险

| 风险 | 收敛方式 |
|---|---|
| SSRF | 4.2 安全验收 + 场景 C 共同收敛 |
| LLM 编造 | 不变式 2 + 6 共同收敛 |
| 假分数退化 | 不变式 4、5 共同收敛 |
| 性能波动（抓取/LLM 慢导致 UX 退化） | 4.1 性能预算 + 场景 B/D 超时控制 |
| 配置泄漏（API key 入仓 / 出现在错误响应） | 4.2 / 4.3 控制 |
| 误判老 lead | 4.6 禁止用 0 分回填 |

### 5.3 需求不明确点（待裁决，进 Design 前需确认）

| ID | 问题 | 当前默认 | 建议下一步 |
|---|---|---|---|
| Q1 | 维度评分公式（规则版） | 草稿 PRD 的 5 维度信号清单为锚点，公式由 Design 决定 | 在 Design 阶段固化，并写入实现说明 |
| Q2 | LLM provider / 模型 | 不锁定具体 provider，只锁定 schema、超时、降级 | Design 阶段选定（OpenAI 或本地代理），写入 decisions.md |
| Q3 | rate limit 默认阈值 | 已在 4.1 给默认值（6 次/分钟、12 次/5 分钟） | 上线前可压测调整 |
| Q4 | feature flag 默认值 | true（上线即启用）；环境可单独关 | 上线前改成 false 静默灰度也可，由发布计划决定 |
| Q5 | 后台 lead 列表是否给 weakest_dimension 加视觉徽章 | 仅展示文本 | UI 设计阶段决定 |

### 5.4 验收检查清单（Verification 入口）

1. API 契约一致：`POST /wp-json/usegeo/v1/scan` 的 input/output schema 与 Interface Spec 一致。
2. Behavior Spec 8 个场景与 6 条不变式全部可机检。
3. 整站基线（CR-LEGACY-SCAN 的 acceptance-spec）24 条不被破坏。
4. 关键路径（首页 scan、lead 提交）UI / 邮件 / 后台展示均通过手工或脚本验证。
5. 配置接口（4.1–4.5）的默认值生效，且可通过 filter / 常量覆盖。

### 5.5 附录

现有源代码主要触点：

- `wordpress/theme/usegeo/template-parts/scanner/scanner.php`
- `wordpress/theme/usegeo/assets/js/scanner.js`
- `wordpress/theme/usegeo/inc/forms.php`
- `wordpress/theme/usegeo/inc/post-types.php`
- `wordpress/theme/usegeo/inc/admin.php`

参考文档：

- PRD：`DeliverHQ/docs/PRD.md` 中的 `PRD-REAL-SCANNER-V1`
- 草稿 PRD：`docs/product/usegeo-real-scanner-v1-prd.md`
- 整站基线 spec：`DeliverHQ/change-requests/CR-LEGACY-SCAN/acceptance-spec.md`
