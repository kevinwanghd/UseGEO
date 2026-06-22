# Acceptance Spec: Publish GEO Basics Cluster

## CR-ID
CR-011

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-CONTENT-LIBRARY
  prd_hash: 77f775574a7a
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|--------|------|----------|
| BasicsArticle | WordPress post 文章 | slug, title, content_html, status, category |
| FAQItem | 文章 FAQ 内容块 | question, answer, article_slug |
| InternalLink | 内容内链 | source_slug, target_path, anchor_text |
| CTA | Scanner 转化入口 | label, href, placement |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|-----------|------|------|--------|------|
| BasicsArticle.slug | string | 5 个目标 slug 之一；已存在则更新，不存在则创建 | 无 | 避免重复 `-2` slug |
| BasicsArticle.content_html | HTML string | 1200–1800 英文词；包含 Quick answer、FAQ、CTA、内链 | 无 | 可由 WordPress REST API 保存 |
| FAQItem.question | string | 每篇至少 4 条 | 无 | 问题必须真实回答搜索意图 |
| InternalLink.target_path | URL path | 每篇至少 2 个内部链接 | 无 | 指向 money pages / Scanner / basics 相关文章 |
| CTA.href | URL path | 至少 1 个 `/#scanner` | `/#scanner` | CTA 不承诺 UseGEO 自营服务 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|------|----------|----------|----------|------|
| BasicsArticle | absent_or_existing_thin | drafted → published | 本地内容生成、验证、REST 发布成功 | published |
| FAQItem | absent | drafted → published | 随文章发布 | published |

### Data Invariants（数据不变式）
- 文章不得承诺 AI ranking 或精准追踪所有 AI 推荐。
- `GEO vs SEO` 必须明确 GEO 是 SEO 的补充，不贬低 SEO。
- 每篇文章必须包含 Scanner CTA 和至少 2 个内部链接。
- 发布时不写入或提交任何 secret。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|------|------|------|------|
| WordPress Post Query | GET | `/wp-json/wp/v2/posts?slug={slug}` | 查询是否已有文章 |
| WordPress Post Update | POST | `/wp-json/wp/v2/posts/{post_id}` | 更新已有文章 |
| WordPress Post Create | POST | `/wp-json/wp/v2/posts` | 创建新文章 |
| Public Post Fetch | GET | `/<slug>/` | 验证生产公开文章 |

### Input Schema
```json
{
  "title": "string",
  "slug": "string",
  "content": "HTML string",
  "status": "publish",
  "categories": ["number"]
}
```

### Output Schema
```json
{
  "id": "number",
  "slug": "string",
  "status": "publish",
  "link": "https://usegeo.blog/<slug>/"
}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|--------|----------|------|------------|
| rest_forbidden | 401/403 | 发布权限无效 | 停止发布，要求 Kevin 重新配置临时 Application Password |
| duplicate_slug_risk | 409 | slug 重复风险 | 查询已有 slug 并更新，不创建重复文章 |
| production_fetch_failed | 4xx/5xx | 生产 URL 无法获取 | 阻断验收 |

### Idempotency & Side Effects
- **幂等性**：同 slug 重复发布应更新同一 post，不创建重复 slug。
- **副作用**：创建或更新生产 WordPress posts；不创建 lead，不发测试邮件，不改主题代码。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|------|----------|----------|
| WordPress posts create/update | 可编辑 posts 的 WordPress 用户权限 | Application Password Basic Auth |
| Public fetch | 无 | HTTPS GET |

---

## 3. Behavior Spec（行为规格）

### 场景 1: 5 篇基础文章生成并发布
- **Given** CR-011 的 5 个目标主题和 slug 已定义
- **When** 内容发布到 WordPress
- **Then** 5 个生产 URL 均返回 HTTP 200，且内容包含标题、Quick answer、FAQ、内链、Scanner CTA
- **Measurable Success** 每篇 1200–1800 英文词；FAQ 数 >= 4；Scanner CTA >= 1

### 场景 2: GEO basics 内容定位正确
- **Given** UseGEO 是学习内容 + Scanner + partner referral 路径
- **When** 读者阅读任一 basics article
- **Then** 内容解释概念、差异、信号和执行建议，但不承诺 AI ranking 或精准追踪所有 AI 推荐
- **Measurable Success** forbidden claim scan 命中 0

### 场景 3: 内链形成 topic cluster
- **Given** 读者从任一 basics article 进入
- **When** 阅读正文、FAQ 或 next steps
- **Then** 可以进入 Scanner、money pages 或其他相关 basics 文章
- **Measurable Success** 每篇至少 2 个内部链接，5 篇合计覆盖 `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/`

### 场景 4: 发布不产生重复 slug 或 secret 泄漏
- **Given** 生产站点可能已有相近文章
- **When** REST 发布执行
- **Then** 已有 slug 被更新，不存在 slug 被创建；仓库与 evidence 不包含 Application Password 明文
- **Measurable Success** production result 中 5 个 slug 唯一；secret token scan 为 0

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| SEO 内容深度 | 词数 | 每篇 1200–1800 英文词 |
| GEO 摘取 | Quick answer + FAQ | 每篇 1 个 Quick answer、至少 4 FAQ |
| 转化 | Scanner CTA | 每篇至少 1 个 Scanner CTA |
| 内链 | Topic cluster | 每篇至少 2 个内部链接 |
| 安全 | secret 泄漏 | CR 文件中 0 个生产 secret |
| 可用性 | 页面状态 | 5 个生产 URL 均 HTTP 200 |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| WordPress REST posts API | 外部 | 已就绪 | Kevin |
| 临时 Application Password | 外部 | 需 Kevin 提供/保持有效，发布后撤销 | Kevin |
| UseGEO money pages | 内部 | 已发布 | Hermes |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|------|------|--------|------|
| F1 | CR-011 范围是发布 5 篇 GEO Basics 内容集群文章 | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F2 | UseGEO 商业定位是学习内容 + Scanner + partner referral / sponsor / affiliate / lead resale | HANDOFF / PRD | Kevin | 2026-06-21 |
| F3 | 生产已有部分相近 posts，发布时应优先更新已有 slug | WordPress public REST API | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|------|----------|----------|----------|------|
| A1 | 既有相近文章可作为 CR-011 的更新目标，不需要保留旧薄内容 | P1 | 按 slug 查询并更新已有 post | 2026-06-22 | verified |
| A2 | 1200–1800 英文词用 HTML 去标签后的英文单词统计作为验收依据 | P2 | 本地 verifier 输出 word_count | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|------|----------|--------|----------|------|
| Q1 | 是否本 CR 同时实现 FAQ schema | P2 | Kevin | 2026-06-22 | resolved：不做，后续单独 CR |

---

## 可测试性
- [x] 每个验收条件可自动化测试
- [x] 边界条件可重现
- [x] 内容指标可量化验证
- [x] 接口契约可验证

**SpecGate 状态**：READY
