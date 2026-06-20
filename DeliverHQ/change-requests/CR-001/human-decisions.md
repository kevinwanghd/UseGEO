# Human Decisions: Real Scanner v1 真实诊断升级

> 本 CR 过程中需要人工拍板的决策记录。非技术决策、边界模糊、需权衡的问题记录在此。

| # | Issue | Decision | Decision By | Date |
|---|---|---|---|---|
| 1 | Q1 维度评分公式是否用 ML 模型 | 采用规则加权评分（非 ML），可解释、可审计、零依赖 | Kevin | 2026-06-19 |
| 2 | Q2 LLM provider 选哪家 | OpenAI gpt-4o-mini，通过 filter 可切换，不锁定唯一 provider | Kevin | 2026-06-19 |
| 3 | Q3 rate limit 默认阈值 | 单 IP 6 次/分钟 + 单会话 12 次/5 分钟，Transient 实现 | Kevin | 2026-06-19 |
| 4 | Q4 feature flag 上线默认值 | 生产环境 true（上线即启用）；本地开发 false | Kevin | 2026-06-19 |
| 5 | Q5 后台 weakest_dimension 是否加视觉徽章 | v1 仅展示文本，后续 CR 再优化视觉 | Kevin | 2026-06-19 |
| 6 | D1-D7 技术选型（wp_remote_get / DOMDocument / Transient / REST route / 常量+filter / 保留旧逻辑 30 天） | 全部 adopted | Kevin | 2026-06-19 |
| 7 | Lane 选 high-risk | 涉及外网抓取 + LLM 调用 + SSRF 风险，high-risk 合理 | Kevin | 2026-06-19 |
| 8 | Scanner UI 复用还是重做 | 复用现有 UI 框架，仅替换数据源，UI 改动放后续 CR | Kevin | 2026-06-19 |
| 9 | lead 邮件是否纳入本 CR | 纳入：邮件追加 score/weakest dimension/top finding/next step | Kevin | 2026-06-19 |
| 10 | 旧 lead 是否回填 scan snapshot | 不回填，避免错误数据污染 | Kevin | 2026-06-19 |
| 11 | LLM 失败时是否仍给完整结果 | 是，LLM 失败 → 规则版完整结果，前端不卡死 | Kevin | 2026-06-19 |

## 决策详情

### 决策 #1：规则加权评分（非 ML）

**背景**：5 维度评分需要一种计算方法。
**选项**：
- A：规则加权评分（信号命中/预期信号比例）
- B：ML 模型评分

**决策**：选择 A
**理由**：可解释、可审计、零依赖、上线快。
**影响范围**：scanner-score.php

---

### 决策 #2：OpenAI gpt-4o-mini

**背景**：LLM 解释层需要选择 provider。
**选项**：
- A：OpenAI gpt-4o-mini
- B：Anthropic Claude
- C：本地模型

**决策**：选择 A，通过 filter 可切换
**理由**：成熟、性价比高、有免费额度。
**影响范围**：scanner-llm.php

---

### 决策 #3：rate limit 阈值

**背景**：需要防滥用但不过度限制正常用户。
**决策**：6 次/分钟/IP + 12 次/5 分钟/会话
**理由**：正常用户足够，阻止自动化滥用；阈值可调。
**影响范围**：scanner-ratelimit.php

---

### 决策 #4：feature flag 默认值

**背景**：上线时是否默认启用真实 scanner。
**决策**：生产 true，本地 false
**理由**：生产上线即启用；本地避免真实抓取。
**影响范围**：scanner.js + scanner-api.php

---

### 决策 #5：后台徽章

**背景**：weakest_dimension 列是否加颜色徽章。
**决策**：v1 仅文本
**理由**：聚焦核心功能，后台视觉优化非 P0。
**影响范围**：inc/admin.php

## 待决策项

无。所有 Q1-Q5 和 D1-D7 均已裁决。
