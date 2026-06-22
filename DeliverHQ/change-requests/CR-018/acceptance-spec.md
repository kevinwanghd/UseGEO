# Acceptance Spec: On-site SEO/GEO Technical Foundation

## CR-ID
CR-018

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
| SeoTechnicalAudit | 当前生产 SEO/GEO 技术审计 | sitemap_status, robots_status, indexed_money_pages, post_count, jsonld_count, internal_link_coverage |
| JsonLdSchemaOutput | 主题输出的结构化数据 | type, url, name, description, datePublished, dateModified, mainEntity |
| MoneyPageLinkSet | 文章模板固定 money page links | geo_checklist_url, ai_visibility_tools_url, best_geo_agencies_url |
| VerificationResult | 本地/生产验证证据 | mode, passed, checks, evidence_path |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| SeoTechnicalAudit.sitemap_status | integer | 必须为 200 | 无 | `/sitemap.xml` redirect 到 WordPress sitemap 可接受 |
| SeoTechnicalAudit.indexed_money_pages | integer | 必须为 3 | 0 | 3 个 money pages 均出现在 page sitemap |
| SeoTechnicalAudit.post_count | integer | 必须 >=20 | 0 | 当前内容集群至少 20 篇文章可发现 |
| JsonLdSchemaOutput.type | enum | Organization/WebSite/WebPage/Article/FAQPage | 无 | 禁止 AggregateRating/Review |
| JsonLdSchemaOutput.mainEntity | array | FAQPage 仅来自可见 FAQ | [] | 不为隐藏内容生成 FAQ schema |
| MoneyPageLinkSet.* | url | 必须为站内相对路径 | 无 | `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/` |
| VerificationResult.passed | boolean | 本地必须 true | false | production 可记录当前部署状态 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| SeoTechnicalAudit | pending | pending→captured | verifier production-audit 完成 | captured |
| JsonLdSchemaOutput | absent | absent→implemented→verified | 主题代码落地并通过 verifier | verified |
| MoneyPageLinkSet | partial | partial→template_enforced→verified | single.php 固定输出 3 链接 | verified |

### Data Invariants（数据不变式）
- JSON-LD 不得包含 `AggregateRating`、`Review`、`ratingValue`、`reviewCount` 等虚假评价信号。
- FAQ schema 只能镜像页面正文中可见的 FAQ 问题与答案。
- 本 CR 不发送外部 outreach，不改 lead consent，不新增 sponsor tracking。
- DeliverHQ 产物只写入 `DeliverHQ/change-requests/CR-018/`。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Theme JSON-LD hook | WordPress hook | `wp_head` | 输出 Organization/WebSite/WebPage/Article/FAQPage JSON-LD |
| Local verifier | CLI | `verify_seo_geo_foundation.py --mode local` | 验证本地主题实现与静态安全约束 |
| Production audit | CLI | `verify_seo_geo_foundation.py --mode production-audit` | 抓取公开生产页并保存 sitemap/schema/link/title/meta 审计 |

### Input Schema
```json
{
  "mode": "local | production-audit",
  "money_pages": ["/geo-checklist/", "/ai-visibility-tools/", "/best-geo-agencies/"],
  "production_base_url": "https://usegeo.blog"
}
```

### Output Schema
```json
{
  "mode": "local",
  "passed": true,
  "checks": [{"name": "jsonld_hook_present", "passed": true}],
  "summary": {"money_page_count": 3, "post_count": 24}
}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| sitemap_unavailable | N/A | sitemap 无法访问 | 检查 WordPress sitemap/插件 |
| schema_missing | N/A | 主题缺少 JSON-LD 输出 | 修复 `inc/seo.php` |
| fake_schema_detected | N/A | 检测到虚假 rating/review schema | 删除虚假 schema |
| internal_links_missing | N/A | 文章模板或页面缺少 money page links | 修复模板或内容 |

### Idempotency & Side Effects
- **幂等性**：verifier 可重复运行；只覆盖 CR-018 evidence JSON。
- **副作用**：production-audit 只读公开 URL；local verifier 只读本地文件并写 evidence。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Theme JSON-LD hook | WordPress theme execution | WordPress 正常加载主题 |
| Local verifier | 本地文件读写 | 仓库权限 |
| Production audit | 公开网页读取 | HTTPS GET，无认证 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: sitemap/robots 可发现新内容
- **Given** 生产站点已发布 3 个 money pages 和 20+ articles
- **When** verifier 抓取 `/sitemap.xml`、`/wp-sitemap-posts-page-1.xml`、`/wp-sitemap-posts-post-1.xml`、`/robots.txt`
- **Then** HTTP status=200，3 个 money pages 均在 page sitemap，post_count >= 20，robots 包含 Sitemap 行
- **Measurable Success** sitemap_status=200, indexed_money_pages=3, post_count>=20

### 场景 2: 主题输出可信 schema
- **Given** WordPress 加载 UseGEO 主题页面
- **When** `wp_head` 执行 JSON-LD hook
- **Then** 首页输出 Organization/WebSite/WebPage；文章输出 Article；页面输出 WebPage；有可见 FAQ 时输出 FAQPage
- **Measurable Success** local verifier 检测到 5 类 schema builder，fake_schema_hits=0

### 场景 3: FAQ schema 不伪造不可见内容
- **Given** 页面正文存在 `FAQ` 区块和可见 H3 问题/答案
- **When** schema builder 解析 FAQ
- **Then** 只把可见问题与答案写入 FAQPage.mainEntity，不生成隐藏 FAQ，不生成 review/rating
- **Measurable Success** FAQ parser 存在 visible-content guard，forbidden schema keywords=0

### 场景 4: 内链技术兜底覆盖所有文章模板
- **Given** 任意 WordPress post 使用 `single.php` 渲染
- **When** 用户阅读文章页
- **Then** 页面模板稳定提供 3 个 money page links：GEO checklist、AI visibility tools、Best GEO agencies
- **Measurable Success** `single.php` 同时包含 3 个 money page paths，production audit 当前缺口有记录

### 场景 5: title/meta 不堆关键词
- **Given** 3 个 money pages 已发布
- **When** production-audit 抓取页面 HTML
- **Then** 每页 title 非空且唯一，meta description 非空且唯一，title 不重复堆叠同一关键词超过 2 次
- **Measurable Success** money_page_unique_titles=3, money_page_unique_descriptions=3, keyword_stuffing_hits=0

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 安全 | fake schema | 0 个 AggregateRating/Review/ratingValue/reviewCount |
| 性能 | JSON-LD hook | 只在 `wp_head` 输出轻量数组；不发外部请求 |
| 可用性 | sitemap | HTTP 200，公开可读 |
| 可维护性 | 主题改动 | 新增 `inc/seo.php` 并由 `functions.php` require，避免塞回大文件 |
| 可测试性 | verifier | `--mode local` 必须 PASS |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| WordPress core sitemap | 内部 | 已启用，生产 `/sitemap.xml` redirect 到 `/wp-sitemap.xml` | Hermes |
| Money pages | 内部 | 已由 CR-010 发布 | Hermes |
| Content clusters | 内部 | 已由 CR-011/013/014/015 发布 | Hermes |
| WordPress production upload/cache purge | 外部人工 | 待 Kevin 确认后执行 | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | CR-018 范围包含 sitemap、schema、internal links、title/meta | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F2 | 生产当前 JSON-LD count=0，需要主题侧补最小 schema | production-audit 初查 | Hermes | 2026-06-22 |
| F3 | 生产 sitemap 可访问且包含 3 个 money pages 和 20+ posts | production-audit 初查 | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | WordPress core sitemap 满足当前阶段使用，不新增 sitemap 插件 | P1 | production-audit 验证 sitemap | 2026-06-22 | verified |
| A2 | 主题侧 JSON-LD 不与现有插件重复，因为当前生产 JSON-LD=0 | P1 | production-audit 抓取 5 个样例页面 HTML | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否立即上传生产主题包并清缓存 | P0 | Kevin | 2026-06-22 | resolved：本 CR 先生成包，生产上传需 Kevin 确认 |

---

## 可测试性
- [x] sitemap 与 robots 可通过公开 URL 审计
- [x] schema builder 和禁止 fake schema 可静态检查
- [x] 文章模板 money page links 可静态检查
- [x] money page title/meta 唯一性可生产审计
- [x] production deploy 前可生成 WordPress-compatible ZIP

**SpecGate 状态**：READY
