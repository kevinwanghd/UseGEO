# Acceptance Spec: Article Page Conversion Redesign

## CR-ID
CR-021

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-CONTENT-LIBRARY
  prd_hash: 56ad78679ada
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|---|---|---|
| ArticlePageTemplate | WordPress article template | hero, toc, content, sidebar_cta, final_cta, related_links |
| ArticleContentNormalization | 正文清理规则 | duplicate_h1_removed, toc_levels, heading_ids |
| ConversionPath | 文章页转化路径 | scanner_cta, money_page_links, sponsor_slot |
| VerificationResult | 本地/生产验证证据 | mode, passed, checks |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| ArticleContentNormalization.duplicate_h1_removed | boolean | 必须 true | false | 正文开头 H1 与 post title 重复时移除 |
| ArticleContentNormalization.toc_levels | array | 只允许 H2 | [2] | 不把 FAQ H3 长问题放进 TOC |
| ConversionPath.scanner_cta | string | 至少 2 个，标签一致 | 无 | 主要 CTA 为 Run scanner |
| ConversionPath.money_page_links | array | 必须 3 个 | 无 | `/geo-checklist/`, `/ai-visibility-tools/`, `/best-geo-agencies/` |
| ArticlePageTemplate.sponsor_slot | string | 必须保留 `article_sidebar` | 无 | sponsor inventory 继续可用 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| ArticlePageTemplate | noisy | noisy→redesigned→verified | 模板/CSS 更新并通过 verifier | verified |
| ArticleContentNormalization | duplicate_h1 | duplicate_h1→single_h1 | H1 strip helper 生效 | single_h1 |

### Data Invariants（数据不变式）
- 不改变文章 URL、post content、表单字段名。
- 不删除 Scanner CTA。
- 不删除 sponsor slot。
- 不删除 3 个 money page foundation links。
- 不新增 fake screenshot 或外部图片依赖。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Article template | PHP render | `single.php` | 渲染文章详情页 |
| Article stylesheet | CSS | `assets/css/single.css` | 文章页视觉样式 |
| Local verifier | CLI | `verify_article_redesign.py --mode local` | 静态验证模板/CSS |
| Production verifier | CLI | `verify_article_redesign.py --mode production` | 抓取示例 URL 验证上线结果 |

### Input Schema
```json
{
  "mode": "local | production",
  "sample_url": "https://usegeo.blog/geo-for-content-marketing-teams/"
}
```

### Output Schema
```json
{
  "mode": "local",
  "passed": true,
  "checks": [{"name": "duplicate_h1_removed", "passed": true}]
}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| duplicate_h1 | N/A | 示例页 H1 多于 1 | 修复 H1 strip |
| toc_polluted | N/A | TOC 包含 H3 FAQ 长问题 | 限制 TOC H2 |
| missing_conversion_path | N/A | 缺少 scanner 或 money page links | 修复模板 |
| missing_sponsor_slot | N/A | article_sidebar sponsor slot 丢失 | 修复模板 |

### Idempotency & Side Effects
- **幂等性**：verifier 可重复运行，只覆盖 CR-021 evidence JSON。
- **副作用**：production verifier 只读公开 URL；本地 verifier 只读文件。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Theme render | WordPress theme execution | WordPress 正常加载主题 |
| Local verifier | 本地文件读写 | 仓库权限 |
| Production verifier | 公开 URL GET | 无认证 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: 示例文章页只有一个 H1
- **Given** 文章正文第一段可能包含与标题重复的 H1
- **When** `single.php` 渲染正文
- **Then** 重复 H1 被移除，页面只有 hero title 一个 H1
- **Measurable Success** production verifier h1_count = 1

### 场景 2: TOC 不被长 FAQ 问题污染
- **Given** 正文包含 H2 和 H3 FAQ
- **When** 生成 TOC
- **Then** TOC 只包含 H2，且每项文字长度不超过 90 字符
- **Measurable Success** local verifier 检测 H3 不进入 toc_items

### 场景 3: 转化路径清晰但不过载
- **Given** 用户阅读文章页
- **When** 查看 hero/sidebar/footer/foundation 区块
- **Then** Scanner CTA 与 3 个 money pages 保留，但不出现重复中段 CTA 堆叠
- **Measurable Success** local verifier 检测 no mid-cta duplication，foundation links = 3

### 场景 4: Sponsor slot 不丢失
- **Given** sponsor inventory 已支持 article_sidebar
- **When** 渲染文章页
- **Then** 仍调用 `usegeo_render_sponsor_slot('article_sidebar')`
- **Measurable Success** local verifier 检测 sponsor slot retained

### 场景 5: 视觉系统更可信
- **Given** 旧页面卡片、阴影、渐变过多
- **When** 新 CSS 加载
- **Then** 页面采用更克制的 editorial card/rail system，减少大阴影和过度渐变，移动端单列
- **Measurable Success** verifier 检测旧类移除和新类存在，CSS 无语法级明显错误

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 可读性 | H1 | production h1_count = 1 |
| 转化 | CTA | scanner CTA + 3 money links 保留 |
| SEO | URL/content | 不改 slug，不改文章正文 |
| 可维护性 | 文件范围 | 仅 `single.php` + `single.css` + CR verifier |
| 可测试性 | verifier | local mode 必须 PASS |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| WordPress single template | 内部 | 已存在 | Hermes |
| Content clusters | 内部 | 已发布 | Hermes |
| Production upload/cache purge | 外部人工 | 待 Kevin 确认 | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | Kevin 认为文章页丑且影响转化 | 用户反馈 | Kevin | 2026-06-22 |
| F2 | 示例页当前 H1 count = 2，CTA count = 19 | production audit | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | 先改全站 single article 模板，比逐篇改内容更高杠杆 | P1 | production sample verifier | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否立即上传生产主题包并清缓存 | P0 | Kevin | 2026-06-22 | resolved：本 CR 先本地完成并打包，生产上传需 Kevin 确认 |

---

## 可测试性
- [x] duplicate H1 可生产验证
- [x] TOC H2-only 可静态验证
- [x] money page links 可静态验证
- [x] sponsor slot 可静态验证
- [x] production deploy 后可通过 sample URL 验证

**SpecGate 状态**：READY
