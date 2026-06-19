# UseGEO 代码健康扫描报告

> 由 DeliverHQ 老项目扫描流程生成。范围限定为当前 WordPress 主题源码，避免 DeliverHQ 自身治理脚本污染项目技术栈统计。

## 执行信息
- 项目根：`D:/Code/UseGEO`
- 扫描源码根：`wordpress/theme/usegeo`
- 扫描时间：2026-06-19 20:23:51
- Git 当前分支状态：`## master...origin/master`

## 规模与技术栈

| 类型 | 文件数 | 有效 LOC |
|---|---:|---:|
| PHP | 26 | 2283 |
| JavaScript | 4 | 385 |
| CSS | 4 | 2407 |
| **合计** | **34** | **5075** |

## 测试与验证风险

- 识别到项目测试文件：0
- DeliverHQ reverse scan 结果：26/26 PHP 源码候选均为 `test_coverage: none`。
- 结论：当前主题核心行为缺少自动化测试背书，任何逆向固化为需求前都必须人工裁决。

## 当前未提交变更影响

- 当前工作区总变更条目：37
- 其中 WordPress theme 相关变更条目：16

```text
 M wordpress/theme/usegeo/assets/css/base.css
 M wordpress/theme/usegeo/assets/css/homepage.css
 M wordpress/theme/usegeo/assets/css/single.css
 M wordpress/theme/usegeo/assets/js/scanner.js
 M wordpress/theme/usegeo/assets/js/tracking.js
 M wordpress/theme/usegeo/footer.php
 M wordpress/theme/usegeo/front-page.php
 M wordpress/theme/usegeo/inc/admin.php
 M wordpress/theme/usegeo/inc/forms.php
 M wordpress/theme/usegeo/single.php
 M wordpress/theme/usegeo/template-parts/cases/case-preview.php
 M wordpress/theme/usegeo/template-parts/community/community-intro.php
 M wordpress/theme/usegeo/template-parts/hero/hero.php
 M wordpress/theme/usegeo/template-parts/roi/roi.php
 M wordpress/theme/usegeo/template-parts/scanner/scanner.php
?? wordpress/theme/usegeo/template-parts/conversion/
```

## 复杂/大文件 Top 10

| 文件 | 有效 LOC |
|---|---:|
| `wordpress/theme/usegeo/inc/admin.php` | 512 |
| `wordpress/theme/usegeo/inc/forms.php` | 262 |
| `wordpress/theme/usegeo/inc/post-types.php` | 222 |
| `wordpress/theme/usegeo/template-parts/scanner/scanner.php` | 212 |
| `wordpress/theme/usegeo/assets/js/scanner.js` | 206 |
| `wordpress/theme/usegeo/single.php` | 185 |
| `wordpress/theme/usegeo/inc/assets.php` | 129 |
| `wordpress/theme/usegeo/template-parts/community/community-intro.php` | 104 |
| `wordpress/theme/usegeo/assets/js/tracking.js` | 100 |
| `wordpress/theme/usegeo/inc/setup.php` | 68 |

## 高频改动 Top 10（git log 计数）

| 文件 | 历史提交次数 |
|---|---:|
| `wordpress/theme/usegeo/functions.php` | 5 |
| `wordpress/theme/usegeo/inc/assets.php` | 3 |
| `wordpress/theme/usegeo/template-parts/scanner/scanner.php` | 3 |
| `wordpress/theme/usegeo/footer.php` | 2 |
| `wordpress/theme/usegeo/front-page.php` | 2 |
| `wordpress/theme/usegeo/header.php` | 2 |
| `wordpress/theme/usegeo/single.php` | 2 |
| `wordpress/theme/usegeo/assets/js/roi.js` | 2 |
| `wordpress/theme/usegeo/assets/js/scanner.js` | 2 |
| `wordpress/theme/usegeo/assets/js/tracking.js` | 2 |

## 敏感/业务关键路径线索

| 文件 | 命中关键词 | LOC |
|---|---|---:|
| `wordpress/theme/usegeo/inc/admin.php` | admin | 512 |
| `wordpress/theme/usegeo/inc/forms.php` | forms | 262 |
| `wordpress/theme/usegeo/assets/js/scanner.js` | scanner | 206 |
| `wordpress/theme/usegeo/assets/js/tracking.js` | tracking | 100 |
| `wordpress/theme/usegeo/template-parts/scanner/scanner.php` | scanner | 212 |

## DeliverHQ 建议

1. 先不要把 reverse scan 候选直接转成 acceptance-spec；ReverseSpecGate 当前 BLOCKED 是正确状态。
2. 优先人工确认这些关键模块：`front-page.php`、`functions.php`、`inc/forms.php`、`inc/admin.php`、`assets/js/scanner.js`、`assets/js/tracking.js`。
3. 若要治理当前 Real Scanner v1 / conversion 改动，下一步应创建单独 CR，并把现有 `docs/product/usegeo-real-scanner-v1-prd.md` 迁移/摘录到 `DeliverHQ/docs/PRD.md` 的功能锚点。
4. 在进入开发前，至少补齐 scanner/form/tracking 的 smoke test 或手工验证清单到 CR 的 `verification-manifest.yml`。