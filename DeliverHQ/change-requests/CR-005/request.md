# Request: Sponsor / Ad Inventory Foundation

## CR-ID
CR-005

## 提出人
Kevin

## 提出日期
2026-06-21

## 来源
- PRD 锚点：PRD-SPONSOR-INVENTORY

## 需求背景

UseGEO 的商业模式以 GEO 学习内容流量、Scanner 工具、广告/赞助/affiliate/partner referral/lead resale 变现。当前已有真实 Scanner、LLM summary、partner lead 管线，但还没有可售的广告/赞助位库存。

## 业务目标

支持手动配置 sponsor slot，用于首页、Scanner 结果、文章侧边栏展示赞助内容。

## 功能描述

后台新增 UseGEO Sponsors 配置页，支持三个 placement：homepage、scanner_result、article_sidebar。每个 placement 可配置 active、title、description、cta label、url。

## 非功能需求

必须标注 Sponsored；外链安全；不接广告平台；不自动发送数据给 sponsor。

## 优先级

P1

## 验收标准

1. 后台能保存三个 sponsor slot。
2. active=true 且字段完整时前台显示。
3. active=false 时不显示。
4. 链接安全且标注 sponsored。
5. 不破坏 scanner、lead、文章页。
