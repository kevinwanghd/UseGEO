# Review Report: On-site SEO/GEO Technical Foundation

## CR-ID
CR-018

## 审查日期
2026-06-22

## 审查范围
- Theme schema implementation: `wordpress/theme/usegeo/inc/seo.php` and `functions.php` require chain.
- Article template internal-link foundation: `single.php` and `assets/css/single.css`.
- Verifier: `DeliverHQ/change-requests/CR-018/evidence/verify_seo_geo_foundation.py`.
- Evidence: local verifier and production audit JSON.

## 验收条件实现审查

### 场景 1: sitemap/robots 可发现新内容
- **验收条件**：HTTP status=200，3 个 money pages 均在 page sitemap，post_count >= 20，robots 包含 Sitemap 行。
- **实现位置**：`verify_seo_geo_foundation.py --mode production-audit`；`evidence/seo-geo-production-audit.json`。
- **审查结果**：✅ PASS
- **备注**：sitemap_status=200，robots_status=200，post_count=24，money pages=3/3。

### 场景 2: 主题输出可信 schema
- **验收条件**：主题包含 Organization/WebSite/WebPage/Article/FAQPage builders，hook 到 `wp_head`。
- **实现位置**：`wordpress/theme/usegeo/inc/seo.php`；`functions.php`。
- **审查结果**：✅ PASS
- **备注**：local verifier `schema_builder_functions_present`、`schema_types_present`、`wp_head_schema_hook` 均 PASS。

### 场景 3: FAQ schema 不伪造不可见内容
- **验收条件**：FAQPage 只从 visible content 中抽取问题答案，不生成 rating/review。
- **实现位置**：`usegeo_extract_visible_faq_items()`；forbidden schema scan。
- **审查结果**：✅ PASS
- **备注**：解析 `the_content` 渲染后的 FAQ 区块；`no_fake_schema_terms` PASS。

### 场景 4: 内链技术兜底覆盖所有文章模板
- **验收条件**：`single.php` 稳定输出 `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/` 三个 money page links。
- **实现位置**：`single.php` foundation links section；`single.css` responsive grid。
- **审查结果**：✅ PASS
- **备注**：local verifier `single_template_money_links` 和 `single_foundation_css_present` PASS；生产当前 4 篇老文章缺口需部署后消失。

### 场景 5: title/meta 不堆关键词
- **验收条件**：3 个 money pages title/meta 非空且唯一，未检测到 title keyword stuffing。
- **实现位置**：生产审计 verifier。
- **审查结果**：✅ PASS
- **备注**：`money_page_unique_titles`、`money_page_unique_descriptions`、`title_keyword_stuffing_absent` PASS。

## 代码质量审查

### 逻辑缺陷
| # | 文件:行号 | 问题描述 | 严重程度 | 建议修复 |
|---|---|---|---|---|
| 1 | 无 | 未发现 P0/P1 逻辑缺陷 | - | 无 |

### 安全风险
| # | 文件:行号 | 风险类型 | 严重程度 | 建议修复 |
|---|---|---|---|---|
| 1 | `inc/seo.php` | JSON-LD 不发外部请求，不含 AggregateRating/Review/ratingValue/reviewCount | - | 无 |
| 2 | 生产部署 | 上传主题包和缓存清理有外部副作用 | P1 | 已保留为 Kevin 人工确认边界 |

### 性能问题
| # | 文件:行号 | 问题描述 | 严重程度 | 建议修复 |
|---|---|---|---|---|
| 1 | `inc/seo.php` | wp_head 内仅构建小型数组和本页 FAQ，不做网络请求 | - | 无 |

### 可维护性
| # | 文件:行号 | 问题描述 | 严重程度 | 建议修复 |
|---|---|---|---|---|
| 1 | `inc/seo.php` | schema 逻辑单独模块化，避免塞入 functions.php | - | 无 |

## Traceability 完整性
- **映射完整性**：traceability.yml 覆盖 4 个主题变更文件和 verifier。
- **变更证据**：`evidence/changed-files.json` 记录 4 个主题文件。
- **缺失的映射**：无。

## 发现的问题汇总

### P0 阻断问题（必须修复）
无。

### P1 重要问题（强烈建议修复）
1. 生产当前 JSON-LD count=0，需上传新主题包并清缓存后复验。
2. 生产当前 4 篇老文章没有 money page links，模板部署后应由 `single.php` foundation links 兜底修复。

### P2 建议改进（可选）
1. 后续如接入 SEO 插件，应增加重复 JSON-LD 检测，避免同一页面输出两套 Organization/WebSite schema。

## 审查结论

**结论**：✅ PASS

### PASS 条件
- [x] 所有验收条件有对应实现或审计证据
- [x] P0 问题数 = 0
- [x] 无 fake schema
- [x] traceability.yml 映射完整
- [x] local verifier PASS

### 阻断原因（若 BLOCKED）
无。

### 放行条件（若 PASS WITH CONDITIONS）
无。

---

**审查人**：Review Agent  
**下一步**：QualityGate / package
