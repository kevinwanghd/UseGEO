# Test Plan: Sponsor / Ad Inventory Foundation

## 测试用例

### TC-A 后台保存 sponsor
- 打开 UseGEO Sponsors 设置页。
- 配置 homepage sponsor active=true/title/url。
- 保存后刷新仍存在。

### TC-B active sponsor 显示
- homepage sponsor active=true。
- 首页应显示 Sponsored 卡片。

### TC-C inactive sponsor 不显示
- placement active=false。
- 对应前台不输出 sponsor card。

### TC-D 外链安全
- 检查 sponsor link 有 target=_blank 且 rel 包含 noopener sponsored。

### TC-E 三个 placement
- 首页、scanner result、article sidebar 均可配置独立 sponsor。

## 自动验证

- `python DeliverHQ/change-requests/CR-005/evidence/static_verify.py`
- `node --check wordpress/theme/usegeo/assets/js/scanner.js`

## 人工 QA

- 生产配置一条测试 sponsor。
- 前台确认显示/隐藏。
