# Request: Article Page Conversion Redesign

## CR-ID
CR-021

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
Kevin 指出 UseGEO 文章页视觉很丑，影响转化，示例页面为 `https://usegeo.blog/geo-for-content-marketing-teams/`。初步生产审计发现该页存在重复 H1、CTA 过多、TOC 包含长 FAQ 问题、卡片/渐变/阴影过重、阅读体验和转化路径不够可信等问题。

## 业务目标
将文章页从“堆卡片的模板页”改为更高信任、更清晰、更适合 B2B 内容转化的 editorial SaaS 页面：先让用户愿意读，再用少量明确 CTA 引导到 Scanner / money pages。

## 功能描述
- 重构 `single.php` 文章模板信息架构。
- 重写 `single.css`，降低视觉噪音，统一主题、留白、卡片边界和 CTA。
- 去除正文重复 H1。
- TOC 只收录 H2，避免长 FAQ H3 污染侧栏。
- 减少重复 CTA，把转化路径收敛为：Hero primary CTA、侧栏 Scanner CTA、底部 action panel、foundation links。
- 保留 SEO 内容、slug、schema、existing tracking data attributes。

## 非功能需求
- 不改文章内容和 URL。
- 不改公开表单字段名。
- 不破坏 JSON-LD、sponsor slot、tracking、scanner 链路。
- 移动端必须单列可读。
- 页面不使用 fake screenshots、不使用 AI purple glow、不新增外部图片依赖。

## 约束条件
- 生产上传/缓存清理仍需 Kevin 确认。
- 本 CR 只改文章模板，不改 homepage/money pages/admin。
- 所有 DeliverHQ 产物必须在 `DeliverHQ/` 内。

## 优先级
P0

## 验收标准（高层）
- 示例文章页生产部署后 H1 count = 1。
- TOC 不包含 H3 FAQ 长问题，只包含 H2 内容章节。
- 页面源码仍包含 Scanner CTA 和 3 个 money page foundation links。
- `single.php` 保留 article sidebar sponsor slot。
- `single.css` 不再使用过重 hero huge type / 多处大渐变卡片堆叠。
- 本地 verifier PASS；生产上传后 production verifier PASS。
