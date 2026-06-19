# 产品需求文档 (PRD): UseGEO

> 本文是**产品意图的唯一来源**，仅人工维护意图部分；Agent 只读。
> 例外：`writeback-agent` 可回填功能锚点的「关联 CR」行（该行不参与哈希）。
>
> 本文给人看：用于规划、评审、把握产品全貌。
> 它**不是** AI 的行动契约——AI 干活读的是 `change-requests/CR-*/acceptance-spec.md`。
> 因此本文**不写**带 ID 的强制项 / 字段结构 / 禁改清单 / 测试用例，那些归 acceptance-spec。
>
> 单文件原则：始终只用 `docs/PRD.md` 单文件，不拆 `docs/prd/` 目录。

## 元信息

| 字段 | 值 |
|---|---|
| 版本 | v1.0 |
| 状态 | 草稿 |
| 负责人 | Kevin |
| 最后更新 | 2026-06-19 |

---

# 第一部分：产品叙事

> 纯人类视角。**不参与哈希锚定**——修改这一部分不会触发任何 CR 对账。

## 1. 背景与问题

B2B 用户的发现行为正在向 ChatGPT、Claude、Gemini、Perplexity 等 AI 工具迁移。许多 B2B 品牌即使已经被搜索引擎索引，也不会被 AI 在生成式回答中引用、推荐或点名。

UseGEO 想解决的核心问题：

- 增长团队对“GEO / AI visibility”的焦虑很泛，不知道该看什么、怎么改、值不值得投入。
- 现有市面工具要么偏 SEO 老视角，要么是黑箱评分，缺少“可解释、可执行、可信任”的轻量诊断。

UseGEO 的定位：把模糊的 GEO 焦虑，变成具体的网站信号缺口和可执行行动建议。

## 2. 目标与非目标

- **目标**：
  - 提供面向 B2B 增长团队的 GEO readiness 诊断入口，从“免费 Scanner snapshot → Full Diagnostic → 付费 Manual GEO Audit / Fix Sprint / Visibility Monitoring”形成清晰转化路径。
  - 通过教育内容、案例库和 ROI 工具，建立 UseGEO 在 GEO/AI visibility 上的专业可信度。
  - 用结构化数据沉淀 leads 和社区案例，作为后续付费服务和案例库的内容资产。
- **非目标（明确不做）**：
  - 不做完整 SaaS 监控平台（不做账号体系、历史报告 dashboard、周期监控）。
  - 不声称真实抓取 ChatGPT/Claude/Gemini 的推荐排名。
  - 不做企业级权限管理或重型 CRM 自动化。
  - 不取代 Manual GEO Audit 服务，scanner 只是诊断入口。

## 3. 目标用户与场景

- **用户角色**：B2B SaaS / Agency-Consultancy / Marketplace / Media / Ecommerce 的市场负责人、增长 / SEO 负责人、创始人。
- **典型场景**：
  - 用户被“GEO / AI 推荐”话题吸引，想先免费看一眼自己的网站“在 AI 视角下长什么样”。
  - 在阅读 GEO 相关内容时，临时想运行 Scanner 评估自家网站。
  - 已经看过免费分数，认真考虑是否值得申请 Full Diagnostic 或 Manual GEO Audit。
  - 内部团队在做 GEO 内部分享，参考 UseGEO 文章和案例。
- **C 端 / B 端定性**：以 B 端 SMB / 中型团队为主，单人/小团队决策。

## 4. 成功指标

| 指标 | 目标值 | 说明 |
|---|---|---|
| Free Scanner → Full Diagnostic 表单提交率 | 待定（建议先采集基线） | 衡量首页转化路径是否真在产生 lead |
| Full Diagnostic Lead → 付费咨询/审计转化率 | 待定 | 衡量销售跟进路径有效性 |
| 文章页 → Scanner 跳转率 | 待定 | 衡量内容是否真把读者带回核心转化路径 |

## 5. 全局约束

> 全局非功能需求、技术红线、合规要求**不在此复制**，统一见：
> - 约束规则：`docs/rules.md`
> - 技术决策红线：`docs/decisions.md`
>
> 此处只放指针，避免与上述文件形成双源。

---

# 第二部分：功能锚点

> 每个 `## [PRD-XXX]` 章节 = 一个**功能锚点**，是 CR 的挂载点。
> **每个锚点章节单独参与哈希**（用于 PRD↔CR 对账），但「关联 CR」行**排除在哈希之外**。
> 锚点 ID 一旦使用永不修改；废弃标 `(已废弃)`，ID 保留。

## [PRD-LEGACY-BASELINE] 老项目逆向基线

- **状态**: reverse-engineered
- **优先级**: P1

**意图**
> 把当前 UseGEO 仓库里已经存在的 WordPress 主题代码，逆向成一份“整站基线”需求记录，作为后续所有正向 CR 的对照基线。

**用户故事**
> 作为 Kevin（产品负责人），我希望对当前老代码里的真实业务行为有一份经过人工裁决的需求清单，以便在后续每一次正向开发中清楚地知道哪些行为是“需求”、哪些是 bug 或技术债，避免 AI 把当前实现细节当成永久契约。

**功能描述**
> 该锚点不是一个新功能，而是一份对 D:\Code\UseGEO\wordpress\theme\usegeo 全部主题代码的逆向裁决基线。基线覆盖站点基础体验、首页转化路径、免费 Scanner、线索表单、社区案例投稿、文章学习库、后台运营管理与站点分析等模块的最小行为承诺。基线本身不再扩展；后续具体功能演进通过其它锚点（PRD-FREE-SCANNER、PRD-REAL-SCANNER-V1、PRD-LEAD-CAPTURE 等）单独驱动。

**验收意图**（粗粒度，给人看）
> 当前老代码里被人工确认/修正为真需求的行为，被汇总到 CR-LEGACY-SCAN 的 acceptance-spec 中；被判定为 bug/实现细节的行为，被记录到 known-deviations，不固化为需求。

**绝不能发生**（粗粒度负例）
> 绝不允许把 functions.php 加载顺序、index.php 兜底模板、本地 seed 内容等实现细节当成需求固化；绝不允许 reject 的条目重新被静默改回 confirm。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-SITE-FOUNDATION] 站点基础与全局体验

- **状态**: reverse-engineered
- **优先级**: P1

**意图**
> 提供 UseGEO 全站统一的页头、页脚、导航、静态页面、搜索、404 等基础体验，保证内容、转化和合规链路有稳定底座。

**用户故事**
> 作为访问 UseGEO 的访客，我希望在任何页面都能找到品牌、主导航、合规链接和搜索/恢复入口，以便顺畅地进入 Scanner、文章、案例和服务介绍。

**功能描述**
> 站点基础层包含：统一页头（品牌入口、主导航、移动端菜单、必要 head 元信息）、统一页脚（品牌简述、合规链接、可选社交渠道）、标准 WordPress 页面模板用于 About / Privacy Policy / Cookie Policy 等静态内容、搜索结果页、友好的 404 恢复路径，以及主题级 setup（文本域、菜单注册、HTML5 输出等）。

**验收意图**
> 全站任何页面都有统一页头页脚；导航能引导到 Scanner、Articles、Cases、About 等核心路径；缺失内容、搜索无结果、跳转失败都有明确恢复入口。

**绝不能发生**
> 绝不允许 404 / 静态页面静默无返回路径；绝不允许全站缺失 wp_head / wp_footer，导致追踪/插件失效。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-HOMEPAGE-CONVERSION] 首页核心转化路径

- **状态**: reverse-engineered
- **优先级**: P0

**意图**
> 让首页成为 UseGEO 的主转化落地页，把访客从“GEO 焦虑”引导到“运行 Scanner → 看证明 → 提交线索 / 继续学习”的清晰路径。

**用户故事**
> 作为对 GEO 感兴趣的 B2B 增长负责人，我希望首页一眼看清 UseGEO 是做什么的、能给我什么、下一步该点哪里，以便决定是运行 Scanner、阅读文章，还是直接申请 Manual Audit。

**功能描述**
> 首页应清晰呈现：Hero 价值主张、免费 Scanner 入口、服务（Manual Audit / Fix Sprint / Monitoring 或等价层级）说明、方法论、样例报告、价值主张、学习路径、内容入口、社区入口、案例入口和 ROI 估算模块；关键模块需保留可跳转锚点，让任意位置 CTA 都能引回 Scanner 主路径。

**验收意图**
> 用户在首页能在不滚动太久的范围内理解“UseGEO 是什么 / 给我什么 / 我下一步该做什么”；任何 CTA 均能落到合理的下一步动作（Scanner、文章、Cases、Services、Sample Report 等）。

**绝不能发生**
> 绝不允许首页变成纯展示页——必须保留 Scanner、Content、Community、Cases 等转化锚点；绝不允许 ROI / Sample Report 等模块被包装成确定性收益承诺。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-FREE-SCANNER] 免费 GEO Scanner（当前模拟版）

- **状态**: reverse-engineered
- **优先级**: P0

**意图**
> 提供免费的、面向 B2B 网站的 GEO readiness snapshot，作为整个产品的核心转化资产和价值证明工具。

**用户故事**
> 作为访客，我希望输入公司网站后立即看到一个 GEO 可见性方向性评估，包括分数、关键发现和下一步建议，以便判断要不要进一步申请 Full Diagnostic。

**功能描述**
> 当前 scanner 通过前端模型展示 GEO readiness 分数、ChatGPT/Claude/Gemini 维度分、信号雷达图、关键发现、下一步建议，以及 Full Diagnostic 表单引导（携带 scanner_query 一起提交）。结果必须明确为方向性诊断，避免被误解为真实实时的全网扫描。

**验收意图**
> 用户输入网站后能在合理时间内看到结构化结果；结果有可解释的维度和发现，不是纯营销分数；用户可以无登录获得免费结果；结果能自然引导到 Full Diagnostic 表单。

**绝不能发生**
> 绝不允许文案宣称 scanner 已经检测了 ChatGPT/Claude/Gemini 是否在推荐用户品牌；绝不允许把估算结果包装成确定性数据；绝不允许 scanner 与 lead 表单脱钩，丢失 scanner_query。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-REAL-SCANNER-V1] Real Scanner v1：真实诊断升级

- **状态**: confirmed
- **优先级**: P0

**意图**
> 把 Free Scanner 从“前端模拟评分”升级为“基于真实网页内容的轻量 GEO 诊断”，让分数和建议有可解释的真实证据，从而支撑更可信的 lead 转化和 Manual GEO Audit 销售。

**用户故事**
> 作为 B2B 市场 / 增长 / SEO 负责人，我希望输入公司网站后系统真实抓取首页内容，提取基础 GEO 信号，并基于真实页面给出可解释的诊断，以便我决定是否值得申请 Full Diagnostic / Manual GEO Audit。

**功能描述**
> 后端提供 Scanner API（含 SSRF 防护和 rate limit），抓取首页 HTML，提取 title / meta description / H1-H2 / canonical / robots / JSON-LD schema / 内链与关键词信号；规则模型生成 5 维度评分（Entity clarity / Content structure / Proof signals / Comparison readiness / AI extractability）；可选 LLM 解释层基于真实提取结果生成可读诊断；LLM 不可用时降级为规则版诊断仍然可用。lead 提交时保存 scan snapshot 供运营跟进。详细产品需求另见项目内 `docs/product/usegeo-real-scanner-v1-prd.md`，本锚点不重复其全部细节。

**验收意图**
> 用户在首页运行 Scanner 后获得的分数、发现、建议都来自对其真实首页内容的轻量分析；结果显式标注为方向性诊断；scan snapshot 与 lead 一同保存，方便人工跟进。

**绝不能发生**
> 绝不允许 Real Scanner v1 在没有真实抓取或解析失败时沉默地用模拟分数蒙混过关；绝不允许把规则评分误描述为真实 ChatGPT/Claude/Gemini 推荐排名追踪；绝不允许 SSRF / 滥用接口的输入未做防护。

**关联 CR**:

---

## [PRD-LEAD-CAPTURE] 线索表单与提交安全

- **状态**: reverse-engineered
- **优先级**: P0

**意图**
> 把 Scanner / Full Assessment 转化路径上的线索安全、可靠地保存到后台，并确保表单提交不会因为安全或邮件问题导致静默失败。

**用户故事**
> 作为有需求的访客，我希望填完 Full Diagnostic 表单后能明确知道提交成功/失败/部分成功的状态；作为运营，我希望所有提交都被结构化保存到后台并尽可能通知到管理员邮箱。

**功能描述**
> 公开线索表单只接受 POST，必须通过 nonce 或同源校验；服务端校验邮箱、必填字段、枚举选项、scanner_query 等关键输入；提交成功后写入 `usegeo_lead` 自定义内容类型及元数据；尝试通知管理员邮箱并记录通知状态；邮件失败时以 success_no_mail 状态返回，保留 lead；所有失败/校验问题都有明确可追踪状态。

**验收意图**
> 用户提交成功/失败/部分成功都有明确反馈；后台中能看到每一条 lead 及其通知状态；表单不会被外站伪造提交；邮件失败不会丢线索。

**绝不能发生**
> 绝不允许 nonce 校验缺失或同源校验缺失；绝不允许邮件失败导致 lead 丢失；绝不允许把当前所有字段名 / 枚举选项当成永久不变的硬契约。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-COMMUNITY-CASE] 社区案例投稿与编辑审核

- **状态**: reverse-engineered
- **优先级**: P1

**意图**
> 通过结构化的社区案例投稿入口，沉淀真实 GEO 执行案例，作为未来案例库、playbook 和付费产品的证明资产来源。

**用户故事**
> 作为愿意分享 GEO 经验的用户，我希望提交一个结构化的案例并知道它会进入编辑审核而不是直接公开；作为运营，我希望所有投稿都集中在后台进行审核、标记 review_status 和 processing_lane。

**功能描述**
> 首页提供社区案例投稿入口；表单收集公司类型、行业、阶段、核心问题、采取动作、结果、经验教训等结构化字段，以及匿名化选项；提交后写入 `usegeo_case` 自定义内容类型，默认 review_status = pending_review、processing_lane = unassigned；后台按 review_status / processing_lane / editorial_notes 进行人工审核与分流；首页 case-preview 仅展示 review_status = approved 的案例。

**验收意图**
> 投稿能在后台被结构化管理；审核状态能影响首页是否展示；匿名选项被尊重，不会暴露不该公开的投稿者信息；首页案例展示仅限已审核内容。

**绝不能发生**
> 绝不允许未审核 / 拒绝的投稿直接出现在首页；绝不允许匿名选项被忽略；绝不允许把当前所有 lane 名称、字段集合当成永久不变的硬契约。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-CONTENT-LIBRARY] 文章学习库

- **状态**: reverse-engineered
- **优先级**: P1

**意图**
> 用结构化的 GEO 教育内容支撑 UseGEO 的专业可信度，并把读者从“阅读内容”自然引导回“运行 Scanner”的核心转化路径。

**用户故事**
> 作为正在了解 GEO 的访客，我希望读到结构化的教育文章、清晰的学习路径，并在阅读过程中被自然提醒可以回到 Scanner 评估自家网站。

**功能描述**
> 提供文章详情页（标题、分类、阅读时间、摘要、正文、TOC、Hero / 侧边栏 / 中段 / 底部 CTA、上一篇/下一篇/相关文章推荐），首页提供 content-hub 文章入口和 learning-path 学习路径；侧边栏 / 文中可复用 sidebar-cta 组件把读者引回 Scanner。

**验收意图**
> 文章页阅读体验顺畅；TOC 在长文中可用；任何文章页都至少有一个明确的“运行 Scanner”入口；首页有清晰的内容入口模块和学习路径。

**绝不能发生**
> 绝不允许文章页变成纯阅读页面，缺失任何转化 CTA；绝不允许相关文章 / TOC / CTA 的具体 HTML 结构被当成永久契约。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-OPS-ADMIN] 后台运营管理

- **状态**: reverse-engineered
- **优先级**: P1

**意图**
> 让 UseGEO 的销售跟进和案例编辑流程能在 WordPress 后台高效完成，把 lead 价值和案例审核结果转化为可执行的运营动作。

**用户故事**
> 作为 Kevin / 运营，我希望在后台一眼分辨高意向 lead，并能对社区案例做 review_status / processing_lane / editorial_notes 等审核动作。

**功能描述**
> 后台 lead 列表展示邮箱、公司类型、规模、帮助意图、支持意向、行动时间、scanner_query 和通知状态，并对高意向字段做视觉标记；后台 case 列表展示联系邮箱、行业、review_status、processing_lane、是否匿名等核心字段；案例详情提供 review_status / processing_lane / editorial_notes 编辑界面。

**验收意图**
> 后台能服务于真实的销售跟进和编辑工作流；高意向 lead 在列表中容易被识别；案例审核状态能直接驱动首页展示和后续运营动作。

**绝不能发生**
> 绝不允许把当前 lane 名称 / 字段展示样式 / PHP 实现细节当成永久契约；绝不允许后台编辑动作绕过 nonce / 权限校验。

**关联 CR**: CR-LEGACY-SCAN

---

## [PRD-ANALYTICS] 站点分析与追踪

- **状态**: reverse-engineered
- **优先级**: P2

**意图**
> 让 UseGEO 能够稳定收集页面访问、CTA 点击、Scanner / 表单等关键转化行为数据，支撑成功指标和后续优化。

**用户故事**
> 作为 Kevin，我希望能基于 GA4 或 GTM 看到 UseGEO 主路径上的关键事件，并保留按需扩展自定义事件的空间。

**功能描述**
> 主题资源加载层支持配置 GTM 或 GA4，并在已有 GTM 时避免重复注入 GA4；前端 tracking.js 负责主关键事件的埋点；资源版本随文件变化自动更新，避免缓存导致旧脚本生效。

**验收意图**
> 站点能在生产环境根据配置启用 GTM 或 GA4；同一页面不会出现 GTM 和 GA4 重复统计；前端追踪脚本部署后能稳定上报核心事件。

**绝不能发生**
> 绝不允许在同一页面同时启用 GTM 和 GA4 直接 tag 注入；绝不允许把当前文件名、Google Fonts URL、enqueue handle 等实现细节当成不可变需求。

**关联 CR**: CR-LEGACY-SCAN

---

# 第三部分：演进

> 人在这里做规划。

## 里程碑 / 路线图

| 里程碑 | 包含锚点 | 计划切出的 CR | 状态 |
|---|---|---|---|
| M1 老项目基线确认 | PRD-LEGACY-BASELINE | CR-LEGACY-SCAN | 进行中 |
| M2 Real Scanner v1 真实诊断 | PRD-REAL-SCANNER-V1, PRD-FREE-SCANNER, PRD-LEAD-CAPTURE | （待切 CR） | 计划中 |
| M3 转化与内容优化 | PRD-HOMEPAGE-CONVERSION, PRD-CONTENT-LIBRARY, PRD-COMMUNITY-CASE, PRD-OPS-ADMIN | （待切 CR） | 计划中 |
| M4 分析与运营基线 | PRD-ANALYTICS, PRD-OPS-ADMIN, PRD-SITE-FOUNDATION | （待切 CR） | 计划中 |

## 变更记录

> 只记录**功能锚点**（第二部分）的意图变更——这类变更会触发挂载 CR 的对账。
> 第一部分叙事的修改不必登记。

| 日期 | 改动锚点 | 摘要 | 触发对账的 CR |
|---|---|---|---|
| 2026-06-19 | 全部 | 从 DeliverHQ 模板初始化为 UseGEO 实际 PRD，建立 9 个功能锚点 + 1 个 Legacy 基线锚点。 | CR-LEGACY-SCAN |
