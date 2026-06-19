---
derived_from:
  prd_section: PRD-LEGACY-BASELINE
  prd_hash: "f80bba225869"
---

# Acceptance Spec: usegeo（逆向生成）

> 由 reverse_to_spec.py 从已人工确认的逆向需求候选转化生成。
> 每条验收条件均经人工裁决确认为真需求（非 bug/技术债）。

## 1. Data Spec

本规格逆向自现有代码，涉及以下模块：

- 模块 `404`：404.php
- 模块 `footer`：footer.php
- 模块 `front-page`：front-page.php
- 模块 `header`：header.php
- 模块 `page`：page.php
- 模块 `search`：search.php
- 模块 `single`：single.php
- 模块 `admin`：inc/admin.php
- 模块 `assets`：inc/assets.php
- 模块 `forms`：inc/forms.php
- 模块 `post-types`：inc/post-types.php
- 模块 `setup`：inc/setup.php
- 模块 `case-preview`：template-parts/cases/case-preview.php
- 模块 `community-intro`：template-parts/community/community-intro.php
- 模块 `content-hub`：template-parts/content-hub/content-hub.php
- 模块 `methodology`：template-parts/conversion/methodology.php
- 模块 `sample-report`：template-parts/conversion/sample-report.php
- 模块 `services`：template-parts/conversion/services.php
- 模块 `sidebar-cta`：template-parts/cta/sidebar-cta.php
- 模块 `hero`：template-parts/hero/hero.php
- 模块 `learning-path`：template-parts/learning/learning-path.php
- 模块 `roi`：template-parts/roi/roi.php
- 模块 `scanner`：template-parts/scanner/scanner.php
- 模块 `value-proposition`：template-parts/value/value-proposition.php

## 2. Interface Spec

- `404`：（见源码）
- `footer`：（见源码）
- `front-page`：（见源码）
- `header`：（见源码）
- `page`：（见源码）
- `search`：（见源码）
- `single`：（见源码）
- `admin`：（见源码）
- `assets`：（见源码）
- `forms`：（见源码）
- `post-types`：（见源码）
- `setup`：（见源码）
- `case-preview`：（见源码）
- `community-intro`：（见源码）
- `content-hub`：（见源码）
- `methodology`：（见源码）
- `sample-report`：（见源码）
- `services`：（见源码）
- `sidebar-cta`：（见源码）
- `hero`：（见源码）
- `learning-path`：（见源码）
- `roi`：（见源码）
- `scanner`：（见源码）
- `value-proposition`：（见源码）

## 3. Behavior Spec

## 验收条件

### 场景 1: 模块 404 的行为
- 验收条件：UseGEO 必须提供友好的 404 页面，明确说明页面不存在或已移动，并提供返回首页、运行 GEO assessment、阅读文章等恢复路径，减少用户流失。具体文案、布局和内联样式不固化为需求。
- 现有实现：404.php
- 裁决备注：404 体验是真需求，但当前实现属于基础模板，可修正后确认。

### 场景 2: 模块 footer 的行为
- 验收条件：UseGEO 全站必须提供页脚区域，包含品牌简述、关键合规/信息链接（至少 Privacy Policy / Cookie Policy / About 等）以及可选社交渠道入口；页脚必须在页面结束前稳定输出，并保留 WordPress `wp_footer()` 以确保插件、脚本和追踪正常工作。
- 现有实现：footer.php
- 裁决备注：footer.php 是全站基础体验和合规入口，应固化为站点级需求；具体社交平台、文案和视觉结构可调整。

### 场景 3: 模块 front-page 的行为
- 验收条件：首页必须作为 UseGEO 的核心转化落地页，按清晰顺序展示 Hero、免费 Scanner、服务说明、方法论、样例报告、价值主张、学习路径、内容入口、社区入口、案例入口和 ROI 模块；Scanner、Content、Community、Cases 等关键模块必须保留可跳转锚点，保证用户能从首页完成“了解价值 → 运行扫描 → 查看证明 → 提交线索/继续阅读”的主路径。
- 现有实现：front-page.php
- 裁决备注：当前 front-page.php 是产品转化路径编排，不只是展示模板，应固化为首页体验需求。

### 场景 4: 模块 header 的行为
- 验收条件：UseGEO 全站必须提供统一页头，包含基础 HTML head 元信息、响应式 viewport、WordPress `wp_head()`、站点 logo/品牌入口、主导航和移动端菜单入口；主导航必须至少能引导用户到 Scanner/Assessment、Articles、Cases、About 等核心路径。单篇内容页应支持合适的 meta description。
- 现有实现：header.php
- 裁决备注：header.php 是全站基础导航、SEO 和插件兼容入口，应固化为站点级需求；具体导航文案和视觉结构可调整。

### 场景 5: 模块 page 的行为
- 验收条件：UseGEO 必须支持标准 WordPress 页面模板，用于承载 About、Privacy Policy、Cookie Policy 等静态页面；模板应展示页面标题和正文内容，并继承全站页头页脚。具体卡片样式和 HTML 结构不固化为需求。
- 现有实现：page.php
- 裁决备注：page.php 是基础 CMS 能力，修正后确认为静态页面展示需求。

### 场景 6: 模块 search 的行为
- 验收条件：UseGEO 应提供搜索结果页，展示用户搜索词、匹配内容列表、内容类型/日期/摘要和分页；无结果时必须给出明确反馈，并提供返回首页或重新搜索的路径。
- 现有实现：search.php
- 裁决备注：search.php 属于基础内容发现体验，是真需求。

### 场景 7: 模块 single 的行为
- 验收条件：UseGEO 的文章详情页必须把内容阅读和转化路径结合起来：文章页应展示标题、分类/日期/阅读时间、摘要和正文；当正文包含 h2/h3 时，应提供可跳转的目录或等价导航；文章页必须在 Hero、侧边栏、中段或底部提供清晰 CTA，引导用户运行 Scanner、查看诊断样例或了解服务选项；文章底部应提供上一篇/下一篇或相关文章推荐，帮助用户继续阅读。具体 HTML 结构、CSS class、TOC 生成实现和推荐查询策略不固化为需求。
- 现有实现：single.php
- 裁决备注：single.php 承载真实内容转化体验，但当前模板实现细节不应逐行固化，因此修正后确认为文章页体验需求。

### 场景 8: 模块 admin 的行为
- 验收条件：WordPress 后台必须支持 UseGEO 的两类运营对象管理：一是社区案例投稿，支持查看提交内容、标记 review_status、分配 processing_lane、记录 editorial_notes，并在列表中展示关键筛选字段；二是 Scanner/Full Assessment 线索，支持在后台列表中查看邮箱、公司类型、公司规模、帮助意图、支持意向、行动时间、扫描输入和通知状态，并通过视觉标记突出高意向线索。后台能力服务于人工运营和销售跟进，但不应把具体 PHP 实现细节、字段展示样式或当前所有 lane 名称永久固化为不可变需求。
- 现有实现：inc/admin.php
- 裁决备注：当前 admin.php 里既有真实运营需求，也有实现细节；建议修正后确认为“后台运营管理能力”，而不是逐行固化当前实现。

### 场景 9: 模块 assets 的行为
- 验收条件：UseGEO 主题必须按页面场景加载必要的前端资源：全站基础样式、导航脚本和转化追踪脚本应稳定加载；首页只加载首页、Scanner、ROI 相关资源；文章页只加载文章详情页资源；资源版本应能随文件变化自动更新，避免缓存导致旧样式/旧脚本生效。站点必须支持配置 GTM 或 GA4 追踪，并避免 GTM 与 GA4 重复注入造成重复统计。具体文件名、enqueue handle、Google Fonts 来源和 PHP 实现方式不固化为需求。
- 现有实现：inc/assets.php
- 裁决备注：assets.php 承载性能、缓存和追踪配置的真实工程需求，但具体实现细节可替换，因此修正后确认。

### 场景 10: 模块 forms 的行为
- 验收条件：UseGEO 必须支持两类公开表单提交：Scanner/Full Assessment 线索提交和社区案例投稿。所有公开提交必须只接受 POST，请求必须通过 nonce 或同源校验，服务端必须校验必填字段、邮箱格式、枚举选项和 scanner_query 等关键输入；成功提交后必须写入对应 WordPress 自定义内容类型及元数据。Lead 提交必须尝试通知站点管理员，并具备邮件失败处理机制：邮件发送失败不得丢失已保存线索，必须记录通知状态、收件人和失败原因，并向前端返回可展示的 success_no_mail 状态；提交失败、校验失败和邮件失败都必须有明确可追踪状态，避免静默失败。当前代码中的具体字段可随产品调整，但安全校验、数据持久化、通知记录、邮件失败处理和用户反馈状态必须保留。
- 现有实现：inc/forms.php
- 裁决备注：forms.php 属于 data 敏感域，是真实业务能力，但字段和枚举未来可能调整；同时当前 lead capture 代码疑似缺少 company_size_options 定义，应作为实现 bug 修复，而不是固化为当前行为。

### 场景 11: 模块 post-types 的行为
- 验收条件：UseGEO 必须有结构化的数据模型来保存两类私有运营对象：Scanner/Full Assessment 线索和社区案例投稿。两类对象应在 WordPress 后台可管理，但默认不应公开访问、不参与公开查询或搜索；其字段由表单提交和后台运营流程维护。本地开发环境可以提供可重复执行且有幂等保护的示例内容/页面种子能力，用于演示和测试，但种子数据内容、文章标题、页面正文和本地域名判断不固化为产品需求。
- 现有实现：inc/post-types.php
- 裁决备注：post-types.php 里“线索/案例私有内容类型”是真需求；local demo seed 是开发辅助能力，只保留幂等本地演示原则，不固化具体内容。

### 场景 12: 模块 setup 的行为
- 验收条件：UseGEO 主题必须完成基础 WordPress theme setup：加载文本域，声明 title-tag、post thumbnails、custom logo、HTML5 输出、宽幅/间距等必要主题能力，并注册 primary/footer 等导航菜单。仅本地开发环境可以覆盖或同步 admin_email 用于调试邮件流，但该本地邮箱和域名判断不应成为生产需求。
- 现有实现：inc/setup.php
- 裁决备注：setup.php 中主题能力注册是真实工程需求；local admin email override 是开发辅助，不固化为产品需求。

### 场景 13: 模块 case-preview 的行为
- 验收条件：首页应展示经过审核的 GEO case preview，用于提供社会证明和真实案例线索；只应展示 review_status 为 approved 的案例，并在无可展示案例时显示建设中/提交案例的空状态入口。案例展示应尊重匿名选项，避免直接暴露不应公开的投稿者信息。
- 现有实现：template-parts/cases/case-preview.php
- 裁决备注：case-preview.php 是信任与案例证明模块，应固化为首页转化支撑需求。

### 场景 14: 模块 community-intro 的行为
- 验收条件：UseGEO 应提供社区案例投稿入口，用于收集真实 GEO 执行案例，支撑未来案例库、playbook 和证明资产。投稿表单必须说明提交会进入编辑审核，不会直接公开；必须收集联系邮箱、公司类型、核心问题、采取动作和结果等必要字段，并提供匿名化选项。
- 现有实现：template-parts/community/community-intro.php
- 裁决备注：community-intro.php 是未来 proof asset / case library 的输入层，是真需求。

### 场景 15: 模块 content-hub 的行为
- 验收条件：首页应提供内容入口模块，展示最新或精选 GEO 文章，帮助用户从 Scanner/转化路径之外继续学习 GEO 基础、AI visibility 和执行方法。内容卡片应至少包含分类/标签、阅读时间、标题、摘要和阅读入口；无内容时应展示明确空状态。
- 现有实现：template-parts/content-hub/content-hub.php
- 裁决备注：content-hub.php 是教育内容分发入口，应固化为产品体验需求。

### 场景 16: 模块 methodology 的行为
- 验收条件：首页应解释 UseGEO assessment 的方法论，让用户理解 Scanner/Manual Audit 会评估哪些 AI visibility 信号；至少应覆盖 entity clarity、recommendation context、proof depth 或等价维度，避免用户把评估当成黑箱分数。
- 现有实现：template-parts/conversion/methodology.php
- 裁决备注：methodology.php 是建立 scanner 可信度和诊断方法透明度的关键模块，应固化为需求。

### 场景 17: 模块 sample-report 的行为
- 验收条件：首页应提供 full diagnostic / paid audit 的样例报告预览，展示完整诊断会产出哪些类型的 gap、评分解释和行动建议；样例必须标明示例性质，不能让用户误以为是其自身网站的真实结果，并应引导用户先运行 free scanner。
- 现有实现：template-parts/conversion/sample-report.php
- 裁决备注：sample-report.php 是付费转化前的预期管理和信任建立模块，应固化为需求。

### 场景 18: 模块 services 的行为
- 验收条件：首页应说明 UseGEO 在免费 Scanner 之后可提供的付费帮助路径，如 Manual GEO Audit、GEO Fix Sprint、AI Visibility Monitoring 或等价服务层级；每个服务应说明适用场景、核心产出和进入方式，并优先引导用户先运行 free scanner 判断是否匹配。
- 现有实现：template-parts/conversion/services.php
- 裁决备注：services.php 是商业化路径说明，应固化为产品需求；具体服务命名和套餐内容可迭代。

### 场景 19: 模块 sidebar-cta 的行为
- 验收条件：内容页或侧边栏可以提供 deeper assessment CTA，引导正在阅读 GEO 内容的用户回到 Scanner/Assessment 路径，说明可获得 readiness、signal gaps 和 next-step guidance。该模块是可复用转化组件，具体使用位置、标题文案和视觉样式不固化为需求。
- 现有实现：template-parts/cta/sidebar-cta.php
- 裁决备注：sidebar-cta.php 是可复用转化组件，不应锁死当前文案和位置，因此修正后确认。

### 场景 20: 模块 hero 的行为
- 验收条件：UseGEO 首页首屏必须清晰表达产品定位：面向 B2B growth teams 的 AI visibility / GEO diagnostic；必须说明用户可以通过免费评估识别 GEO readiness、内容信号和证明缺口，并提供至少两个首屏行动入口：运行免费 assessment/scanner，以及继续学习 GEO 基础内容。首屏还应提供可信的利益点或流程预览，帮助用户理解 Assess → Diagnose → Execute 的转化路径。
- 现有实现：template-parts/hero/hero.php
- 裁决备注：hero.php 是首页核心定位与首屏转化承诺，应作为产品体验需求固化；具体文案、分数示例和视觉结构可后续优化。

### 场景 21: 模块 learning-path 的行为
- 验收条件：首页应提供 GEO 学习路径，帮助新用户理解从 GEO 基础认知、信号/结构缺口识别，到执行优先级判断的渐进路径；该模块应降低用户理解门槛，并把教育内容与后续 Scanner/诊断行动连接起来。
- 现有实现：template-parts/learning/learning-path.php
- 裁决备注：learning-path.php 是教育型转化路径的一部分，应固化为体验需求。

### 场景 22: 模块 roi 的行为
- 验收条件：UseGEO 首页可以提供 ROI/商业价值估算模块，帮助用户用投资额、当前流量、转化率等输入判断 GEO 修复是否值得优先投入。该模块输出必须明确标注为方向性估算，不得包装成确定收益承诺；结果至少应帮助用户理解预期收入提升、ROI 或回本周期中的一种或多种商业指标，并把用户引导回更深入的 GEO diagnostic 或人工评估。具体计算公式、字段名称、默认值和展示样式不固化为需求。
- 现有实现：template-parts/roi/roi.php
- 裁决备注：ROI 模块是转化辅助能力，但当前计算模型可能随商业假设调整，因此修正后确认，不直接固化现有实现。

### 场景 23: 模块 scanner 的行为
- 验收条件：UseGEO 首页必须提供免费的 GEO Scanner 入口，允许用户输入公司网站并获得即时的 GEO readiness snapshot；扫描体验必须包含输入校验、运行/加载反馈、结果展示、模型维度分数（至少覆盖 ChatGPT/Claude/Gemini 或等价 AI 渠道）、信号维度拆解、关键发现和下一步建议。免费结果后必须提供 Full Diagnostic 解锁表单，收集工作邮箱、公司类型、规模、帮助意图、支持意向和行动时间，并携带 scanner_query 提交到后端线索捕获流程。Scanner 不得变成纯营销假分数：如果结果来自估算、规则评分或演示数据，必须在文案或交互中明确说明其方向性/估算属性；结果必须能解释主要评分依据、信号缺口和建议动作，避免给用户造成“已完成真实实时全网扫描”的误导。
- 现有实现：template-parts/scanner/scanner.php
- 裁决备注：scanner.php 是核心转化资产和产品承诺，应固化为 P0 需求；同时必须约束结果可信度和可解释性，避免 scanner 退化为不可验证的营销展示。

### 场景 24: 模块 value-proposition 的行为
- 验收条件：首页应清晰解释为什么 GEO/AI visibility 值得现在关注，包括搜索入口变化、品牌被索引但不被推荐的风险，以及内容结构对 AI 推荐和获客的影响；该模块应帮助用户形成行动动机。
- 现有实现：template-parts/value/value-proposition.php
- 裁决备注：value-proposition.php 是核心价值主张模块，应固化为产品叙事需求。

---

**生成时间**: 2026-06-19 22:19:39
**来源**: reverse-spec-candidates.yml（人工确认）