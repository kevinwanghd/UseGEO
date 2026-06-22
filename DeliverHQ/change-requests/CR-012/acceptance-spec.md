# Acceptance Spec: Content Template & Publishing System

## CR-ID
CR-012

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
| ArticleBrief | 单篇文章写作输入 | primary_keyword, search_intent, target_reader, related_money_page, scanner_cta, partner_angle |
| ArticleTemplate | 标准文章结构 | opening, quick_answer, main_sections, checklist, example, common_mistakes, faq, cta |
| PublishingChecklist | 发布前验收清单 | internal_links, scanner_cta, faq, forbidden_claims, verifier |
| SchemaPlan | FAQ/Article JSON-LD 后续规划 | visible_faq_only, no_fake_schema, rendered_validation |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|-----------|------|------|--------|------|
| ArticleBrief.primary_keyword | string | 必填，1 个主关键词 | 无 | 文章主搜索意图 |
| ArticleBrief.secondary_keywords | list | 必填，3–6 个 | [] | 支撑主题覆盖 |
| ArticleBrief.internal_links | list | 至少 3 个 | [] | 至少包含 Scanner 或 money page |
| ArticleTemplate.quick_answer | HTML block | 必填，60–120 英文词规划 | 无 | 可被 AI/search 摘取 |
| ArticleTemplate.faq | HTML block | 至少 4 个 FAQ item | 无 | 可被未来 FAQ schema 镜像 |
| PublishingChecklist.forbidden_claims | list | 必填 | 无 | AI ranking / precise all-AI tracking / SEO is dead |
| SchemaPlan.visible_faq_only | boolean | 必须为 true | true | schema 只能镜像可见 FAQ |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|------|----------|----------|----------|------|
| ArticleBrief | absent | drafted → verified | verifier 检查字段完整 | verified |
| ArticleTemplate | absent | drafted → verified | verifier 检查结构完整 | verified |
| PublishingChecklist | absent | drafted → verified | verifier 检查 checklist 完整 | verified |

### Data Invariants（数据不变式）
- 模板必须要求至少 3 个 internal links。
- 模板必须要求至少 1 个 Scanner CTA。
- 模板必须包含 Quick answer、FAQ、Common mistakes 或 checklist。
- Schema plan 不允许为不可见内容生成结构化数据。
- 模板不得鼓励承诺 AI ranking 或精准追踪所有 AI 推荐。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|------|------|------|------|
| Local Template Verifier | CLI | `verify_content_template.py --mode local` | 验证模板产物完整性 |

### Input Schema
```json
{
  "template_doc": "DeliverHQ/docs/CONTENT-TEMPLATE.md",
  "brief_template": "outputs/article-brief-template.yml",
  "html_skeleton": "outputs/article-html-skeleton.html",
  "publishing_checklist": "outputs/publishing-checklist.md",
  "schema_plan": "outputs/faq-jsonld-plan.md"
}
```

### Output Schema
```json
{
  "passed": true,
  "checks": [{"name": "required_fields", "passed": true}]
}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|--------|----------|------|------------|
| missing_template | N/A | 模板文件缺失 | 补齐文件后重跑 verifier |
| missing_required_field | N/A | required field 缺失 | 补齐字段 |
| unsafe_schema_plan | N/A | schema plan 未声明 visible FAQ only | 补充 schema 约束 |

### Idempotency & Side Effects
- **幂等性**：重复运行 verifier 不修改模板文件，只更新 evidence JSON。
- **副作用**：只写 `evidence/content-template-result.json`，不发布生产内容。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|------|----------|----------|
| Local Template Verifier | 本地文件读写 | 仓库文件权限 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: 模板 required fields 完整
- **Given** `CONTENT-TEMPLATE.md` 和 `article-brief-template.yml` 已创建
- **When** verifier 运行
- **Then** keyword、search intent、target reader、related money page、scanner CTA、outline、FAQ、internal links、partner angle 均被检测到
- **Measurable Success** required field missing count = 0

### 场景 2: 固定文章结构完整
- **Given** `article-html-skeleton.html` 已创建
- **When** verifier 运行
- **Then** H1、opening、Quick answer、Main sections、Checklist/criteria、Example、Common mistakes、FAQ、Next step CTA 均存在
- **Measurable Success** required section missing count = 0

### 场景 3: 发布检查防止低质量内容上线
- **Given** `publishing-checklist.md` 已创建
- **When** verifier 运行
- **Then** checklist 要求至少 3 内链、至少 1 Scanner CTA、FAQ、forbidden claims、local verifier、production verifier
- **Measurable Success** checklist required item missing count = 0

### 场景 4: Schema 规划安全
- **Given** `faq-jsonld-plan.md` 已创建
- **When** verifier 运行
- **Then** schema plan 明确 visible FAQ only、no fake schema、browser/rich-results validation
- **Measurable Success** unsafe schema plan findings = 0

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 可复用性 | 模板字段 | required fields 100% 覆盖 |
| 可验证性 | verifier | exit 0 且 evidence JSON passed=true |
| 安全 | secret 泄漏 | 不使用生产 password/token/secret |
| SEO/GEO | 内容结构 | Quick answer、FAQ、internal links、Scanner CTA 都明确 |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| CR-010 money pages | 内部 | 已完成 | Hermes |
| CR-011 basics articles | 内部 | 已完成 | Hermes |
| DeliverHQ docs directory | 内部 | 已就绪 | Hermes |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|------|------|--------|------|
| F1 | CR-012 目标是建立统一内容模板 | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F2 | 后续文章必须有 quick answer、内链、scanner CTA | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F3 | FAQ schema 只做规划，不在本 CR 实现 | CR-012 scope | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|------|----------|----------|----------|------|
| A1 | 模板放在 `DeliverHQ/docs/CONTENT-TEMPLATE.md` 作为长期治理文档 | P1 | verifier 检查文件存在和字段完整 | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|------|----------|--------|----------|------|
| Q1 | 是否本 CR 发布生产文章 | P2 | Kevin | 2026-06-22 | resolved：不发布，CR-012 是模板系统 |

---

## 可测试性
- [x] 每个验收条件可自动化测试
- [x] 边界条件可重现
- [x] 内容模板完整性可验证
- [x] 无外部 API 依赖

**SpecGate 状态**：READY
