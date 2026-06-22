# Request: On-site SEO/GEO Technical Foundation

## CR-ID
CR-018

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已完成 money pages、内容集群、内容模板、分发资产和 partner/sponsor 商业验证资产。下一步需要补齐站内 SEO/GEO 技术基础，避免内容有了但站点缺少可被搜索引擎和 LLM 稳定提取的技术信号。

## 业务目标
建立最小但安全的站内 SEO/GEO foundation：确认 sitemap/robots 与新页面收录入口；为站点、文章、money pages 和可见 FAQ 输出可信 JSON-LD；让文章模板稳定链接到 3 个 money pages；保持 title/meta description 不堆关键词且唯一。

## 功能描述
本 CR 包含：
- 审计当前生产 sitemap、robots、money page HTML、article HTML、title/meta、internal links、JSON-LD。
- 在主题中新增最小 JSON-LD 输出：Organization/WebSite/WebPage/Article/FAQPage。
- FAQ schema 只镜像页面中可见 FAQ 内容；不生成 rating/review/author credential 等虚假数据。
- 在文章模板增加固定 GEO foundation links，覆盖 `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/`。
- 新增 deterministic verifier，验证本地主题实现与生产审计结果。

## 非功能需求
- 不依赖付费 SEO 插件。
- 不伪造评分、评论、证书、作者资历或第三方背书。
- 不改变 lead consent、sponsor、scanner 业务逻辑。
- 主题代码最小改动，所有 DeliverHQ 产物留在 `DeliverHQ/` 内。

## 约束条件
- 生产上传/缓存清理需要 Kevin 人工确认，本 CR 先生成可部署主题包和本地验证证据。
- 如果生产已有插件 schema，先验证不重复；当前审计显示生产 JSON-LD 为 0，因此主题侧补最小 schema。
- 当前 Windows 环境没有可用 `php` 命令，PHP 语法以静态结构检查替代，最终生产前仍需 WordPress 上传验证。

## 优先级
P1

## 验收标准（高层）
- `/sitemap.xml` 或 WordPress sitemap 可访问，money pages 与 20+ posts 可在 sitemap 子文件中发现。
- 本地主题包含可信 JSON-LD 输出逻辑：Organization/WebSite/WebPage/Article/FAQPage。
- money pages 至少可输出 WebPage/FAQPage 基础 schema，FAQ 仅来自可见 FAQ。
- 文章模板稳定包含 3 个 money page links。
- 首页链接 3 个 money pages；3 个 money pages 互链。
- money pages 有唯一 title/meta description，且不堆关键词。
- 本地 verifier PASS；生产当前审计结果已保存。
