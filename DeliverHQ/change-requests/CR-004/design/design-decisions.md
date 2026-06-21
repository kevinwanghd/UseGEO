# Design Decisions: Partner Lead Export / Pipeline

| # | Decision | Rationale | Tradeoffs | Status |
|---|---|---|---|---|
| 1 | 只做后台手动导出，不自动推送 partner | 降低误发和合规风险 | 需要人工运营 | adopted |
| 2 | CSV 只导出 consent=yes + hot/warm | 避免未授权 lead 被售卖 | 可能漏掉部分潜力 lead | adopted |
| 3 | partner_status 用 CPT meta | 与现有 lead meta 一致 | 查询需要 meta_query | adopted |
| 4 | 使用 admin-post 导出 CSV | WordPress 原生，带 nonce/权限 | UI 简单 | adopted |
