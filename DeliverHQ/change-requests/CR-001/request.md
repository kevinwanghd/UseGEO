# Request: Real Scanner v1 真实诊断升级

> Change Request 需求输入文档。由需求方/产品经理填写。

## CR-ID
CR-001

## 提出人
Kevin

## 提出日期
2026-06-19

## 来源
- PRD 锚点：`PRD-REAL-SCANNER-V1`（confirmed，UseGEO 路线图 M2）
- 项目内已有草稿 PRD：`docs/product/usegeo-real-scanner-v1-prd.md`
- 整站基线约束：`DeliverHQ/change-requests/CR-LEGACY-SCAN/acceptance-spec.md`

## 需求背景

UseGEO 当前首页已经具备完整的 GEO scanner 交互壳：用户输入 URL，前端展示加载态，前端生成评分、雷达图、结论，并通过 Full Diagnostic 表单收集线索。

但当前 scanner 的核心评分逻辑是**前端模拟**：

- 不会真实抓取用户网站；
- 不会解析页面内容；
- 不会检查 title / description / H1 / schema / proof / comparison 等 GEO 信号；
- 分数主要来自 URL 字符串特征。

这对早期 MVP 转化验证可以接受，但要进入更可信的商业化阶段，scanner 必须从“演示型工具”升级为“真实轻量评估工具”。否则 Scanner 会持续退化为“营销假分数”，与 PRD-FREE-SCANNER 中“不得变成纯营销假分数”的约束冲突，也无法支撑 PRD-LEAD-CAPTURE 的高质量线索沉淀。

## 业务目标

让用户输入公司网站后，**系统真实抓取首页内容、提取基础 GEO 信号、用规则模型生成可解释评分，再用 LLM 生成可读的诊断解释**，从而支撑：

- 更可信的 free → full diagnostic 转化（PRD-FREE-SCANNER / PRD-LEAD-CAPTURE）
- 更高质量的 lead snapshot，支持人工销售跟进（PRD-OPS-ADMIN）
- 后续 Manual GEO Audit / Fix Sprint / Visibility Monitoring 服务的可信入口（PRD-HOMEPAGE-CONVERSION → services / sample-report）

不在本 CR 范围内的目标：

- 不做完整 SaaS dashboard 或账号体系；
- 不做真实 ChatGPT/Claude/Gemini 推荐排名追踪；
- 不做多页面爬虫或竞品自动识别；
- 不做 PDF 报告导出。

## 功能描述

### 用户故事

> 作为一个 B2B 市场负责人 / 创始人 / SEO 负责人，
> 我想输入我的公司网站地址，获得一个基于真实首页内容的 GEO readiness 诊断，
> 以便判断当前网站是否容易被 AI 系统理解、引用和推荐，并决定是否值得申请 Full Diagnostic / Manual GEO Audit。

### 核心用户路径

1. 用户进入 `usegeo.blog` 首页，看到 free GEO scanner（结构沿用 PRD-FREE-SCANNER）。
2. 用户输入公司网站 URL，点击 Run assessment。
3. 前端调用后端 API（`POST /wp-json/usegeo/v1/scan`）。
4. 后端：
   1. 校验 URL 与 SSRF 边界；
   2. 真实抓取该 URL 的首页 HTML；
   3. 解析 title / meta description / H1 / H2 / canonical / robots / JSON-LD schema / 内链与关键词信号；
   4. 用规则模型生成 5 维度评分：Entity clarity / Content structure / Proof signals / Comparison readiness / AI extractability；
   5. 生成基于真实信号的 findings 列表；
   6. 可选调用 LLM 解释层，生成自然语言诊断；
   7. LLM 不可用时仍返回规则版结果，不阻塞用户。
5. 前端展示：
   - 总分、5 维度分、雷达图（沿用 PRD-FREE-SCANNER 的展示要求）；
   - 至少 3 条基于真实信号的 findings；
   - LLM summary 或 fallback 的规则版 summary；
   - Evidence 区，至少展示 page title、H1、schema、proof signals、comparison signals、word count 等真实提取信号；
   - 明确文案：directional GEO readiness snapshot，不声称 AI 推荐结果。
6. 用户填写 Full Diagnostic 表单：
   - 沿用 PRD-LEAD-CAPTURE 现有字段与安全约束；
   - 提交时携带 `scanner_query` 与本次 scan snapshot；
   - 后端写入 `usegeo_lead` 时，附加 scan snapshot 的关键 post meta；
   - 邮件通知中包含核心分数与建议下一步。
7. 后台 lead 列表展示分数、最弱维度等关键信息（沿用 PRD-OPS-ADMIN 的列与视觉规则，不破坏现有运营字段）。

### 与 Real Scanner v1 草稿 PRD 的关系

本 CR 的功能范围以 `docs/product/usegeo-real-scanner-v1-prd.md`（v1.0 Draft）的第 6–14 章为基础，包括：

- API 设计（POST /wp-json/usegeo/v1/scan，请求/响应/错误结构）；
- 5 维度评分定义；
- LLM 角色与降级；
- 前端真实 API 接入；
- Lead 表单 scan snapshot 保存。

但**所有具体字段、错误码、UI 文案、维度阈值、API JSON shape 均不在本 request 中固化**，由 acceptance-spec 阶段在 SDD 三段（Data / Interface / Behavior）中具体落定，避免 request 阶段越界写成实现契约。

## 非功能需求

- **性能**：
  - 单次 scan 用户感知耗时 ≤ 15s（含抓取 + 规则 + LLM）；
  - LLM 失败/超时时，整体响应不应超过 12s；
  - 后端抓取本身 timeout 8–12s，最多 3 次 redirect。
- **安全（强制）**：
  - URL 抓取必须具备 SSRF 防护（拒绝 localhost / 127.0.0.1 / 0.0.0.0 / ::1 / 10.x / 172.16-31.x / 192.168.x、`file://` / `ftp://` 等非 http/https 协议）；
  - 限制响应体大小（建议 ≤ 2MB）与 content-type（仅 HTML）；
  - 不执行远程 JavaScript，不下载二进制；
  - 必须设置明确 User-Agent；
  - 表单与 API 调用沿用 PRD-LEAD-CAPTURE 中的 nonce / 同源校验约束。
- **可用性 / 健壮性**：
  - LLM API key 缺失或调用失败 → 规则版结果仍可用，前端不卡死 loading；
  - 抓取失败 / URL 非法 / rate limit → 明确分类错误（invalid_url / blocked_url / fetch_timeout / fetch_failed / non_html_response / parse_failed / rate_limited），用户看到的提示必须是友好文案，不暴露 PHP warning、stack trace、服务器路径。
- **合规与定位**：
  - Scanner 必须明确标注为 directional GEO readiness scan / homepage signal scan，**不得**声称已检测 ChatGPT / Claude / Gemini 推荐结果；
  - 文案需符合 PRD-FREE-SCANNER 中“避免营销假分数”的硬约束。
- **rate limit**：
  - 单 IP / 单会话需要基础 rate limit（具体阈值在 acceptance-spec 中定义），防止滥用；
  - 触发限流时返回 `rate_limited` 错误，前端展示稍后重试提示。
- **可观测性**：
  - lead 创建必须保留 scan_completed_at、scan 主要分数、findings 关键摘要、LLM 是否启用，方便后续运营和问题排查；
  - 关键错误（抓取失败、LLM 失败）应可在服务器日志或 WP debug log 中定位（避免静默失败）。

## 约束条件

- **运行环境**：WordPress 主题运行于现有站点（参见 PRD-SITE-FOUNDATION / PRD-ANALYTICS），不引入额外重型依赖（不引入完整爬虫栈）。
- **代码组织**：新增能力以 WordPress 主题或 mu-plugin 形式落地；不破坏 `wordpress/theme/usegeo` 现有结构与 PRD-LEGACY-BASELINE 中的整站基线行为。
- **配置**：LLM provider、API key、rate limit、超时、阈值等必须可通过 WP 常量 / option / filter 等方式配置，不得硬编码到代码里。
- **回退**：如果 v1 上线后稳定性问题严重，必须能在不删数据的情况下回到当前“前端模拟分数”体验（保留前端旧逻辑或开关）。
- **数据迁移**：本 CR 不强制对老 lead 做数据迁移；老 lead 没有 scan snapshot 是允许的。
- **不动**：
  - 不修改 `forms.php` 中已有的 case submission 流程（属于 PRD-COMMUNITY-CASE，归 P1 而非本 CR）；
  - 不修改后台 case 审核界面（PRD-OPS-ADMIN 的 case 部分）。
- **PRD 一致性**：本 CR acceptance-spec 必须 `derived_from: prd_section: PRD-REAL-SCANNER-V1`；变更若涉及 PRD-FREE-SCANNER / PRD-LEAD-CAPTURE / PRD-OPS-ADMIN 的对外承诺，需要回写或新增对应锚点，不得绕过 PRD。

## 优先级

P0

## 验收标准（高层）

> 详细 SDD 验收条件由 acceptance-spec 落定。这里只列“需求方视角”的高层验收。

1. 用户在首页输入合法公开网站后，能在合理时间内得到一份基于真实首页内容的 GEO readiness 结果，结果可解释、可追溯到真实信号。
2. 评分不再依赖 URL 长度等纯字符串特征。
3. 结果包含至少 5 个维度分（Entity clarity / Content structure / Proof signals / Comparison readiness / AI extractability）和至少 3 条基于真实页面信号的 findings。
4. LLM 可用时给出自然语言 summary 与 top recommendations；LLM 不可用时仍能返回规则版完整结果，并在前端展示，不卡死。
5. 用户提交 Full Diagnostic 表单后，后台 lead 必须保存关键 scan snapshot，且邮件通知能体现核心分数 / 最弱维度 / 推荐下一步。
6. 安全边界稳定：localhost / 内网 IP / 非 http/https 协议 / 非 HTML 响应 / 超大文件均被拒绝，并返回友好错误。
7. 文案不得宣称真实 AI 推荐结果或 ChatGPT/Claude/Gemini 排名追踪；scanner 结果须显式标注为 directional 诊断。
8. 不破坏整站基线（CR-LEGACY-SCAN 的 24 条 acceptance）：首页结构、表单安全、后台 lead 字段、tracking、case 提交流程仍按基线工作。

## 附件

- PRD 草稿：`D:\Code\UseGEO\docs\product\usegeo-real-scanner-v1-prd.md`
- PRD 锚点：`D:\Code\UseGEO\DeliverHQ\docs\PRD.md` 中的 `## [PRD-REAL-SCANNER-V1]`
- 已有 taskboard：`D:\Code\UseGEO\docs\engineering\usegeo-real-scanner-v1-taskboard.md`（仅参考，不作为 SDD 真相源）
- 当前 scanner 实现：
  - `wordpress/theme/usegeo/template-parts/scanner/scanner.php`
  - `wordpress/theme/usegeo/assets/js/scanner.js`
  - `wordpress/theme/usegeo/inc/forms.php`（lead capture）
  - `wordpress/theme/usegeo/inc/post-types.php`（usegeo_lead）
  - `wordpress/theme/usegeo/inc/admin.php`（lead 后台展示）
