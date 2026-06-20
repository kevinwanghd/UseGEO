# Review Report: Real Scanner v1 真实诊断升级

## 审查结论

**结论**: ✅ PASS

本轮审查基于真实 git diff、`acceptance-spec.md`、`traceability.yml`、`test-plan.md` 和静态验证结果。未发现 P0 阻断问题。

### P0 阻断问题

（无）

### P1 需修复 / 风险提示

1. 当前环境无 PHP CLI，未能执行 `php -l`。已用静态括号/重复函数检查替代；上线前仍需在 WordPress/PHP 环境执行真实 PHP 语法检查。
2. Real Scanner API 需要在真实 WordPress 环境验证 nonce/origin、REST route、wp_remote_get、DOMDocument 扩展和邮件行为。

### P2 后续优化

1. weakest_dimension 后台列 v1 仅文本，后续可加视觉徽章。
2. rate limit 当前基于 Transient，未来多实例部署可改 Redis / 共享缓存。
3. 评分权重当前默认等权，上线后根据真实数据调优。

---

## 逐场景审查

## 验收条件实现审查

| 验收条件 | 实现证据 | 审查结果 |
|---|---|---|
| API 契约：POST /wp-json/usegeo/v1/scan | `inc/scanner-api.php` 注册 REST route，`scanner.js` 调用 API | ✅ PASS |
| 真实抓取与解析 | `scanner-fetch.php` + `scanner-parse.php` | ✅ PASS |
| 5 维度评分 + findings | `scanner-score.php` | ✅ PASS |
| LLM 降级 | `scanner-llm.php` fallback summary | ✅ PASS |
| SSRF / redirect 防护 | `scanner-fetch.php` 每跳校验 + private IP 黑名单 | ✅ PASS |
| Lead snapshot 保存 | `scanner.php` hidden fields + `forms.php` post meta | ✅ PASS |
| 后台展示 | `admin.php` 新增 score / weakest dimension | ✅ PASS |
| Feature flag 回退 | `scanner-api.php` + `assets.php` + `scanner.js` | ✅ PASS |

## 代码质量审查

| 维度 | 结论 | 说明 |
|---|---|---|
| 模块拆分 | ✅ PASS | API / fetch / parse / score / LLM / rate limit 分离，职责清晰 |
| 安全边界 | ✅ PASS | SSRF 黑名单、手动重定向校验、PII 不进 LLM prompt |
| 降级路径 | ✅ PASS | LLM 不可用、feature flag off、scan 失败留资均有处理 |
| 可维护性 | ✅ PASS | 评分权重和配置通过 filter/常量调整 |
| 测试可执行性 | ⚠️ WARN | 当前环境缺 PHP CLI，需在真实 WP/PHP 环境补跑 `php -l` 和 REST 手工验证 |

## 发现的问题汇总

### P0 阻断问题

（无）

### P1 需修复 / 风险提示

1. 当前环境无 PHP CLI，未能执行 `php -l`。已用静态括号/重复函数检查替代；上线前仍需在 WordPress/PHP 环境执行真实 PHP 语法检查。
2. Real Scanner API 需要在真实 WordPress 环境验证 nonce/origin、REST route、wp_remote_get、DOMDocument 扩展和邮件行为。

### P2 后续优化

1. weakest_dimension 后台列 v1 仅文本，后续可加视觉徽章。
2. rate limit 当前基于 Transient，未来多实例部署可改 Redis / 共享缓存。
3. 评分权重当前默认等权，上线后根据真实数据调优。

---

## 逐场景审查

### 场景 1: 合法公开 URL 真实抓取

**审查结果**: ✅ PASS

- `scanner-api.php` 注册 `POST /wp-json/usegeo/v1/scan`。
- `scanner-fetch.php` 负责 URL 校验、手动 redirect、SSRF 校验、HTML 抓取。
- `scanner-parse.php` 提取 title/meta/H1/H2/schema/canonical/word_count/detected_signals。
- `scanner-score.php` 返回 5 维度分 + total。
- `scanner-score.php` 生成 ≥3 findings。

### 场景 2: LLM 不可用

**审查结果**: ✅ PASS

- `scanner-llm.php` 在 API key 缺失、HTTP 错误、响应不合 schema 时回退到 `usegeo_scan_fallback_llm_summary()`。
- 前端展示 `llm_summary.summary` 或规则 fallback，不会卡死 loading。

### 场景 3: URL 非法 / SSRF 命中

**审查结果**: ✅ PASS

- `scanner-fetch.php` 拒绝非 http/https、localhost、127.0.0.1、0.0.0.0、10/8、172.16/12、192.168/16、IPv6 loopback/ULA/link-local。
- redirect 使用 `redirection => 0` 手动处理，每一跳重新校验，避免重定向到内网。

### 场景 4: 抓取超时 / 非 HTML / 超大文件

**审查结果**: ✅ PASS

- timeout 默认 10s，可通过 filter 调整。
- `limit_response_size` 和最终 `strlen` 双重限制 2MB。
- Content-Type 非 `text/html` 返回 `non_html_response`。

### 场景 5: 触发 rate limit

**审查结果**: ✅ PASS

- `scanner-ratelimit.php` 使用 Transient 限制 IP 与 session。
- 超限返回 `WP_Error('rate_limited')`，REST 层映射 HTTP 429。

### 场景 6: lead 提交携带 scan snapshot

**审查结果**: ✅ PASS

- `scanner.php` 新增 hidden fields：total、5 维度、completed_at、findings_summary、llm_summary。
- `scanner.js` 成功 scan 后写入 hidden fields。
- `forms.php` 收集并保存 scan snapshot post meta。
- 邮件正文追加 score / weakest dimension / top finding / next step。
- `admin.php` 新增 GEO Score / Weakest Dimension 列。

### 场景 7: Feature flag 关闭真实 scanner

**审查结果**: ✅ PASS

- `scanner-api.php` 提供 `usegeo_is_real_scanner_enabled()`。
- `assets.php` 通过 `wp_localize_script` 下发 `realScannerEnabled`。
- `scanner.js` flag=false 时走 demoPayload，不调用 REST API，并且不写 scan_* hidden fields。

### 场景 8: scan 失败时 lead 表单仍可提交

**审查结果**: ✅ PASS

- `scanner.js` 错误时清空 scan_* hidden fields，仅保留 scanner_query。
- `forms.php` 缺少完整 snapshot 时写 `scan_status=missing`，不阻断 lead 保存。
- 邮件正文 scan 字段使用 `(scan unavailable)`。
- 后台分数字段为空时显示 `—`。

---

## Traceability 完整性

- `traceability.yml` 已覆盖 13 个实现触点。
- `evidence/changed-files.json` 已记录当前全部非 DeliverHQ 变更文件。
- 当前 diff 中的实现文件均已映射到 traceability。

---

## 安全审查

- SSRF：PASS（手动 redirect + 每跳校验）
- LLM PII 边界：PASS（prompt 仅发送 URL、scores、extracted、findings，不发送 lead 邮箱/表单字段/IP/cookie）
- API key：PASS（通过常量/filter 注入，不入仓）
- 错误响应：PASS（REST 层统一返回 public message）

---

## 静态验证结果

- `node --check wordpress/theme/usegeo/assets/js/scanner.js`: PASS
- PHP 文件括号/方括号/花括号平衡检查：PASS
- 重复函数名检查：PASS
- 契约断言（REST route / SSRF / hidden fields / evidence / admin columns / feature flag）：PASS

---

## 审查结论

**结论**: ✅ PASS

可进入 QualityGate，但上线前必须在真实 WordPress/PHP 环境补跑 PHP CLI 或等价语法检查与浏览器手工 QA。
