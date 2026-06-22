# Acceptance Spec: Publish GEO Use Case Cluster

## CR-ID
CR-015

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-CONTENT-LIBRARY
  prd_hash: 77f775574a7a
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|---|---|---|
| UseCaseArticle | WordPress post use-case article | slug, title, content_html, status, category |
| ArticleMetadata | 发布元数据 | primary_keyword, required_links, category, wp_slug |
| VerificationResult | 本地/生产验收结果 | word_count, faq_count, scanner_cta_count, internal_link_count, forbidden_hits |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| UseCaseArticle.slug | string | 5 个目标 slug 之一 | 无 | 不创建重复 `-2` |
| UseCaseArticle.content_html | HTML string | 1200–1800 英文词；FAQ >=4；CTA >=1；internal links >=3 | 无 | 按 CONTENT-TEMPLATE 结构 |
| VerificationResult.forbidden_hits | list | 必须为空 | [] | AI ranking / precise all-AI tracking / self-service promise |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| UseCaseArticle | absent_or_existing | drafted → local_verified → published → production_verified | verifier 与 REST 发布成功 | production_verified |

### Data Invariants（数据不变式）
- 每篇文章必须至少 3 个内部链接和 1 个 Scanner CTA。
- Ecommerce 文章不得强套 B2B SaaS 场景。
- Agency 文章需要 potential sponsor / partner angle 但不得伪装赞助。
- secrets 不得写入文件、evidence、git history。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| WordPress Post Query | GET | `/wp-json/wp/v2/posts?slug={slug}` | 查询已有 post |
| WordPress Post Update | POST | `/wp-json/wp/v2/posts/{post_id}` | 更新 post |
| WordPress Post Create | POST | `/wp-json/wp/v2/posts` | 创建 post |
| Public Post Fetch | GET | `/<slug>/` | 生产验收 |

### Input Schema
```json
{"title":"string","slug":"string","content":"HTML string","status":"publish","categories":["number"]}
```

### Output Schema
```json
{"id":"number","slug":"string","status":"publish","link":"https://usegeo.blog/<slug>/"}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| rest_forbidden | 401/403 | 权限无效 | 停止发布 |
| production_fetch_failed | 4xx/5xx | 生产 URL 无法获取 | 阻断验收 |

### Idempotency & Side Effects
- **幂等性**：同 slug 重复发布更新同一 post。
- **副作用**：创建/更新生产 WordPress posts；不创建 lead，不改主题代码。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| WordPress posts create/update | 可编辑 posts 用户 | Application Password Basic Auth |
| Public fetch | 无 | HTTPS GET |

---

## 3. Behavior Spec（行为规格）

### 场景 1: 5 篇 use case articles 发布
- **Given** 5 个目标主题和 slug 已定义
- **When** 发布脚本执行
- **Then** 5 个生产 URL HTTP 200，内容符合模板结构
- **Measurable Success** 每篇 1200–1800 words、FAQ >=4、CTA >=1、internal links >=3

### 场景 2: 每篇满足 use case 要求
- **Given** CR-015 backlog 对每篇有场景要求
- **When** verifier 运行
- **Then** 每篇包含对应 markers
- **Measurable Success** required marker missing count = 0

### 场景 3: 风险措辞正确
- **Given** use-case 内容会引导 partner/referral
- **When** 用户阅读文章
- **Then** 不承诺 AI ranking 或 UseGEO 自营完整交付
- **Measurable Success** forbidden claim scan = 0

### 场景 4: 发布不泄漏 secret
- **Given** 使用临时 Application Password
- **When** 发布和提交
- **Then** 密码不进入 CR 文件/evidence/git
- **Measurable Success** secret fragment scan = 0

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 内容深度 | 词数 | 每篇 1200–1800 英文词 |
| 转化 | Scanner CTA | 每篇 >=1 |
| 内链 | Topic cluster | 每篇 >=3 |
| 安全 | secret | 0 命中 |
| 可用性 | 生产 URL | 5/5 HTTP 200 |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| CONTENT-TEMPLATE.md | 内部 | 已就绪 | Hermes |
| WordPress Application Password | 外部 | Kevin 决定批量 CR 完成后撤销 | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | CR-015 范围是 5 篇 use case 文章 | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F2 | 内容需按 CONTENT-TEMPLATE 生成 | CR-012 | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | 目标 slugs 当前不存在，可创建新 posts | P1 | REST 查询 | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否本 CR 插入 sponsor 内容 | P2 | Kevin | 2026-06-22 | resolved：不插入，后续确认 sponsor 后单独处理 |

---

## 可测试性
- [x] 每个验收条件可自动化测试
- [x] 生产 URL 可公开验证
- [x] forbidden claims 可扫描

**SpecGate 状态**：READY
