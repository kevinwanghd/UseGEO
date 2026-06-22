# Request: Content Template & Publishing System

## CR-ID
CR-012

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已完成 CR-010 money pages 和 CR-011 GEO Basics Cluster。后续 CR-013+ 将持续发布 tactical / commercial / support content。如果每篇都从零写，会导致结构不稳定、内链遗漏、CTA 不一致、FAQ/schema 风险不可控。

## 业务目标
建立统一的 UseGEO 内容模板与发布检查系统，让后续文章按同一 SEO/GEO 结构生成：brief → outline → article body → FAQ → internal links → Scanner CTA → partner angle → verifier。

## 功能描述
新增并验证一套内容模板产物：
- `DeliverHQ/docs/CONTENT-TEMPLATE.md`：长期使用的内容模板和发布规则。
- `outputs/article-brief-template.yml`：文章 brief 字段模板。
- `outputs/article-html-skeleton.html`：文章 HTML 骨架。
- `outputs/publishing-checklist.md`：发布前检查清单。
- `outputs/faq-jsonld-plan.md`：FAQ/Article schema 后续实现规划。
- `evidence/verify_content_template.py`：模板 verifier。

## 非功能需求
- 模板必须明确 required fields：keyword、search intent、target reader、related money page、scanner CTA、outline、FAQ、internal links、partner angle。
- 标准文章结构必须包含：H1、开头 100 字直接回答、Quick answer、Main sections、Checklist/criteria、Example、Common mistakes、FAQ、Next step CTA。
- 发布检查必须要求至少 3 个内链、至少 1 个 Scanner CTA、FAQ 可结构化、无虚假 AI ranking / 精准追踪承诺。
- 不发布生产 WordPress 内容，不使用或提交生产 secret。

## 约束条件
- DeliverHQ 产物只放在 `DeliverHQ/` 内。
- 不修改 WordPress theme PHP/CSS/JS。
- 不实现 schema 代码；只做 schema planning，后续 CR 单独实现。

## 优先级
P0

## 验收标准（高层）
- 内容模板文档存在并包含所有 required fields。
- HTML skeleton 包含固定文章结构和 Scanner CTA。
- Publishing checklist 包含内链、FAQ、forbidden claims、production verifier 要求。
- FAQ JSON-LD plan 明确只镜像可见 FAQ，不做虚假结构化数据。
- 本地 verifier PASS。

## 附件
- `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md` CR-012 section
- `DeliverHQ/docs/PRD.md` PRD-CONTENT-LIBRARY
