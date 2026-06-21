# Test Plan: Content SEO Cluster Foundation

## 测试用例

### TC-A 首页显示 SEO cluster
- 打开首页。
- 预期：出现 GEO learning paths 区块。

### TC-B 三个入口链接
- 检查 card links。
- 预期：`/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/`。

### TC-C Scanner CTA
- 检查区块底部 CTA。
- 预期：链接到 `/#scanner`。

### TC-D 移动端布局
- 窄屏下 cards 单列。

## 自动验证

- `python DeliverHQ/change-requests/CR-006/evidence/static_verify.py`
- `node --check wordpress/theme/usegeo/assets/js/scanner.js`
