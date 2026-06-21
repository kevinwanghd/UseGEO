# Writeback Report: Sponsor / Ad Inventory Foundation

## 归档日期

2026-06-21

## 代码变更

- `inc/admin.php`：新增 UseGEO Sponsors 设置页、sponsor option、render helper。
- `front-page.php`：新增 homepage sponsor slot。
- `template-parts/scanner/scanner.php`：新增 scanner_result sponsor slot。
- `single.php`：新增 article_sidebar sponsor slot。
- `assets/css/homepage.css`：新增 sponsor card 样式。

## 文档更新

- PRD 新增 `PRD-SPONSOR-INVENTORY`。
- CR-005 文档补齐。

## 知识沉淀

本次无新规则。

## 可追溯性

- traceability.yml 覆盖 5 个实现文件。
- ReviewGate / QualityGate PASS。

## 后续动作

- 合并部署。
- 生产配置测试 sponsor 并验证前台展示。
