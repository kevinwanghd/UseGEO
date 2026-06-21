# Request: Content SEO Cluster Foundation

## CR-ID
CR-006

## 提出人
Kevin

## 提出日期
2026-06-21

## 来源
- PRD 锚点：PRD-CONTENT-LIBRARY

## 需求背景

UseGEO 的变现依赖学习内容获取流量，再通过 Scanner、partner lead、sponsor slots 变现。当前首页已有 content-hub 和 learning-path，但缺少明确的商业内容入口。

## 业务目标

在首页新增 SEO cluster 入口模块，明确引导到三类商业/工具内容页：GEO checklist、AI visibility tools、Best GEO agencies。先做入口和内链，不自动发布长文章内容。

## 功能描述

新增 homepage section：Content SEO Cluster。展示 3 个 cards：
1. GEO Checklist
2. AI Visibility Tools
3. Best GEO Agencies

每个 card 包含 title、description、target slug、CTA。目标页面如果尚不存在，链接仍指向未来 slug。

## 非功能需求

- 不创建/发布文章。
- 不接广告平台。
- 不影响 scanner 主路径。
- 内链要清晰，CTA 回到 Scanner。

## 优先级

P1

## 验收标准

1. 首页出现 Content SEO Cluster 区块。
2. 3 个入口分别指向 `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/`。
3. 区块文案不承诺 UseGEO 自营服务。
4. 移动端布局不破坏。
