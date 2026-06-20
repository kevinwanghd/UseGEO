# UseGEO Real Scanner v1 产品需求文档（PRD）

## 1. 文档信息

- 项目名称：`usegeo.blog`
- 功能名称：`Real Scanner v1`
- 文档类型：`Product Requirements Document`
- 当前版本：`v1.0`
- 文档状态：`Draft`
- 目标阶段：从“模拟 GEO scanner”升级为“可信的轻量 GEO 诊断工具”
- 适用对象：产品 / 设计 / WordPress 开发 / 后端开发 / 运营跟进

---

## 2. 背景

当前 `usegeo.blog` 首页已经具备一个完整的 GEO scanner 交互壳：

1. 用户输入网站 URL。
2. 前端展示加载态。
3. 前端生成评分、雷达图、结论。
4. 用户可提交 full diagnostic 表单。
5. 后台保存 lead。

但当前 scanner 的核心评分逻辑仍然是前端模拟：

- 不会真实抓取用户网站。
- 不会解析网页内容。
- 不会检查 title、description、H1、schema、proof、comparison 等 GEO 信号。
- 不会基于真实页面内容生成诊断。
- 分数主要来自 URL 字符串特征。

这对早期 MVP 转化验证可以接受，但如果网站要进入更可信的商业化阶段，scanner 必须从“演示型工具”升级为“真实轻量评估工具”。

---

## 3. 产品目标

`Real Scanner v1` 的目标是：

**让用户输入公司网站后，系统真实抓取首页内容，提取基础 GEO 信号，用规则模型生成评分，再用 LLM 生成可读的诊断解释，从而支撑更可信的 lead 转化和 Manual GEO Audit 销售。**

本阶段不是做完整 GEO SaaS，也不是做真实 AI 推荐排名追踪。

本阶段要做到：

1. 结果基于真实网页内容。
2. 用户能看到诊断证据。
3. 分数和建议有可解释来源。
4. LLM 解释受真实提取内容约束。
5. LLM 不可用时，规则诊断仍然可用。
6. lead 后台能保存 scan snapshot，方便人工跟进。

---

## 4. 一句话定义

**Real Scanner v1 是一个面向 B2B 网站的轻量 GEO readiness scan：真实检查首页是否具备让 AI 系统理解、引用和推荐的基础信号。**

---

## 5. 用户故事

作为一个 B2B 市场负责人 / 创始人 / SEO 负责人，

我想输入我的公司网站地址，获得一个基于真实网页内容的 GEO readiness 诊断，

以便我知道当前网站是否容易被 AI 系统理解、引用和推荐，并判断是否值得申请完整人工审计。

---

## 6. 核心用户路径

1. 用户进入 `usegeo.blog` 首页。
2. 用户在 scanner 输入公司网站 URL。
3. 系统显示真实分析加载态：
   - 正在抓取首页内容
   - 正在提取品牌与内容信号
   - 正在生成 GEO readiness 诊断
4. 后端抓取该 URL 的首页 HTML。
5. 后端解析页面信号：
   - title
   - meta description
   - H1 / H2
   - 页面正文摘要
   - schema.org JSON-LD
   - canonical
   - robots indexability
   - internal links
   - proof / customer / case / pricing / comparison 等关键词信号
6. 系统用规则模型生成 5 个维度分：
   - Entity clarity
   - Content structure
   - Proof signals
   - Comparison readiness
   - AI extractability
7. 系统把提取结果和评分交给 LLM，生成自然语言解释。
8. 用户看到免费诊断结果：
   - 总分
   - 5 个维度分
   - 3 条真实发现
   - 3 条优先建议
   - 是否建议申请 full diagnostic / Manual GEO Audit
9. 用户如果想要完整建议，填写企业邮箱和业务信息。
10. 后台保存 lead，同时保存本次 scan snapshot，方便后续人工跟进。

---

## 7. 功能范围

## 7.1 In Scope

Real Scanner v1 需要包含：

1. WordPress REST API：`POST /wp-json/usegeo/v1/scan`
2. URL 校验与 SSRF 防护
3. 服务端首页 HTML 抓取
4. 基础 HTML 解析
5. JSON-LD schema 解析
6. GEO 信号关键词检测
7. 5 维度规则评分
8. 规则版 findings 生成
9. LLM 解释层
10. LLM 失败降级
11. 前端 scanner 接入真实 API
12. 前端展示真实 evidence
13. lead 表单保存 scan snapshot
14. 后台 lead 保存 scan meta
15. 基础 rate limit
16. 错误分类与友好提示

## 7.2 Out of Scope

本版本不做：

1. 多页面全站爬虫
2. 账号注册 / 登录
3. 历史报告 dashboard
4. 周期性监控
5. 竞品自动识别
6. 真实 ChatGPT / Claude / Gemini 推荐排名追踪
7. Google AI Overview / Perplexity 可见性追踪
8. PDF 报告自动导出
9. 企业级权限管理
10. 复杂 CRM 自动化

---

## 8. 结果定位与合规边界

Real Scanner v1 的结果必须被定位为：

- Directional scan
- GEO readiness snapshot
- Homepage signal scan
- Lightweight diagnostic

不要声称：

- “我们检测了 ChatGPT 是否推荐你”
- “我们保证 AI 会推荐你的品牌”
- “这是完整 AI visibility tracking”
- “你的品牌在所有 AI 系统中不可见”

推荐措辞：

- “Based on homepage signals we detected...”
- “This may improve AI extractability...”
- “Your site appears to have weak comparison signals...”
- “This scan is directional and does not replace a full manual audit.”

---

## 9. 评分维度

## 9.1 Entity clarity

衡量页面是否能清楚表达：

- 这是什么品牌 / 公司
- 属于什么品类
- 服务谁
- 解决什么问题

可参考信号：

- title
- meta description
- H1
- Organization schema
- 清晰的品牌 / 品类 / 服务描述

## 9.2 Content structure

衡量页面结构是否容易被机器抽取和理解。

可参考信号：

- H1 是否存在
- H2 数量
- 正文长度
- section 结构
- internal links
- title / description / H1 是否互补

## 9.3 Proof signals

衡量页面是否提供足够证明，让 AI 系统更容易信任和复述。

可参考信号：

- case study
- customer
- client
- testimonial
- results
- ROI
- review
- trusted by
- 具体数字 / 百分比 / 结果

## 9.4 Comparison readiness

衡量页面是否具备被放入推荐、替代、比较场景的基础。

可参考信号：

- compare
- comparison
- alternative
- alternatives
- versus
- vs
- best
- top
- use cases
- for teams / for agencies / for B2B
- pricing / plans

## 9.5 AI extractability

衡量页面是否容易被爬取、抽取、复述。

可参考信号：

- 页面可 index
- title / meta / H1 完整
- schema 存在
- canonical 正常
- 正文足够清晰
- 没有 noindex

---

## 10. API 设计

## 10.1 请求

Endpoint：

```http
POST /wp-json/usegeo/v1/scan
```

请求体：

```json
{
  "url": "https://example.com"
}
```

## 10.2 成功响应

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
  "findings": [
    {
      "severity": "high",
      "dimension": "comparison_readiness",
      "title": "Comparison coverage appears weak",
      "evidence": "No comparison, alternative, versus, or competitor-related language was detected on the homepage.",
      "recommendation": "Add comparison or alternative pages for high-intent AI recommendation queries."
    }
  ],
  "llm_summary": {
    "available": true,
    "summary": "...",
    "top_recommendations": ["...", "...", "..."],
    "audit_recommendation": "Manual GEO Audit is recommended."
  }
}
```

## 10.3 错误响应

错误类型：

- `invalid_url`
- `blocked_url`
- `fetch_timeout`
- `fetch_failed`
- `non_html_response`
- `parse_failed`
- `rate_limited`

示例：

```json
{
  "ok": false,
  "error": "blocked_url",
  "message": "This URL cannot be scanned. Please enter a public website URL."
}
```

---

## 11. 安全要求

URL 抓取必须具备 SSRF 防护。

必须阻止：

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

抓取限制：

- timeout：8-12 秒
- redirect：最多 3 次
- response size：建议 1MB-2MB
- 只接受 HTML 响应
- 不执行远程 JavaScript
- 不下载大文件
- 设置明确 User-Agent

---

## 12. LLM 要求

## 12.1 LLM 作用

LLM 只负责把规则评分和提取内容解释成更自然、更专业的诊断。

LLM 不负责：

- 覆盖规则评分
- 编造未检测到的信息
- 承诺 AI 推荐结果
- 自动生成无法验证的竞品结论

## 12.2 LLM 输出结构

```json
{
  "summary": "...",
  "top_findings": [
    {
      "title": "...",
      "why_it_matters": "...",
      "evidence": "...",
      "recommended_fix": "..."
    }
  ],
  "top_recommendations": [
    "...",
    "...",
    "..."
  ],
  "audit_recommendation": {
    "recommended": true,
    "reason": "..."
  }
}
```

## 12.3 LLM 降级

如果 LLM 不可用：

- API 仍返回规则评分
- API 仍返回规则 findings
- 前端仍展示完整基础结果
- 不向用户显示内部错误
- `llm_summary.available = false`

---

## 13. 前端要求

当前前端 scanner 需要从“模拟分数”改成“调用真实 API”。

要求：

1. 用户输入 URL 后调用 `/wp-json/usegeo/v1/scan`。
2. loading 状态持续到 API 返回。
3. 成功后展示：
   - 总分
   - 5 维度分
   - radar chart
   - findings
   - LLM summary 或规则 summary
   - evidence 区
4. 失败时展示友好错误。
5. 不再根据 URL 长度生成分数。
6. 提交 lead 时带上 scan snapshot。

Evidence 区建议展示：

- Page title
- H1
- Schema detected
- Word count
- Proof signals detected / not detected
- Comparison signals detected / not detected

---

## 14. Lead 保存要求

用户提交 full diagnostic 表单后，`usegeo_lead` 需要保存 scan snapshot。

建议 post meta：

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

邮件通知建议包含：

- Website
- GEO score
- Weakest dimension
- Top finding
- Recommended next step

---

## 15. 验收标准

## AC1：结果必须基于真实网页内容

Given 用户输入一个公开网站 URL  
When 用户点击 Run assessment  
Then 后端必须真实请求该 URL  
And 返回结果中至少包含该页面的 title、description 或 H1 中的一项真实内容  
And 不再只根据 URL 长度生成分数。

## AC2：结果必须包含证据

Given 系统成功解析网站  
When 用户看到结果  
Then 页面应展示至少 4 项 evidence，例如 title、H1、schema、proof signals、comparison signals、word count。

## AC3：LLM 解释必须受真实提取内容约束

Given 后端已经提取页面信号  
When 调用 LLM 生成诊断  
Then prompt 只能基于提取内容和规则评分生成建议  
And 不允许编造页面中不存在的客户、案例、产品特性或竞品。

## AC4：LLM 失败时 scanner 仍然可用

Given LLM API 超时、报错或没有配置 API key  
When 用户运行 scanner  
Then 系统仍然返回规则评分版本  
And 前端显示基础诊断  
And 不让用户卡死在 loading 状态。

## AC5：留资时必须保存 scan snapshot

Given 用户完成 scanner 并提交 full diagnostic 表单  
When 后台创建 `usegeo_lead`  
Then lead 记录必须保存本次 scan 的关键结果。

## AC6：安全边界必须成立

Given 用户输入 URL  
When 后端抓取页面  
Then 系统必须阻止 localhost、内网 IP、非 http/https 协议、大文件和非 HTML 响应。

## AC7：错误提示必须友好

Given 抓取失败、URL 无效或触发限流  
When 前端收到错误响应  
Then 用户看到清楚、非技术化的提示  
And 不看到 PHP warning、stack trace 或服务器路径。

---

## 16. MVP 完成定义

Real Scanner v1 完成时应满足：

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

## 17. 推荐命名

对外建议使用：

- `Free GEO Readiness Scan`
- `AI Visibility Readiness Snapshot`
- `Homepage GEO Signal Scan`
- `Directional GEO Diagnostic`

不建议使用：

- `AI Visibility Tracking`
- `ChatGPT Ranking Checker`
- `AI Recommendation Detector`

原因：本版本检测的是页面信号 readiness，不是实际 AI 推荐排名。

---

## 18. 后续版本方向

Real Scanner v1 完成后，可以进入：

1. 多页面扫描：about / pricing / case studies / blog / comparison pages。
2. 手动输入竞品并生成对比。
3. Manual GEO Audit 付费入口。
4. 报告保存与导出。
5. 历史扫描与 dashboard。
6. Prompt set / AI answer monitoring。

但这些不进入当前版本。