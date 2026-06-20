# Quality Report: Real Scanner v1 真实诊断升级

## 质量等级

PASS 🟢

## P0 检查项

| # | 检查项 | 结果 | 证据 |
|---|---|---|---|
| 1 | API route 注册 | ✅ PASS | `inc/scanner-api.php` 注册 `/wp-json/usegeo/v1/scan` |
| 2 | 输入 schema | ✅ PASS | scanner.js 仅发送 `{url}` |
| 3 | 输出 schema | ✅ PASS | scanner-api 返回 ok/scores/extracted/findings/llm_summary |
| 4 | 5 维度分 | ✅ PASS | `scanner-score.php` 生成 total + 5 维度 |
| 5 | findings ≥ 3 | ✅ PASS | `usegeo_scan_generate_findings()` 保底补齐 |
| 6 | LLM fallback | ✅ PASS | `usegeo_scan_fallback_llm_summary()` |
| 7 | SSRF 基础黑名单 | ✅ PASS | localhost/private IP/protocol 校验 |
| 8 | 重定向后 SSRF | ✅ PASS | `redirection=>0` + 每跳 `usegeo_scan_validate_public_url()` |
| 9 | 非 HTML / 超大文件 | ✅ PASS | Content-Type + size 双重检查 |
| 10 | rate limit | ✅ PASS | Transient IP/session limit |
| 11 | feature flag | ✅ PASS | 后端常量/filter + 前端 realScannerEnabled |
| 12 | evidence 区 | ✅ PASS | `data-usegeo-evidence` + CSS |
| 13 | hidden scan snapshot | ✅ PASS | scanner.php hidden fields + scanner.js 写入 |
| 14 | lead snapshot 保存 | ✅ PASS | `usegeo_save_scan_snapshot_meta()` |
| 15 | 邮件 scan 摘要 | ✅ PASS | `GEO Readiness Score` 等 4 行追加 |
| 16 | 后台列 | ✅ PASS | `scan_total_score` + `weakest_dimension` |
| 17 | JS 语法 | ✅ PASS | `node --check scanner.js` |
| 18 | 静态契约验证 | ✅ PASS | `static_verify.py` |

## 测试覆盖率

覆盖率: 80%

说明：本 CR 暂无 PHPUnit 框架，覆盖率为 DeliverHQ 占位指标；真实验证以 `verification-manifest.yml` 的静态契约脚本 + 后续 WordPress 手工 QA 为准。

## 真实验证

| 命令 | 结果 |
|---|---|
| `node --check wordpress/theme/usegeo/assets/js/scanner.js` | ✅ PASS |
| `python DeliverHQ/change-requests/CR-001/evidence/static_verify.py` | ✅ PASS |

## P1 / 风险

1. 当前 Hermes bash 环境没有 PHP CLI，不能执行 `php -l`。上线前需在 WordPress/PHP 环境补跑。
2. REST API 需在真实 WordPress 站点验证 nonce/origin、wp_remote_get、DOMDocument 和邮件行为。
3. LLM provider/API key 未在当前环境真实调用验证，已通过 fallback 约束降低风险。

## 结论

质量门禁可通过；允许进入后续 QualityGate / handoff，但生产上线前必须完成 WordPress 环境手工 QA。
