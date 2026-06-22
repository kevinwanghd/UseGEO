# Request: Publish GEO Basics Cluster

## CR-ID
CR-011

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已完成三张 money pages 扩写。下一步需要发布 GEO basics 内容集群，建立基础主题权威，让搜索引擎和 AI crawler 明确 UseGEO 是 GEO / AI visibility 学习入口，并把读者导向 Scanner、money pages 和 partner referral 路径。

## 业务目标
发布 5 篇英文基础内容文章：
1. What Is Generative Engine Optimization?
2. GEO vs SEO: What Actually Changes?
3. What Is AI Visibility?
4. Why Your Brand Does Not Show Up in ChatGPT Recommendations
5. How AI Search Chooses Which Brands to Mention

目标是承接 P0 基础关键词，形成内链闭环，并把读者从教育内容自然引回 Scanner。

## 功能描述
为每篇文章生成可发布 HTML 内容快照，包含 Quick answer、主体章节、common mistakes、FAQ、internal links 和 Scanner CTA。发布时优先更新已存在的相近文章，避免重复 slug；不存在的文章创建为 publish 状态。

## 非功能需求
- 每篇 1200–1800 英文词。
- 每篇至少 4 个 FAQ。
- 每篇至少 1 个 Scanner CTA。
- 每篇至少 2 个内部链接，链接到 money pages 或其他 basics 文章。
- 不承诺精准追踪所有 AI 推荐，不承诺 AI ranking，不贬低 SEO。
- 不提交 WordPress Application Password 或任何 secret。

## 约束条件
- UseGEO 不是自营完整 GEO 服务公司，内容必须保持 learning content + Scanner + partner referral / sponsor / affiliate / lead resale 定位。
- 生产发布只使用 WordPress REST API 更新/创建 posts，不改主题代码。
- 如使用临时 Application Password，发布后提醒 Kevin 撤销。

## 优先级
P0

## 验收标准（高层）
- 5 篇文章生产 URL 返回 HTTP 200。
- 每篇 1200–1800 英文词。
- 每篇包含 Quick answer、FAQ、common mistakes 或 checklist。
- 每篇至少 1 个 Scanner CTA。
- 内链覆盖 `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/` 中相关页面。
- 不出现虚假 AI ranking / 精准推荐追踪承诺。

## 附件
- `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md` CR-011 section
- `DeliverHQ/docs/PRD.md` PRD-CONTENT-LIBRARY
