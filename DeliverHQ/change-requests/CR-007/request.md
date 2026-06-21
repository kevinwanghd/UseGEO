# Request: Publish GEO Checklist Page

## CR-ID
CR-007

## 提出人
Kevin

## 提出日期
2026-06-21

## 来源
- PRD 锚点：PRD-CONTENT-LIBRARY
- CR-006 已在首页创建 `/geo-checklist/` 入口

## 需求背景

CR-006 已上线 SEO cluster，但 `/geo-checklist/` 目标页尚未发布。这个页面是 UseGEO 学习内容流量的第一个商业内容入口，用于承接 GEO basics / checklist 类搜索流量，并把读者引导到 Scanner。

## 业务目标

发布一页清晰、可读、可转化的 GEO checklist 页面：

- 解释 GEO readiness 应该检查什么；
- 给用户一套实用 checklist；
- 明确引导用户运行 free scanner；
- 不承诺 UseGEO 自营完整服务；
- 为后续广告、赞助、partner lead 变现做内容入口。

## 功能描述

创建 WordPress 页面：

- Title: Generative Engine Optimization Checklist
- Slug: `geo-checklist`
- Status: publish
- Content: 使用 CR-007 输出的 block HTML。

## 非功能需求

- 页面内容不能像 AI 文章堆砌，要具体、克制、可读。
- 页面至少有 2 个 Scanner CTA。
- 页面应内链到 `/ai-visibility-tools/` 和 `/best-geo-agencies/`（即使后续页面尚未发布）。

## 优先级

P1

## 验收标准

1. `https://usegeo.blog/geo-checklist/` 返回 200。
2. 页面 title 是 `Generative Engine Optimization Checklist`。
3. 页面包含至少 6 个 checklist sections。
4. 页面包含至少 2 个指向 `/#scanner` 的 CTA。
5. 页面包含到 `/ai-visibility-tools/` 和 `/best-geo-agencies/` 的内链。
