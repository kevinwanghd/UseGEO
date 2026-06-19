# 老项目逆向扫描报告

> 由 scan_legacy.py 生成（客观数据）。推断层与裁决层待 AI/人工补充。

## 执行信息
- 项目：usegeo
- 扫描根目录：D:\Code\UseGEO\wordpress\theme\usegeo
- 主技术栈：php
- 语言分布：node=4, php=26
- 扫描时间：2026-06-19 20:22:25

## 风险概览

| 指标 | 数量 | 占比 |
|---|---|---|
| 候选条目总数 | 26 | 100% |
| **需人工确认** (review_required) | 26 | 100% |
| 敏感域模块 | 1 | 4% |
| 无测试覆盖 | 26 | 100% |

## 需人工确认的高风险模块

| ID | 模块 | 风险原因 | 测试 | 复杂度 |
|---|---|---|---|---|
| RC-001 | 404 | 无测试覆盖 | none | low |
| RC-002 | footer | 无测试覆盖 | none | low |
| RC-003 | front-page | 无测试覆盖 | none | low |
| RC-004 | functions | 无测试覆盖 | none | low |
| RC-005 | header | 无测试覆盖 | none | low |
| RC-006 | index | 无测试覆盖 | none | low |
| RC-007 | page | 无测试覆盖 | none | low |
| RC-008 | search | 无测试覆盖 | none | low |
| RC-009 | single | 无测试覆盖 | none | medium |
| RC-010 | admin | 无测试覆盖；高复杂度 | none | high |
| RC-011 | assets | 无测试覆盖 | none | medium |
| RC-012 | forms | 无测试覆盖；data 敏感域 | none | medium |
| RC-013 | post-types | 无测试覆盖 | none | medium |
| RC-014 | setup | 无测试覆盖 | none | low |
| RC-015 | case-preview | 无测试覆盖 | none | low |
| RC-016 | community-intro | 无测试覆盖 | none | low |
| RC-017 | content-hub | 无测试覆盖 | none | low |
| RC-018 | methodology | 无测试覆盖 | none | low |
| RC-019 | sample-report | 无测试覆盖 | none | low |
| RC-020 | services | 无测试覆盖 | none | low |
| RC-021 | sidebar-cta | 无测试覆盖 | none | low |
| RC-022 | hero | 无测试覆盖 | none | low |
| RC-023 | learning-path | 无测试覆盖 | none | low |
| RC-024 | roi | 无测试覆盖 | none | low |
| RC-025 | scanner | 无测试覆盖 | none | medium |
| RC-026 | value-proposition | 无测试覆盖 | none | low |

## 下一步

1. AI 为每个候选填写推断层（inferred_behavior / assumptions / evidence）
2. 人工逐条裁决 review_required 条目（confirm/modify/reject），回答 is_real_requirement
3. 运行 ReverseSpecGate 确认无 unconfirmed 高风险条目
4. 运行 reverse_to_spec.py 转化为 acceptance-spec.md + traceability.yml

**报告由客观数据驱动；review_required 不可由 AI 主观降级。**