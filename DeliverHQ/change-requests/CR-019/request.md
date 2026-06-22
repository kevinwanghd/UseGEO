# Request: Sponsor Reporting & Inventory Tracking

## CR-ID
CR-019

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已有 sponsor slot 基础能力和 CR-017 sponsor offer，但当前 sponsor slot 只负责展示，缺少点击/曝光上报、投放周期控制和更严格的 sponsored/nofollow 外链标记。CR-019 补齐最小可运营的 reporting 与 inventory tracking 基础。

## 业务目标
让手动 sponsor inventory 可以被运营追踪：点击事件能进入 GTM/dataLayer，曝光事件只记录 placement，后台可配置 start/end date，到期自动隐藏；所有 sponsor 外链默认 `rel="noopener sponsored nofollow"`。

## 功能描述
新增/修改：
- Sponsor admin 表单增加 start_date / end_date。
- Sponsor slot render 按 active + URL/title + date window 判断展示。
- Sponsor card 带 `data-usegeo-sponsor-slot`、`data-usegeo-sponsor-url`、`data-usegeo-sponsor-active-window`。
- `tracking.js` 增加 `sponsor_click` 事件：placement、sponsor_url。
- `tracking.js` 增加一次性 `sponsor_impression` 事件：只记录 placement，不记录 URL。
- Sponsor link rel 改为 `noopener sponsored nofollow`。

## 非功能需求
- 不接入自动广告平台，不做结算，不新增数据库表。
- impression tracking 使用 IntersectionObserver；不支持时静默跳过，不阻塞页面。
- 不记录用户身份或个人信息。
- 不影响现有 scanner / lead / content 功能。

## 约束条件
- 生产上传/缓存清理仍需 Kevin 确认。
- 本 CR 不要求 GTM 容器已配置，只要求 dataLayer 事件可被 GTM 读取。
- 所有 DeliverHQ 产物必须在 `DeliverHQ/` 内。

## 优先级
P1

## 验收标准（高层）
- sponsor click push `sponsor_click`，包含 placement 和 sponsor_url。
- sponsor impression push `sponsor_impression`，只包含 placement。
- 后台 sponsor slot 可保存 start_date / end_date。
- end_date 早于当前日期时 sponsor 不显示；日期为空时长期展示。
- sponsor 外链默认 `rel="noopener sponsored nofollow"`。
- 本地 verifier PASS；生产上传后 public HTML / JS 验证 PASS。
