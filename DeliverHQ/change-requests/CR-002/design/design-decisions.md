# Design Decisions: LLM Summary 启用与验证

> 本 CR 无 UI 改动，只记录配置/安全设计决策。

| # | Decision | Rationale | Tradeoffs | Status |
|---|---|---|---|---|
| 1 | 不改 scanner UI | CR-001 已完成 UI，CR-002 只启用 LLM summary | 范围小 | adopted |
| 2 | API key 通过生产配置注入，不入仓 | 防泄漏 | 需要人工操作 | adopted |
| 3 | 扩展 CR-001 LLM 适配支持 `USEGEO_LLM_BASE_URL` | 代理 key 非官方 OpenAI，需要可配置 base_url | 多一个配置项 | adopted |
| 4 | 默认代理 base_url 为 `https://chat.aiprox.net` | Kevin 已提供代理地址 | 生产需手工配置 key | adopted |
| 5 | 默认模型 `gpt-5.4-mini` | 短文本诊断成本/延迟更合适 | 可切 `gpt-5.5` | adopted |
| 4 | 无 key/错 key/超时均 fallback | 不阻断 scanner | LLM 不可用时体验降级 | adopted |
| 5 | prompt 不含 PII | 隐私边界清晰 | summary 只基于页面信号 | adopted |

## 配置建议

优先方案：在 `wp-config.php` 中加入（不要提交到 Git）：

```php
define('USEGEO_LLM_API_KEY', '实际代理 key');
define('USEGEO_LLM_BASE_URL', 'https://chat.aiprox.net');
define('USEGEO_LLM_MODEL', 'gpt-5.4-mini');
```

放置位置：`/* That's all, stop editing! */` 之前。

## 验证方式

- 配置前：`llm_summary.available=false`（已在 CR-001 生产验证）
- 配置后：`llm_summary.available=true`
- 移除/注释 key 后：fallback 恢复
