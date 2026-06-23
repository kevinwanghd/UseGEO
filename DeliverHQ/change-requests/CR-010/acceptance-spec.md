# Acceptance Spec: Expand Money Pages

## CR-ID
CR-010

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-CONTENT-LIBRARY
  prd_hash: 56ad78679ada
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|--------|------|----------|
| MoneyPage | 已存在的 WordPress page，需要更新正文内容 | slug, title, content_html, status |
| FAQItem | 页面 FAQ 条目，作为正文内容的一部分发布 | question, answer, page_slug |
| InternalLink | 三页之间及回 Scanner 的内部链接 | source_slug, target_path, anchor_text |
| CTA | 页面内的转化入口 | label, href, placement, intent |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 约束 | 默认值 | 说明 |
|-----------|------|------|--------|------|
| MoneyPage.slug | string | 必须为 `geo-checklist` / `ai-visibility-tools` / `best-geo-agencies` 三者之一 | 无 | 不允许创建 `-2` 重复 slug |
| MoneyPage.content_html | HTML string | 每页英文正文 2500–3500 词；至少 5 个 FAQ；至少 2 个 Scanner CTA | 无 | 内容可被 WordPress REST API 或后台编辑器保存 |
| FAQItem.question | string | 每页至少 5 条；每条问题唯一 | 无 | 面向 SEO/GEO 和 AI 摘取 |
| FAQItem.answer | string | 每条 60–140 英文词 | 无 | 不包含虚假排名承诺 |
| InternalLink.target_path | URL path | 必须为 `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/` 或 `/#scanner` | 无 | 三页互链且回 Scanner |
| CTA.href | URL path | 至少 2 个指向 `/#scanner` 或 `https://usegeo.blog/#scanner` | `/#scanner` | CTA 文案不承诺自营完整服务 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|------|----------|----------|----------|------|
| MoneyPage | existing_published_thin | draft_expanded → published_expanded | 本地内容生成、静态验证、生产 REST/API 或后台更新成功 | published_expanded |
| FAQItem | absent_or_thin | drafted → published | 内容随 MoneyPage 发布 | published |
| InternalLink | partial | verified_local → verified_production | 本地 HTML 检查与生产 HTML 检查通过 | verified_production |

### Data Invariants（数据不变式）
- 只更新三个目标 slug，不创建新 slug，不删除页面。
- 每个页面正文必须包含另外两个 money pages 的内部链接。
- 每个页面必须包含至少 2 个 Scanner CTA。
- 任何页面不得承诺“保证进入 AI 推荐/排名”或“UseGEO 自营完整 GEO 服务交付”。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|------|------|------|------|
| WordPress Pages Update | POST | `/wp-json/wp/v2/pages/{page_id}` | 更新已存在页面正文 |
| Public Page Fetch | GET | `/geo-checklist/` | 验证 GEO checklist 公开页 |
| Public Page Fetch | GET | `/ai-visibility-tools/` | 验证 AI visibility tools 公开页 |
| Public Page Fetch | GET | `/best-geo-agencies/` | 验证 Best GEO agencies 公开页 |

### Input Schema
```json
{
  "title": "string (required when updating title)",
  "content": "string HTML (required)",
  "status": "publish"
}
```

### Output Schema
```json
{
  "id": "number",
  "slug": "geo-checklist | ai-visibility-tools | best-geo-agencies",
  "status": "publish",
  "link": "https://usegeo.blog/<slug>/"
}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|--------|----------|------|------------|
| rest_forbidden | 401/403 | WordPress Application Password 或权限无效 | 停止发布，请 Kevin 重新创建或确认权限 |
| rest_post_invalid_id | 404 | 页面 ID 不存在 | 先按 slug 查询页面 ID |
| duplicate_slug_risk | 409 | 目标 slug 已存在重复风险 | 不创建新页，只更新已有目标页 |
| production_fetch_failed | 4xx/5xx | 公开 URL 无法获取 | 停止验收并记录 HTTP 状态 |

### Idempotency & Side Effects
- **幂等性**：对同一 page ID 重复 POST 相同 title/content/status，最终内容一致。
- **副作用**：会更新生产 WordPress 页面正文；不会创建 lead、发送测试邮件或修改 secret。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|------|----------|----------|
| WordPress Pages Update | 可编辑页面的 WordPress 用户权限 | Application Password Basic Auth 或已登录后台 nonce |
| Public Page Fetch | 无 | HTTPS GET |

---

## 3. Behavior Spec（行为规格）

### 场景 1：三张 money pages 扩写并发布
- **Given** 三个目标页面已存在且 slug 分别为 `geo-checklist`、`ai-visibility-tools`、`best-geo-agencies`
- **When** CR-010 内容被发布到生产 WordPress
- **Then** 三个公开 URL 均返回 HTTP 200，正文包含对应页面的新 H1、长正文、FAQ 和 Scanner CTA
- **Measurable Success** 每页 HTML 去标签后统计 2500–3500 英文词；每页至少 5 个 FAQ；每页至少 2 个 Scanner CTA

### 场景 2：商业定位和风险措辞正确
- **Given** UseGEO 的定位是 GEO 学习内容 + Scanner + partner referral / sponsor / affiliate / lead resale
- **When** 用户阅读任一 money page
- **Then** 页面可以引导用户运行 Scanner 或请求 expert follow-up / partner match，但不得承诺 UseGEO 自营完整 GEO 服务，也不得承诺 AI ranking 结果
- **Measurable Success** 静态检查中 forbidden claim 命中数为 0；partner/referral/expert-follow-up 相关 CTA 每页至少 1 处

### 场景 3：内部链接形成商业内容闭环
- **Given** 用户从任一 money page 进入
- **When** 用户阅读正文、FAQ 或底部 CTA
- **Then** 用户可以跳到另外两个 money pages，并可以回到首页 Scanner
- **Measurable Success** 每页包含另外两个页面路径各至少 1 次，`/#scanner` 或 `https://usegeo.blog/#scanner` 至少 2 次

### 场景 4：发布过程不泄漏或依赖 secret
- **Given** 发布可能使用临时 WordPress Application Password
- **When** 本地生成内容、提交代码和运行验证
- **Then** 仓库中不包含 Application Password、LLM API key、Authorization token 或 Basic Auth 明文
- **Measurable Success** secret scan 对 CR-010 outputs/evidence 和 git diff 返回 0 个命中

---

## 非功能验收

| 维度 | 指标 | 验收标准 |
|---|---|---|
| SEO 内容深度 | 词数 | 每页 2500–3500 英文词 |
| SEO/GEO 摘取 | FAQ | 每页至少 5 个 FAQ，每个回答 60–140 英文词 |
| 转化 | Scanner CTA | 每页至少 2 个 Scanner CTA |
| 内链 | 三页互链 | 每页链接另外两个 money pages |
| 安全 | secret 泄漏 | git diff 与 CR outputs 中 0 个生产 secret |
| 可用性 | 页面状态 | 三个生产 URL 均 HTTP 200 |

---

## 依赖项

| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| 已发布 WordPress 三个目标页面 | 内部 | 已就绪 | Kevin/Hermes |
| WordPress 页面发布权限 | 外部 | 需 Kevin 保持或重新创建临时 Application Password | Kevin |
| 生产公开站点 `https://usegeo.blog` | 外部 | 已就绪 | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|------|------|--------|------|
| F1 | UseGEO 不自营完整 GEO 服务，商业化聚焦内容流量、广告/赞助、affiliate、partner referral、lead resale | HANDOFF-2026-06-21-usegeo-growth.md | Kevin | 2026-06-21 |
| F2 | 三个目标页面已在生产发布并验证过 | HANDOFF-2026-06-21-usegeo-growth.md | Kevin/Hermes | 2026-06-21 |
| F3 | CR-010 范围是扩写三个 money pages、统一 CTA、FAQ 和互链 | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-21 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|------|----------|----------|----------|------|
| A1 | 继续更新现有三个 WordPress 页面，而不是新建文章或新 slug | P1 | 发布前按 slug 查询并只更新已有 page ID | 2026-06-22 | verified |
| A2 | 词数使用 HTML 去标签后的英文单词统计作为验收依据 | P2 | 本地 verifier 输出每页 word_count（必须落在 2500–3500） | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|------|----------|--------|----------|------|
| Q1 | 是否需要在本 CR 中加入具体第三方工具/agency 排名名单 | P2 | Kevin | 2026-06-22 | resolved：不加入，避免虚假 Top 10 和 sponsor/affiliate 未确认风险 |

---

## 可测试性

- [x] 每个验收条件可通过脚本或公开 HTTP 请求测试
- [x] 边界条件可重现
- [x] 性能指标可量化验证
- [x] 数据限制条件可验证
- [x] 接口格式可验证

---

## Gate 检查点

- [x] Data Spec 完整（实体、字段、限制条件、状态转换）
- [x] Interface Spec 完整（API、Input/Output、错误码、权限）
- [x] Behavior Spec 完整（至少 3 个场景：正常/异常/边界）
- [x] 验收条件明确，无未解决占位符
- [x] P0 Open Questions 已解决（status = resolved）
- [x] P0 Assumptions 已验证（status = verified）
- [x] 无模糊词或已量化
- [x] 依赖项状态已确认
- [x] 非功能需求已量化

**SpecGate 状态**：READY
