# Design Decisions: Real Scanner v1 真实诊断升级

> 本 CR 的技术设计决策记录。架构选型、权衡、技术方案记录在此。

## 决策总表

| # | Decision | Rationale | Tradeoffs | Status |
|---|---|---|---|---|
| D1 | 后端抓取用 `wp_remote_get` | WordPress 原生 HTTP API，不需额外依赖 | 功能受限 vs 零依赖 | adopted |
| D2 | HTML 解析用 `DOMDocument` | PHP 原生扩展，不引入第三方 | 严格性 vs 零依赖 | adopted |
| D3 | rate limit 用 WP Transient | 不引入 Redis；单机够用 | 分布式一致性 vs 简单 | adopted |
| D4 | LLM 调用用 `wp_remote_post` | 同 D1，复用 WP HTTP API | 同 D1 | adopted |
| D5 | scan API 注册为 WP REST route | `register_rest_route('usegeo/v1', '/scan', ...)` | 独立端点 vs admin-post | adopted |
| D6 | feature flag 用 WP 常量 + filter | 最简方案，不需配置框架 | 灵活性 vs 复杂度 | adopted |
| D7 | 旧模拟逻辑保留在 scanner.js | feature flag 关闭时走旧路径，30 天不删 | 代码冗余 vs 回退安全 | adopted |

---

## Q1-Q5 需求不明确点裁决

### Q1：维度评分公式（规则版）

**问题**：5 维度评分如何从首页信号计算得出？

**最终决策**：采用规则加权评分。

- 每个维度按"信号命中数 / 预期信号数"比例打分（0–100）。
- total 取 5 维度加权平均。
- 默认等权（各 20%），权重可通过 `usegeo_scan_dimension_weights` filter 调整。
- 不使用 ML 模型。
- 具体信号清单与阈值如下：

| 维度 | 预期信号 | 命中规则 |
|---|---|---|
| entity_clarity | title 非空、H1 非空、meta description 非空、Organization schema 存在 | 每命中 1 个 +25 |
| content_structure | H1 存在、H2 ≥ 2、word_count ≥ 300、有 section 结构 | 每命中 1 个 +25 |
| proof_signals | case/customer/testimonial/results/ROI/trusted by 关键词命中 | 命中 ≥1 → 60；≥3 → 80；≥5 → 100 |
| comparison_readiness | compare/alternative/versus/vs/best/use cases/pricing 关键词命中 | 命中 ≥1 → 50；≥3 → 75；≥5 → 100 |
| ai_extractability | 可 index（无 noindex）、title 完整、canonical 正常、schema 存在、HTML 可解析 | 每命中 1 个 +20 |

**理由**：
1. 规则评分可解释、可审计、可机检。
2. 不引入 ML 依赖，降低部署复杂度。
3. 权重可调，留有调优空间。

**影响范围**：
- 新增评分模块（`inc/scanner-scoring.php` 或等价文件）。
- 不修改现有主题模板结构。

---

### Q2：LLM provider / 模型

**问题**：使用哪个 LLM 提供方和模型？

**最终决策**：使用 OpenAI（`gpt-4o-mini` 或等价模型）。

- 通过 `USEGEO_LLM_API_KEY` 注入 API key。
- provider 可通过 `USEGEO_LLM_PROVIDER` filter 切换。
- 模型可通过 `USEGEO_LLM_MODEL` filter 切换。
- 不锁定唯一 provider，但 v1 只实现 OpenAI 适配。
- LLM prompt 输入仅限：用户输入的目标 URL + 本次 scan 已抓取/解析的页面信号。
- LLM prompt 禁止包含：lead 邮箱、表单字段、扫描者 IP、cookie、session。

**理由**：
1. OpenAI API 成熟、稳定、有免费额度。
2. gpt-4o-mini 性价比高，适合短文本诊断。
3. filter 机制保留切换空间，不锁死。

**影响范围**：
- 新增 LLM 调用模块（`inc/scanner-llm.php` 或等价文件）。
- 需要配置 `USEGEO_LLM_API_KEY`。

---

### Q3：rate limit 默认阈值

**问题**：单 IP / 单会话限流具体阈值。

**最终决策**：

- 单 IP：6 次 / 分钟（默认），通过 `USEGEO_SCAN_RATE_LIMIT_PER_IP` filter 调整。
- 单会话：12 次 / 5 分钟（默认），通过 `USEGEO_SCAN_RATE_LIMIT_PER_SESSION` filter 调整。
- 实现方式：WP Transient（`usegeo_scan_rl_{ip}` / `usegeo_scan_rl_{session}`）。
- 超限返回 HTTP 429 + `error_code = rate_limited`。

**理由**：
1. 6 次/分钟足够正常用户使用，阻止自动化滥用。
2. Transient 不需要额外基础设施。
3. 阈值可调，上线后根据实际流量优化。

---

### Q4：feature flag 默认值

**问题**：`USEGEO_REAL_SCANNER_ENABLED` 上线时的默认值。

**最终决策**：

- 生产环境：默认 `true`（上线即启用）。
- 本地开发环境：默认 `false`（通过 `wp-config-local.php` 或 filter 控制）。
- 切换不需要部署，只需 WP 常量 / option 调整。
- 至少 30 天保留旧模拟逻辑代码路径。

**理由**：
1. 生产环境上线即启用，确保用户立即获得真实体验。
2. 本地开发默认关闭，避免开发时对外网发起真实抓取。
3. 30 天保留期确保回退安全。

---

### Q5：后台 lead 列表 weakest_dimension 视觉徽章

**问题**：后台 lead 列表的 `weakest_dimension` 列是否加颜色徽章。

**最终决策**：v1 仅展示文本。

- `weakest_dimension` 列直接显示维度名（如 `comparison_readiness`）。
- 不加颜色徽章 / 优先级标记。
- 后续 CR 可优化为视觉徽章（如红/黄/绿）。

**理由**：
1. v1 聚焦核心功能（真实抓取 + 评分 + LLM），后台视觉优化非 P0。
2. 文本展示已满足销售跟进需求。
3. 避免过度设计。

---

## 遗留技术债务

| # | 债务 | 原因 | 后续计划 |
|---|---|---|---|
| 1 | rate limit 用 Transient 单机实现 | 不引入 Redis | 如需多实例部署，迁移到 Redis 或共享存储 |
| 2 | LLM 只适配 OpenAI | v1 只实现一个 provider | 后续可扩展 Anthropic / 本地模型 |
| 3 | 评分公式权重等权 | v1 不调优 | 上线后根据真实数据调权重 |
| 4 | 旧模拟逻辑保留在 scanner.js | 回退安全 | 30 天后删除 |
| 5 | 后台 weakest_dimension 无视觉徽章 | v1 不做 | 后续 CR 优化 |
