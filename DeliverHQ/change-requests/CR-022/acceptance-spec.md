# Acceptance Spec: Public Page Visual Unification

## CR-ID
CR-022

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
| PublicPageTemplate | WordPress page template | hero, content, action, foundation_links |
| PageContentNormalization | 普通页正文清理规则 | duplicate_h1_removed, content_preserved |
| SiteVisualSystem | 统一 public page 视觉系统 | public_page_classes, article_classes, nav_footer |
| VerificationResult | 本地/生产验证证据 | mode, passed, checks |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| PageContentNormalization.duplicate_h1_removed | boolean | 必须 true | false | 普通页正文首个重复 H1 移除 |
| PublicPageTemplate.foundation_links | array | 3 个 money pages | 无 | 保留核心内链路径 |
| SiteVisualSystem.public_page_classes | array | 必须包含 `usegeo-public-page` 前缀 | 无 | 普通页统一样式 |
| VerificationResult.passed | boolean | local 必须 true | false | production 上传后复验 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| PublicPageTemplate | basic | basic→unified→verified | page.php/base.css 更新并通过 verifier | verified |
| PageContentNormalization | duplicate_h1 | duplicate_h1→single_h1 | strip helper 生效 | single_h1 |

### Data Invariants（数据不变式）
- 不改变页面 URL、post content、表单字段名。
- 不删除 Scanner CTA 或 money page links。
- 不改首页结构。
- 不新增外部图片依赖或 fake screenshot。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Page template | PHP render | `page.php` | 渲染普通页面/money pages |
| Base stylesheet | CSS | `assets/css/base.css` | public page visual system |
| Local verifier | CLI | `verify_public_page_unification.py --mode local` | 静态验证模板/CSS |
| Production verifier | CLI | `verify_public_page_unification.py --mode production` | 抓取多个 public pages 验证上线结果 |

### Input Schema
```json
{"mode":"local | production","sample_paths":["/geo-checklist/","/ai-visibility-tools/","/best-geo-agencies/","/about/"]}
```

### Output Schema
```json
{"mode":"local","passed":true,"checks":[{"name":"page_template_classes","passed":true}]}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| duplicate_h1 | N/A | money page H1 多于 1 | 修复 page H1 strip |
| missing_public_classes | N/A | page.php/base.css 缺少统一 class | 修复模板/CSS |
| missing_conversion_links | N/A | money page 缺少 scanner/money links | 修复模板或内容 |

### Idempotency & Side Effects
- **幂等性**：verifier 可重复运行，只覆盖 CR-022 evidence JSON。
- **副作用**：production verifier 只读公开 URL。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Theme render | WordPress theme execution | WordPress 正常加载主题 |
| Local verifier | 本地文件读写 | 仓库权限 |
| Production verifier | 公开 URL GET | 无认证 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: money pages 只有一个 H1
- **Given** page content 可能含有和 page title 重复的 H1
- **When** `page.php` 渲染
- **Then** 重复首个 H1 被移除，只保留模板 hero H1
- **Measurable Success** production verifier 对 3 个 money pages 检测 h1_count=1

### 场景 2: 普通页面使用统一 visual system
- **Given** `/about/`、`/articles/`、`/cases/` 使用 page.php
- **When** 页面渲染
- **Then** HTML 包含 `usegeo-public-page`、`usegeo-public-page-hero`、`usegeo-public-page-content`
- **Measurable Success** production verifier class checks PASS

### 场景 3: money page 转化路径保留
- **Given** 3 个 money pages 已发布
- **When** 页面渲染
- **Then** scanner CTA 和 3 个 money page 互链仍存在
- **Measurable Success** production verifier 检测 links present

### 场景 4: 首页不被结构性改动影响
- **Given** 首页使用 `front-page.php`
- **When** 本 CR 合入
- **Then** 不修改 `front-page.php`，首页仍 200
- **Measurable Success** verifier 确认 front-page.php not in changed files and production homepage 200

### 场景 5: 文章页 redesign 与普通页视觉一致
- **Given** CR-021 article redesign 已本地完成
- **When** CR-022 打包
- **Then** `single.php/single.css` 与 public page styles 使用同一主题变量和视觉语言
- **Measurable Success** local verifier 同时调用 CR-021 local verifier PASS

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 可读性 | H1 | money page h1_count=1 |
| 转化 | Links | scanner + money links 保留 |
| 安全 | Scope | 不改 URL/form fields/admin |
| 可维护性 | 文件范围 | `page.php` + `base.css` + CR verifier |
| 可测试性 | verifier | local mode 必须 PASS |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| CR-021 Article redesign | 内部 | 已本地完成并 push，待同包上传 | Hermes |
| Money pages | 内部 | 已发布 | Hermes |
| Production upload/cache purge | 外部人工 | 待 Kevin 确认 | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | Kevin 希望所有页面统一改掉再上传 | 用户反馈 | Kevin | 2026-06-22 |
| F2 | 3 个 money pages 当前 H1 count=2 | production audit | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | 首页保持结构不动，先统一文章页和普通页即可 | P1 | production homepage smoke | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否立即上传生产主题包并清缓存 | P0 | Kevin | 2026-06-22 | resolved：本 CR 先本地完成并打包，生产上传需 Kevin 确认 |

---

## 可测试性
- [x] page.php classes 可静态验证
- [x] duplicate H1 strip 可静态和生产验证
- [x] money links 可生产验证
- [x] homepage smoke 可生产验证
- [x] CR-021 local verifier 可复用

**SpecGate 状态**：READY
