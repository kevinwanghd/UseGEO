# Writeback Report: Real Scanner v1 真实诊断升级

## 归档日期

2026-06-21

## 代码变更

本 CR 在 `feature/CR-001` worktree 中实现 Real Scanner v1：

- 新增后端 scanner 模块：
  - `wordpress/theme/usegeo/inc/scanner-api.php`
  - `wordpress/theme/usegeo/inc/scanner-fetch.php`
  - `wordpress/theme/usegeo/inc/scanner-parse.php`
  - `wordpress/theme/usegeo/inc/scanner-score.php`
  - `wordpress/theme/usegeo/inc/scanner-llm.php`
  - `wordpress/theme/usegeo/inc/scanner-ratelimit.php`
- 修改前端：
  - `wordpress/theme/usegeo/assets/js/scanner.js`
  - `wordpress/theme/usegeo/template-parts/scanner/scanner.php`
  - `wordpress/theme/usegeo/assets/css/homepage.css`
- 修改 lead / 后台 / 资源加载：
  - `wordpress/theme/usegeo/inc/forms.php`
  - `wordpress/theme/usegeo/inc/admin.php`
  - `wordpress/theme/usegeo/inc/assets.php`
  - `wordpress/theme/usegeo/functions.php`

实现范围：

- REST API：`POST /wp-json/usegeo/v1/scan`
- SSRF 防护与手动重定向校验
- HTML 解析与 5 维度规则评分
- LLM 解释层与 fallback
- 前端 evidence 区、错误状态、hidden scan snapshot
- lead snapshot 保存与邮件摘要
- 后台 lead 列表 score / weakest dimension
- feature flag 回退到旧模拟逻辑

## 文档更新

本 CR 已更新 / 生成以下治理文档：

- `request.md`：需求背景、业务目标、范围、非功能需求、约束、验收标准
- `acceptance-spec.md`：Data / Interface / Behavior / NFR / 依赖与风险
- `design/metadata.yml`：`ui_type: B端`
- `design/design-decisions.md`：Q1-Q5 与 D1-D7 决策
- `design/hi-fi-spec.md`：UI/后端/前端/后台/邮件设计规格
- `context-summary.md`：Spec + Design 摘要与加载上下文
- `traceability.yml`：13 个实现触点映射
- `test-plan.md`：TC-A 到 TC-H 测试计划
- `review-report.md`：逐场景审查与代码质量审查
- `quality-report.md`：P0 质量检查与真实验证结果
- `verification-manifest.yml`：静态验证命令
- `evidence/changed-files.json`：当前 diff 文件清单
- `evidence/static_verify.py`：当前环境可执行的静态契约验证脚本
- `evidence/baseline-before.json` / `baseline-after.json` / `baseline-comparison.json`

## 知识沉淀

本次无新规则。

沉淀的项目知识：

1. UseGEO Real Scanner v1 的后端应拆成 API / fetch / parse / score / llm / ratelimit 六个模块，避免把逻辑塞回 `functions.php` 或 `forms.php`。
2. 外网抓取必须手动处理 redirect，每一跳都重新做 SSRF 校验，不能依赖 HTTP 客户端自动重定向。
3. LLM prompt 必须只包含目标 URL 和本次 scan 提取的页面信号，不携带 lead 邮箱、表单字段、IP、cookie、session。
4. feature flag 关闭时前端 demo mode 不写 `scan_*` hidden fields，避免把模拟分数保存为真实 snapshot。
5. 旧 lead 不回填 0 分，后台缺失 score 时显示 `—`。

这些内容已体现在：

- `acceptance-spec.md` 的不变式
- `design/design-decisions.md` 的 Q1-Q5 / D1-D7
- `review-report.md` 的安全审查

## 可追溯性

- `traceability.yml` 已覆盖当前 13 个非 DeliverHQ 变更文件。
- `evidence/changed-files.json` 已记录当前实际 diff。
- `reviewgate.py` 已验证 traceability 与 git diff 一致。
- `qualitygate.py` 已运行真实验证命令并通过。

## 后续动作

1. 在真实 WordPress/PHP 环境补跑 PHP 语法检查（当前 Hermes bash 无 PHP CLI）。
2. 在本地站点手工 QA：合法 URL、SSRF、LLM fallback、lead snapshot、邮件、后台列。
3. 如 QA 通过，再由 Kevin 决定是否合并 `feature/CR-001` 到主分支。
4. 上线后 30 天内保留旧模拟逻辑，确认稳定后再开后续清理 CR。
