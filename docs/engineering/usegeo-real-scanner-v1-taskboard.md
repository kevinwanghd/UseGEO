# UseGEO Real Scanner v1 需求卡片 / 看板任务

## 1. 目的

这份看板用于把 `UseGEO` 当前首页 scanner 从“前端模拟评分”升级为“真实轻量 GEO readiness scan”。

目标不是一次性做完整 GEO SaaS，而是先完成：

1. 真实抓取用户输入的网站首页。
2. 解析基础 GEO / SEO / AI extractability 信号。
3. 用规则模型生成可解释评分。
4. 用 LLM 生成自然语言诊断。
5. 把 scan snapshot 写入 lead，支撑后续 Manual GEO Audit 转化。

---

## 2. 当前不做

以下事项不进入 Real Scanner v1：

- 多页面全站爬虫
- 账号注册 / 登录
- 历史报告 dashboard
- 真实 ChatGPT / Claude / Gemini 推荐排名追踪
- 自动竞品识别
- PDF 报告自动生成
- 企业级 CRM 自动化

---

## 3. 任务总览

| ID | 任务 | 优先级 | 状态 | 阶段 |
|---|---|---:|---|---|
| RS-01 | 定义 scanner API 响应结构 | P0 | Ready | API 基础 |
| RS-02 | 新增 WordPress REST API 路由 | P0 | Ready | API 基础 |
| RS-03 | 实现 URL 校验与 SSRF 防护 | P0 | Ready | 安全 |
| RS-04 | 实现首页 HTML 抓取器 | P0 | Ready | 抓取 |
| RS-05 | 解析基础 SEO / GEO 信号 | P0 | Ready | 解析 |
| RS-06 | 解析 schema.org JSON-LD | P0 | Ready | 解析 |
| RS-07 | 实现 GEO 信号关键词检测 | P0 | Ready | 解析 |
| RS-08 | 实现 5 维度规则评分 | P0 | Ready | 评分 |
| RS-09 | 生成规则版 findings | P0 | Ready | 评分 |
| RS-10 | 替换前端模拟评分逻辑 | P0 | Ready | 前端 |
| RS-11 | 更新结果 UI，显示真实 evidence | P0 | Ready | 前端 |
| RS-12 | 把 scan snapshot 写入 lead 表单 hidden fields | P0 | Ready | 转化 |
| RS-13 | 扩展 lead 保存字段 | P0 | Ready | 转化 |
| RS-14 | 增加简单 rate limit | P0 | Ready | 安全 |
| RS-15 | 增加错误分类与友好提示 | P0 | Ready | 可靠性 |
| RS-16 | 准备 5 个测试网站样本 | P0 | Ready | QA |
| RS-17 | 完整主路径验收 | P0 | Ready | QA |
| RS-18 | 设计 LLM prompt 模板 | P1 | Ready | LLM |
| RS-19 | 增加 LLM provider 配置 | P1 | Ready | LLM |
| RS-20 | 实现 LLM 报告生成函数 | P1 | Ready | LLM |
| RS-21 | 增加 LLM 内容安全过滤 | P1 | Ready | LLM |
| RS-22 | 更新 LLM summary 展示 | P1 | Ready | 前端 |
| RS-23 | 邮件通知增加 scan summary | P1 | Backlog | 运营 |
| RS-24 | 后台 lead 列表显示 scanner 分数 | P1 | Backlog | 运营 |
| RS-25 | 增加扫描结果缓存 | P1 | Backlog | 性能 |

---

## 4. 推荐执行顺序

### 阶段 1：真实但不智能

目标：先把 scanner 从“假的”变成“半真但可信”。

执行顺序：

1. RS-01 API contract
2. RS-02 REST route
3. RS-03 URL 安全校验
4. RS-04 HTML 抓取
5. RS-05 基础解析
6. RS-06 schema 解析
7. RS-07 信号检测
8. RS-08 规则评分
9. RS-09 规则 findings
10. RS-10 前端接 API
11. RS-11 前端展示 evidence

### 阶段 2：LLM 解释层

目标：让报告更像专家诊断。

执行顺序：

1. RS-18 LLM prompt 模板
2. RS-19 provider 配置
3. RS-20 LLM summary 函数
4. RS-21 内容安全过滤
5. RS-22 前端展示 LLM summary

### 阶段 3：转化闭环

目标：让 scanner 支撑人工跟进和 Manual GEO Audit 销售。

执行顺序：

1. RS-12 scan snapshot hidden fields
2. RS-13 lead 保存 scan 数据
3. RS-23 邮件通知增加 scan summary
4. RS-24 后台 lead 列表显示 score

### 阶段 4：安全与验收

目标：上线前保证稳定和可信。

执行顺序：

1. RS-14 rate limit
2. RS-15 错误分类
3. RS-16 测试样本
4. RS-17 完整主路径验收

---

## 5. 详细需求卡片

## RS-01. 定义 scanner API 响应结构

**优先级：** P0  
**状态：** Ready  
**类型：** Product / Engineering  
**阶段：** API 基础

### 目标

定义 `/wp-json/usegeo/v1/scan` 的请求和响应结构，确保前端、后端、LLM 和 lead 保存围绕同一份数据模型实现。

### 建议请求

```json
{
  "url": "https://example.com"
}
```

### 建议响应

```json
{
  "ok": true,
  "url": "https://example.com",
  "final_url": "https://example.com/",
  "scores": {
    "total": 72,
    "entity_clarity": 75,
    "content_structure": 68,
    "proof_signals": 54,
    "comparison_readiness": 42,
    "ai_extractability": 79
  },
  "extracted": {
    "title": "...",
    "meta_description": "...",
    "h1": ["..."],
    "h2": ["..."],
    "schema_types": ["Organization"],
    "canonical": "...",
    "word_count": 820,
    "detected_signals": {
      "has_case_study_terms": true,
      "has_customer_terms": false,
      "has_comparison_terms": false,
      "has_pricing_terms": true,
      "has_faq_schema": false,
      "has_organization_schema": true
    }
  },
  "findings": [],
  "llm_summary": {
    "available": false,
    "summary": "",
    "top_recommendations": [],
    "audit_recommendation": ""
  }
}
```

### 验收标准

- API contract 字段清楚。
- 前端显示需要的字段都存在。
- LLM 可用和不可用时都能表达。
- lead 保存所需字段能从响应中获得。

---

## RS-02. 新增 WordPress REST API 路由

**优先级：** P0  
**状态：** Ready  
**类型：** Backend  
**阶段：** API 基础

### 涉及文件

- 修改：`D:\Code\UseGEO\wordpress\theme\usegeo\functions.php`
- 新增：`D:\Code\UseGEO\wordpress\theme\usegeo\inc\scanner-api.php`

### 目标

注册：

```http
POST /wp-json/usegeo/v1/scan
```

### 验收标准

- 访问 endpoint 能返回 JSON。
- 未传 URL 时返回 400。
- URL 无效时返回 400。
- 合法 URL 进入扫描流程。
- 不影响现有首页渲染。

---

## RS-03. 实现 URL 校验与 SSRF 防护

**优先级：** P0  
**状态：** Ready  
**类型：** Backend / Security  
**阶段：** 安全

### 目标

在后端抓取前校验 URL，避免 scanner 被用来请求本机、内网或非网页资源。

### 必须阻止

- `localhost`
- `127.0.0.1`
- `0.0.0.0`
- `::1`
- `10.x.x.x`
- `172.16.x.x - 172.31.x.x`
- `192.168.x.x`
- `file://`
- `ftp://`
- 非 `http` / `https` 协议

### 验收标准

- 输入 `http://localhost` 被拒绝。
- 输入 `http://127.0.0.1` 被拒绝。
- 输入 `https://usegeo.blog` 可以继续。
- 错误返回可读 JSON，不返回 PHP warning。

---

## RS-04. 实现首页 HTML 抓取器

**优先级：** P0  
**状态：** Ready  
**类型：** Backend  
**阶段：** 抓取

### 目标

服务端真实抓取用户输入 URL 的 HTML。

### 要求

- 使用 WordPress HTTP API：`wp_remote_get`
- timeout：8-12 秒
- redirection：最多 3 次
- response size：建议限制 1MB-2MB
- User-Agent：设置为 `UseGEO Scanner`
- 只接受 HTML 响应
- 非 2xx 返回错误

### 验收标准

- 能抓取正常网站首页。
- 超时网站返回 `fetch_timeout` 或 `fetch_failed`。
- PDF、图片、zip 等非 HTML 不进入解析。
- 抓取失败时前端能显示友好错误。

---

## RS-05. 解析基础 SEO / GEO 信号

**优先级：** P0  
**状态：** Ready  
**类型：** Backend  
**阶段：** 解析

### 目标

从 HTML 中提取基础页面信号。

### 需要提取

- `<title>`
- meta description
- canonical
- robots meta
- H1
- H2
- body text
- word count
- internal links
- external links

### 验收标准

- 正常 HTML 页面能提取 title / H1 / H2。
- 缺失字段返回空数组或 null，不报错。
- body text 去掉 script / style 等明显噪声。

---

## RS-06. 解析 schema.org JSON-LD

**优先级：** P0  
**状态：** Ready  
**类型：** Backend  
**阶段：** 解析

### 目标

解析页面中的：

```html
<script type="application/ld+json">
```

### 重点提取类型

- Organization
- WebSite
- Product
- Service
- FAQPage
- Article
- BreadcrumbList
- LocalBusiness

### 验收标准

- 有 JSON-LD 时能解析 `@type`。
- JSON-LD 格式错误时跳过，不中断扫描。
- 支持单对象和数组。
- 返回 `schema_types` 数组。

---

## RS-07. 实现 GEO 信号关键词检测

**优先级：** P0  
**状态：** Ready  
**类型：** Backend  
**阶段：** 解析

### 目标

基于页面文本检测 GEO 相关信号。

### 信号类别

Proof signals：

- case study
- customer
- client
- testimonial
- results
- ROI
- review
- trusted by

Comparison signals：

- compare
- comparison
- alternative
- alternatives
- versus
- vs
- best
- top
- for teams
- for agencies
- for B2B

Commercial clarity：

- pricing
- plans
- demo
- book a call
- contact sales
- use cases
- solutions

Entity clarity：

- about
- company
- platform
- service
- product
- category terms

### 验收标准

- 返回 `detected_signals`。
- 关键词检测大小写不敏感。
- 每个 signal 可用于后续评分和 evidence 文案。

---

## RS-08. 实现 5 维度规则评分

**优先级：** P0  
**状态：** Ready  
**类型：** Backend / Product Logic  
**阶段：** 评分

### 目标

实现第一版规则评分算法。

### 维度

1. Entity clarity
2. Content structure
3. Proof signals
4. Comparison readiness
5. AI extractability

### 验收标准

- 每个维度输出 0-100。
- total 是 5 维度加权平均或简单平均。
- 每个低分维度能生成至少一条 finding。
- 评分逻辑集中在一个函数/文件，方便后续调整。

---

## RS-09. 生成规则版 findings

**优先级：** P0  
**状态：** Ready  
**类型：** Backend / Product Logic  
**阶段：** 评分

### 目标

根据评分和检测信号生成基础 findings。

### Finding 字段

- `dimension`
- `severity`
- `title`
- `evidence`
- `recommendation`

### 验收标准

- 至少返回 3 条 findings。
- findings 必须包含 evidence。
- 即使 LLM 不可用，前端也能展示 findings。

---

## RS-10. 替换前端模拟评分逻辑

**优先级：** P0  
**状态：** Ready  
**类型：** Frontend  
**阶段：** 前端

### 涉及文件

- 修改：`D:\Code\UseGEO\wordpress\theme\usegeo\assets\js\scanner.js`

### 目标

停用当前根据 URL 字符串生成分数的模拟逻辑，改为调用真实 API。

### 验收标准

- 输入不同网站，结果来自后端。
- 不再根据 URL 长度算分。
- loading 状态持续到 API 返回。
- API 失败时显示友好提示。

---

## RS-11. 更新结果 UI，显示真实 evidence

**优先级：** P0  
**状态：** Ready  
**类型：** Frontend / UX  
**阶段：** 前端

### 目标

在现有结果区增加真实证据展示，让用户看出结果来自实际网页。

### 建议展示

- Page title
- H1
- Detected schema
- Word count
- Proof signals detected / not detected
- Comparison signals detected / not detected

### 验收标准

- 用户能看出结果来自真实网页。
- evidence 区默认展示关键 4-6 项。
- 移动端不破坏布局。
- 没有检测到的项目显示 `Not detected`。

---

## RS-12. 把 scan snapshot 写入 lead 表单 hidden fields

**优先级：** P0  
**状态：** Ready  
**类型：** Frontend / Backend  
**阶段：** 转化

### 目标

用户提交 full diagnostic 表单时，把本次 scan 的关键数据一起提交。

### 建议 hidden fields

- `scan_total_score`
- `scan_entity_clarity`
- `scan_content_structure`
- `scan_proof_signals`
- `scan_comparison_readiness`
- `scan_ai_extractability`
- `scan_extracted_title`
- `scan_extracted_h1`
- `scan_findings_summary`
- `scan_llm_summary`

### 验收标准

- 用户跑完 scanner 后提交表单，后台 lead 能看到 scan 信息。
- 如果用户没有跑 scanner，不提交假数据。
- hidden 数据需要 sanitize，避免保存过长或危险 HTML。

---

## RS-13. 扩展 lead 保存字段

**优先级：** P0  
**状态：** Ready  
**类型：** Backend  
**阶段：** 转化

### 涉及文件

- 修改：`D:\Code\UseGEO\wordpress\theme\usegeo\inc\forms.php`

### 目标

在 `usegeo_handle_lead_capture()` 中保存 scan snapshot。

### 建议 post meta

- `scan_total_score`
- `scan_entity_clarity`
- `scan_content_structure`
- `scan_proof_signals`
- `scan_comparison_readiness`
- `scan_ai_extractability`
- `scan_extracted_title`
- `scan_extracted_h1`
- `scan_findings_summary`
- `scan_llm_summary`
- `scan_completed_at`

### 验收标准

- 后台打开 lead 能看到 scan 数据。
- 字段缺失时不影响 lead 创建。
- 邮件通知后续可复用这些字段。

---

## RS-14. 增加简单 rate limit

**优先级：** P0  
**状态：** Ready  
**类型：** Backend / Security  
**阶段：** 安全

### 目标

对 scanner API 做基础限流，防止被滥用。

### 建议规则

- 同 IP 每 10 分钟最多 5 次扫描。
- 超出后返回 429。
- 使用 WordPress transient 实现。

### 验收标准

- 连续请求超过限制后返回 429。
- 正常用户不受影响。
- 错误提示友好。

---

## RS-15. 增加错误分类与友好提示

**优先级：** P0  
**状态：** Ready  
**类型：** Backend / UX  
**阶段：** 可靠性

### 目标

后端返回明确错误类型，前端显示对应文案。

### 错误类型

- `invalid_url`
- `blocked_url`
- `fetch_timeout`
- `fetch_failed`
- `non_html_response`
- `parse_failed`
- `rate_limited`

### 验收标准

- 用户知道是 URL 问题、网站无法访问，还是系统暂时失败。
- 不暴露 PHP warning、stack trace、服务器路径。
- 前端文案专业，不吓人。

---

## RS-16. 准备 5 个测试网站样本

**优先级：** P0  
**状态：** Ready  
**类型：** QA / Product  
**阶段：** QA

### 目标

准备覆盖不同场景的测试样本。

### 样本类型

1. B2B SaaS 官网
2. Agency / Consultancy 官网
3. 内容站 / Blog
4. 电商站
5. 故意输入错误 URL

### 验收标准

- 每个网站能返回不同结果。
- 分数差异能解释。
- 错误 URL 有友好失败。
- 至少 3 个真实网站结果看起来有道理。

---

## RS-17. 完整主路径验收

**优先级：** P0  
**状态：** Ready  
**类型：** QA  
**阶段：** QA

### 验收路径

1. 打开首页。
2. 输入真实 URL。
3. 点击 Run assessment。
4. 看到 loading。
5. 看到真实结果。
6. 检查 evidence。
7. 填写 full diagnostic 表单。
8. 后台出现 lead。
9. lead 里包含 scan snapshot。
10. 管理员收到邮件。

### 验收标准

- 桌面端通过。
- 移动端通过。
- LLM 关闭时通过。
- LLM 开启时通过。
- API 失败时不会卡死。

---

## RS-18. 设计 LLM prompt 模板

**优先级：** P1  
**状态：** Ready  
**类型：** AI / Product  
**阶段：** LLM

### 目标

设计受约束的 prompt，让 LLM 只能基于提取内容生成解释。

### 要求

- 输入 structured scan data。
- 禁止编造未检测到的信息。
- 输出 JSON。
- 输出内容适合 B2B 用户。
- 语气专业、直接、可行动。
- 不承诺“保证 AI 会推荐你”。

### 验收标准

- prompt 明确禁止 hallucination。
- prompt 要求 evidence 必须来自 extracted data。
- prompt 输出 JSON。
- LLM 失败时不影响规则版结果。

---

## RS-19. 增加 LLM provider 配置

**优先级：** P1  
**状态：** Ready  
**类型：** Backend / Config  
**阶段：** LLM

### 目标

在 WordPress theme 中支持配置 LLM API key。

### 建议配置

```php
define('USEGEO_LLM_PROVIDER', 'openai');
define('USEGEO_OPENAI_API_KEY', '...');
define('USEGEO_LLM_MODEL', 'gpt-4o-mini');
```

### 验收标准

- 没有配置 API key 时，系统自动跳过 LLM。
- 配置错误时，不让扫描失败。
- LLM 调用有 timeout。
- 不把 API key 输出到前端。

---

## RS-20. 实现 LLM 报告生成函数

**优先级：** P1  
**状态：** Ready  
**类型：** Backend / AI  
**阶段：** LLM

### 目标

新增函数：

```php
usegeo_generate_llm_scan_summary($scan_data)
```

### 输出

- summary
- top findings
- recommendations
- audit recommendation

### 验收标准

- LLM 正常时返回结构化 JSON。
- LLM 超时或 JSON 解析失败时返回 `available: false`。
- 后端日志记录失败原因，但不暴露给用户。
- LLM 结果不能覆盖规则评分，只能补充解释。

---

## RS-21. 增加 LLM 内容安全过滤

**优先级：** P1  
**状态：** Ready  
**类型：** Backend / Safety  
**阶段：** LLM

### 目标

避免 LLM 生成过度承诺或不可验证结论。

### 需要避免

- `Your brand will rank higher in ChatGPT`
- `Guaranteed AI visibility`
- `Claude will recommend you`
- `You are invisible to all AI systems`

### 推荐措辞

- `may improve`
- `likely helps`
- `appears weak`
- `based on the homepage signals we detected`
- `this scan is directional, not a full visibility test`

### 验收标准

- 报告文案不承诺保证结果。
- 报告清楚说明这是 directional scan。
- 不把轻量 scanner 伪装成完整 AI visibility tracking。

---

## RS-22. 更新 LLM summary 展示

**优先级：** P1  
**状态：** Ready  
**类型：** Frontend / UX  
**阶段：** 前端

### 目标

如果 API 返回 `llm_summary.available = true`，展示 LLM 生成的诊断摘要。

如果不可用，展示规则版 summary。

### 验收标准

- LLM 可用时，用户看到更自然的解释。
- LLM 不可用时，页面仍然完整。
- 不显示 `AI error` 这种内部错误。
- CTA 仍然指向 full diagnostic。

---

## RS-23. 邮件通知增加 scan summary

**优先级：** P1  
**状态：** Backlog  
**类型：** Backend / Ops  
**阶段：** 运营

### 目标

新 lead 邮件里加入 scan 摘要，帮助快速判断是否值得跟进。

### 建议内容

```text
Website: ...
GEO score: 64
Weakest dimension: Comparison readiness
Top finding: No comparison or alternative language detected.
Recommended next step: Manual GEO Audit likely useful.
```

### 验收标准

- 收到邮件就能判断是否值得跟进。
- 邮件不包含超长 JSON。
- 邮件仍为纯文本，避免投递问题。

---

## RS-24. 后台 lead 列表显示 scanner 分数

**优先级：** P1  
**状态：** Backlog  
**类型：** WordPress Admin  
**阶段：** 运营

### 目标

在 `UseGEO Leads` 后台列表中增加 scanner 信息列。

### 建议列

- Scanner URL
- Score
- Recommended action
- Submitted at

### 验收标准

- 能快速判断哪个 lead 更值得跟进。
- 不需要进入每条详情页才能看到基本情况。

---

## RS-25. 增加扫描结果缓存

**优先级：** P1  
**状态：** Backlog  
**类型：** Backend / Performance  
**阶段：** 性能

### 目标

同一个 URL 在短时间内重复扫描时，使用缓存结果。

### 建议方案

- URL hash 作为 key。
- TTL：6-24 小时。
- LLM 结果也缓存，避免重复 API 成本。

### 验收标准

- 同 URL 重复扫描响应更快。
- API 成本下降。
- 缓存不会影响用户留资保存。

---

## 6. Definition of Done

Real Scanner v1 完成时必须满足：

1. 用户输入网站后，后端真实抓取该网站首页。
2. 返回结果中能看到真实 title / H1 / schema / detected signals。
3. 分数不再依赖 URL 长度。
4. 至少有 5 个维度评分。
5. 至少有 3 条基于真实信号的 findings。
6. LLM 可用时生成自然语言诊断。
7. LLM 不可用时规则诊断仍然可用。
8. 用户提交 lead 后，后台保存本次 scan snapshot。
9. 系统有基本 URL 安全防护和 rate limit。
10. 桌面和移动端主路径都能跑通。

---

## 7. 后续 Backlog

Real Scanner v1 之后可以考虑：

- 多页面扫描
- 手动输入竞品并对比
- Manual GEO Audit 付费页
- 报告导出
- 历史扫描
- dashboard
- AI answer / prompt visibility monitoring

这些不进入当前看板。