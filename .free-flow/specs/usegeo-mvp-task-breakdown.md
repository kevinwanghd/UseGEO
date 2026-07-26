# UseGEO MVP Task Breakdown

> For execution, follow `CLAUDE.md`, `docs/engineering/code-and-git-standards.md`, and `docs/engineering/wordpress-integration.md`.

**Goal:** Deliver a WordPress-first MVP for `usegeo.blog` that supports homepage conversion, GEO scanner interaction, ROI calculator interaction, article CTA flow, and lead capture.

**Architecture:** Use a custom lightweight WordPress theme under `wordpress/theme/usegeo`. Keep homepage, article page, scanner, ROI calculator, and CTA modules inside the theme as template parts plus vanilla JS/CSS. Do not introduce plugins or heavy frontend frameworks in this phase.

**Tech Stack:** WordPress theme templates, PHP, HTML, CSS, vanilla JavaScript

---

## 1. 执行原则

### 1.1 当前范围

本次只做 MVP 闭环，不做：

- 插件化
- 后台配置页
- 真实 LLM API 检测
- 复杂埋点平台深度集成
- 文章系统高级功能

### 1.2 完成标准

满足以下条件才算 MVP 可用：

1. 首页具备完整 Landing Page 结构。
2. 品牌 AI 曝光度诊断器可完成输入、加载、结果展示。
3. 结果区包含留资表单入口或留资模块。
4. ROI 试算器可即时计算并展示结果。
5. 文章页具备可用的侧边 CTA。
6. 移动端主路径可用。

---

## 2. 任务总览

按依赖关系拆成 8 个任务：

1. 主题基础能力完善
2. 全局样式系统落地
3. 首页 Hero 完整实现
4. GEO Scanner 完整实现
5. 留资区与 CTA 模块实现
6. 首页剩余区块实现
7. 文章页与侧边栏实现
8. 验收、清理与文档回写

其中：

- `1-4` 是主路径关键任务
- `5-7` 是闭环补全任务
- `8` 是交付门槛

---

## 3. 任务拆分

## Task 1: 主题基础能力完善

**目标：** 把当前轻主题骨架补成可稳定承载首页和文章页的基础结构。

**涉及文件：**

- `D:\Code\UseGEO\wordpress\theme\usegeo\functions.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\header.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\footer.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\index.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\style.css`

**子任务：**

- 完善主题基础信息与支持项
- 确认导航菜单、文章缩略图、标题支持等基础能力
- 统一 assets enqueue 方式
- 补基础 body class / shell 结构
- 预留后续 SEO 与表单挂载位

**验收标准：**

- 主题能被 WordPress 正常识别
- 首页、文章页、普通页面模板都能正常渲染
- CSS 和 JS 资源通过统一方式加载

---

## Task 2: 全局样式系统落地

**目标：** 建立一套适用于首页、工具和文章页的统一视觉底座。

**涉及文件：**

- `D:\Code\UseGEO\wordpress\theme\usegeo\assets\css\base.css`
- `D:\Code\UseGEO\wordpress\theme\usegeo\assets\css\homepage.css`

**子任务：**

- 固化颜色、圆角、边框、阴影、间距变量
- 建立通用容器、卡片、标题、按钮、输入框样式
- 加入首页和文章页共用的排版基础
- 补桌面和移动端断点规则

**验收标准：**

- 不同模块不再各写一套基础样式
- 首页和文章页视觉语言一致
- 移动端布局不会明显崩坏

---

## Task 3: 首页 Hero 完整实现

**目标：** 把首页首屏做成真正的产品型 Landing Page 首屏。

**涉及文件：**

- `D:\Code\UseGEO\wordpress\theme\usegeo\front-page.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\template-parts\hero\hero.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\assets\css\homepage.css`

**子任务：**

- 完整实现 Hero 标题、副标题、信任标签、主 CTA 区域
- 调整视觉层级，让输入区成为绝对焦点
- 预留与 scanner 模块衔接的首屏布局
- 做 Hero 移动端适配

**验收标准：**

- 首屏看起来像产品站，不像博客
- 用户不滚动就能看到核心价值和主动作
- CTA 足够显眼

---

## Task 4: GEO Scanner 完整实现

**目标：** 把品牌 AI 曝光度诊断器从骨架升级为完整 MVP 工具。

**涉及文件：**

- `D:\Code\UseGEO\wordpress\theme\usegeo\template-parts\scanner\scanner.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\assets\js\scanner.js`
- `D:\Code\UseGEO\wordpress\theme\usegeo\assets\css\homepage.css`

**子任务：**

- 完整输入态
- 2-3 秒加载态与动态文案
- 结果态评分卡
- 简化图表或图表占位区
- 结果解读文案区
- 与留资区的衔接

**验收标准：**

- 输入、加载、结果三态完整
- 结果区看起来可信，不像假按钮
- 移动端可完整操作

---

## Task 5: 留资区与 CTA 模块实现

**目标：** 把诊断体验接上真正的转化动作。

**涉及文件：**

- `D:\Code\UseGEO\wordpress\theme\usegeo\template-parts\scanner\scanner.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\template-parts\cta\sidebar-cta.php`
- 必要时新增主题内表单 partial

**子任务：**

- 在 scanner 结果区增加报告升级模块
- 加入企业邮箱和职位字段
- 加入权益文案
- 明确提交后的用户反馈
- 抽出可复用 CTA 模块供文章页使用

**验收标准：**

- 首页主路径具备可见的留资入口
- CTA 模块可以在首页与文章页复用
- 文案符合 B2B 语境

---

## Task 6: 首页剩余区块实现

**目标：** 补齐支撑转化的首页其余内容模块。

**涉及文件：**

- `D:\Code\UseGEO\wordpress\theme\usegeo\front-page.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\template-parts\content-hub\content-hub.php`
- 新增或扩展 value proposition partial
- `D:\Code\UseGEO\wordpress\theme\usegeo\template-parts\roi\roi.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\assets\js\roi.js`

**子任务：**

- 增加 Value Proposition 三列区块
- 完善 Content Hub 文章卡片结构
- 完善 ROI 试算器布局与即时计算
- 统一首页各模块节奏和间距

**验收标准：**

- 首页结构完整
- ROI 工具可以即时反馈结果
- 内容区块起到信任背书作用而不抢主 CTA

---

## Task 7: 文章页与侧边栏实现

**目标：** 打通内容入口到工具入口的辅助路径。

**涉及文件：**

- `D:\Code\UseGEO\wordpress\theme\usegeo\single.php`
- `D:\Code\UseGEO\wordpress\theme\usegeo\template-parts\cta\sidebar-cta.php`
- 必要时新增文章页样式文件或扩展 `base.css`

**子任务：**

- 优化文章页主阅读区排版
- 实现右侧 Sticky Sidebar
- 让 Sidebar 支持 CTA 或 ROI 模块展示
- 做移动端文末 CTA 退化方案

**验收标准：**

- 桌面端文章页有清晰侧边 CTA
- 移动端不会因侧边栏失效而丢失转化入口
- 阅读体验不被工具强打断

---

## Task 8: 验收、清理与文档回写

**目标：** 在交付前完成自查，确保后续能继续迭代。

**涉及文件：**

- `D:\Code\UseGEO\docs\engineering\usegeo-development-plan.md`
- `D:\Code\UseGEO\docs\reports\`
- 必要时更新 `D:\Code\UseGEO\docs\product\usegeo-prd.md`
- 必要时更新 `D:\Code\UseGEO\docs\product\usegeo-design-guidelines.md`

**子任务：**

- 桌面端和移动端走一遍主路径
- 确认首页、文章页、CTA、工具逻辑都正常
- 清理调试代码和无效样式
- 将偏离 PRD 的实际实现回写到文档
- 产出一份首轮验收或交付记录

**验收标准：**

- 主路径可走通
- 文档和实现不严重脱节
- 仓库结构保持整洁

---

## 4. 建议执行顺序

严格按下面顺序推进：

1. Task 1
2. Task 2
3. Task 3
4. Task 4
5. Task 5
6. Task 6
7. Task 7
8. Task 8

说明：

- Task 1-2 是所有 UI 和模板的基础
- Task 3-5 决定主转化链路是否成立
- Task 6-7 决定产品站是否完整
- Task 8 决定是否能稳定进入下一轮开发

---

## 5. 可并行部分

如果后面要并行推进，可以这样拆：

- 一条线做 `首页主路径`：Task 3 + Task 4 + Task 5
- 一条线做 `视觉系统和页面结构`：Task 2 + Task 6
- 一条线做 `文章页辅助路径`：Task 7

但前提是：

- Task 1 必须先完成
- 所有人必须遵守 `CLAUDE.md`

---

## 6. 当前建议

下一步直接从 `Task 1: 主题基础能力完善` 开始，不要同时开多个任务。

原因：

- 现在主题骨架只是初始版本
- 先把基础结构和资源加载方式稳定下来，后面的页面和工具实现才不会反复返工
