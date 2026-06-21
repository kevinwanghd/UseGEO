# Human Decisions: LLM Summary 启用与验证

| # | Issue | Decision | Decision By | Date |
|---|---|---|---|---|
| 1 | 是否继续 CR-002 | 继续下一个需求：LLM Summary 启用与验证 | Kevin | 2026-06-21 |
| 2 | 是否提交真实 secret 到仓库 | 不提交，生产手工配置 | Kevin | 2026-06-21 |
| 3 | UI 是否改动 | 不改 UI，复用 CR-001 | Kevin | 2026-06-21 |
| 4 | 代理 base_url | 使用 `https://chat.aiprox.net` | Kevin | 2026-06-21 |
| 5 | 默认模型 | 使用 `gpt-5.4-mini`，需要时可切 `gpt-5.5` | Kevin | 2026-06-21 |
| 6 | 是否先实现 base_url 支持 | 批准先实现并部署 base_url/model 配置能力；生产 key 由 Kevin 手工配置后再验收 available=true | Kevin | 2026-06-21 |

## 待决策项

无。生产 secret 不在 CR 文档中记录；后续由 Kevin 在生产 `wp-config.php` 手工配置。
