# Design Decisions: Partner Lead Monetization Foundation

| # | Decision | Rationale | Tradeoffs | Status |
|---|---|---|---|---|
| 1 | 显式 partner consent checkbox | lead 转介/售卖必须有授权证据 | 增加表单摩擦 | adopted |
| 2 | v1 不自动发送给 partner | 降低合规和误发风险 | 需要人工运营 | adopted |
| 3 | lead_quality 用规则计算 | 简单可解释 | 后续需调优 | adopted |
| 4 | budget/region 用枚举 | 便于筛选和售卖 | 表达不如自由文本丰富 | adopted |
| 5 | CTA 文案从自营服务改为 expert follow-up / partner matching | 符合商业定位 | 可能降低“完整服务”转化 | adopted |

## lead_quality 规则

- hot：consent=yes + 高意向 help_intent + 高意向 support_level + 近期 timeline
- warm：consent=yes + business email，或 score < 70
- cold：consent=no，或 just_researching + not_hiring

## 表单文案

建议 checkbox 文案：

> I agree that UseGEO may share my submission with relevant GEO/SEO partners who can contact me about services that may fit my needs.

## 后台展示

新增列位置：在 GEO Score / Weakest Dimension 之后，Notification 之前。
