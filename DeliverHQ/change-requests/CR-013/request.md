# Request: Publish Tactical GEO Cluster

## CR-ID
CR-013

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已完成 money pages、GEO Basics Cluster 和内容模板系统。下一步发布 tactical GEO 内容集群，覆盖读者能直接执行的页面级改进主题，增强 UseGEO 的实操可信度，并把读者导回 Scanner、GEO checklist、AI visibility tools 和 agency guide。

## 业务目标
发布 5 篇 Tactical GEO 文章：
1. How to Improve Entity Clarity for AI Search
2. How to Build Comparison Pages for GEO
3. How Proof Signals Help AI Recommend Your Brand
4. Schema Markup for AI Visibility
5. How to Make Your Homepage Easier for LLMs to Understand

## 功能描述
按 `DeliverHQ/docs/CONTENT-TEMPLATE.md` 生成 5 篇英文文章 HTML snapshot，发布为 WordPress posts。每篇包含 Quick answer、Checklist、Example workflow、Common mistakes、FAQ、internal links 和 Scanner CTA。

## 非功能需求
- 每篇 1200–1800 英文词。
- 每篇至少 4 个 FAQ。
- 每篇至少 1 个 Scanner CTA。
- 每篇至少 3 个内部链接。
- 不承诺 AI ranking，不承诺 schema/内容直接带来 AI 推荐，不写 “SEO is dead”。
- 发布脚本只从 process env 读取 WP_USER/WP_APP_PASSWORD，不落盘。

## 约束条件
- 不修改 WordPress theme 代码。
- 不新增 schema 代码；Schema article 只讲安全做法。
- 优先按 slug 查询已有 post，避免 duplicate `-2`。

## 优先级
P1

## 验收标准（高层）
- 5 个生产 URL 均 HTTP 200。
- 每篇 1200–1800 英文词、FAQ >=4、Scanner CTA >=1、internal links >=3。
- 每篇包含对应任务要求：entity clarity checklist / comparison outline / proof examples / schema types / homepage title-H1-meta examples。
- forbidden claim scan 0 命中。
