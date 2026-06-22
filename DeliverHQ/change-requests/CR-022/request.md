# Request: Public Page Visual Unification

## CR-ID
CR-022

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
Kevin 指出不只是一个文章页丑，而是希望所有页面视觉统一后再上传。当前文章页已在 CR-021 本地重做，但生产尚未上传；money pages 和普通页面仍使用简陋 `page.php` + `usegeo-card` 包裹，3 个 money pages 存在重复 H1，整体和新文章页不统一。

## 业务目标
将 UseGEO public pages 统一成同一套高信任 B2B editorial SaaS 视觉语言：文章页、money pages、About/Articles/Cases 等普通页读起来像同一个品牌，并减少重复 H1 / 卡片堆叠带来的廉价感。

## 功能描述
- 重构 `page.php` 普通页面模板。
- 在 `base.css` 增加统一 public page hero/content/action/foundation styles。
- 普通页渲染时移除与页面标题重复的正文首个 H1。
- money pages 保留正文、FAQ、Scanner CTA 和内链，不改 URL。
- 首页 `front-page.php` 结构不动，只继承基础导航/页脚/变量。
- 最终上传包包含 CR-021 article redesign 和 CR-022 page redesign。

## 非功能需求
- 不改 URL / slug / WordPress content / 表单字段。
- 不移除 scanner、sponsor、JSON-LD、tracking。
- 不新增外部图片依赖，不做 fake screenshot。
- 移动端单列可读。

## 约束条件
- 生产上传/缓存清理仍需 Kevin 确认。
- 本 CR 不重写首页模块内容，只统一 public page 模板和样式。
- 所有 DeliverHQ 产物必须在 `DeliverHQ/` 内。

## 优先级
P0

## 验收标准（高层）
- `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/` 生产部署后 H1 count = 1。
- 普通页使用统一 `usegeo-public-page-*` class system。
- money pages 仍包含 scanner CTA 和 3 个 money page 互链。
- 文章页 CR-021 redesign 仍保留并通过 local verifier。
- 本地 verifier PASS；生产上传后 production verifier PASS。
