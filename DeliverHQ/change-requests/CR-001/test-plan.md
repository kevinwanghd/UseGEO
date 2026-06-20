# Test Plan: Real Scanner v1 真实诊断升级

## 目标

验证 CR-001 的 8 个 Behavior 场景、6 条不变式、API 契约、lead snapshot、邮件与后台展示，不破坏 CR-LEGACY-SCAN 的整站基线。

## 测试范围

| 范围 | 覆盖 |
|---|---|
| API | `POST /wp-json/usegeo/v1/scan` 成功/错误/降级响应 |
| SSRF | localhost / 内网 / 非 http(s) / 重定向后内网 |
| Frontend | scanner.js 调用 API、loading、error、evidence、hidden snapshot |
| Lead | forms.php 保存 scan_* meta、邮件正文新增 5 行 |
| Admin | lead 列表新增 GEO Score / Weakest Dimension |
| Regression | CR-LEGACY-SCAN 首页/表单/后台基线不破坏 |

## 测试用例

### TC-A 合法公开 URL 真实抓取
- 输入：`https://example.com`
- 预期：HTTP 200，`ok=true`，5 维度分齐全，`extracted.title` 或 `extracted.h1[0]` 非空，findings ≥ 3。

### TC-B LLM 不可用降级
- 输入：不配置 `USEGEO_LLM_API_KEY`，扫描 `https://example.com`
- 预期：HTTP 200，`ok=true`，`llm_summary.available=false`，规则版 findings 正常。

### TC-C SSRF 阻断
- 输入：`http://127.0.0.1`、`http://localhost`、`file:///etc/passwd`
- 预期：HTTP 400，`error_code ∈ {invalid_url, blocked_url}`，不发起外部请求。

### TC-D 非 HTML / 超时 / 超大文件
- 输入：返回 PDF 或无法响应的 URL
- 预期：返回 `non_html_response` / `fetch_timeout` / `fetch_failed`，前端显示友好错误。

### TC-E Rate limit
- 输入：同一 IP 连续超过 6 次/分钟或同会话超过 12 次/5分钟
- 预期：HTTP 429，`error_code=rate_limited`。

### TC-F Lead snapshot
- 前置：成功 scan
- 操作：提交 Full Diagnostic 表单
- 预期：lead 保存 `scan_total_score`、5 维度分、`scan_findings_summary`、`scan_completed_at`；邮件包含 Website / GEO total score / Weakest dimension / Top finding / Recommended next step。

### TC-G Feature flag 关闭
- 前置：`USEGEO_REAL_SCANNER_ENABLED=false`
- 操作：运行 scanner
- 预期：前端不调用 REST API，走 demo mode；lead 可提交但不写 scan_* meta。

### TC-H Scan 失败仍可留资
- 前置：scanner 返回错误
- 操作：提交 Full Diagnostic 表单
- 预期：lead 保存成功；`scan_status=missing`；后台分数列显示 `-`；邮件 scan 字段为 `(scan unavailable)`。

## 自动/半自动验证命令

见 `verification-manifest.yml`。

## 当前已执行验证

- `node --check wordpress/theme/usegeo/assets/js/scanner.js`：PASS
- 静态 PHP 括号/重复函数检查：PASS（当前环境无 PHP CLI，未执行 `php -l`）
- 契约 grep/assertion：PASS

## 人工 QA 清单

- [ ] 打开首页，输入合法 URL，确认 loading → result → evidence 区显示。
- [ ] 输入非法/内网 URL，确认显示友好错误且无 stack trace。
- [ ] 关闭 feature flag，确认旧 demo scanner 仍可用。
- [ ] 成功 scan 后提交 lead，确认后台 lead 有 scan meta。
- [ ] 检查邮件正文包含 5 行 scan 摘要。
- [ ] 后台列表显示 GEO Score / Weakest Dimension。
