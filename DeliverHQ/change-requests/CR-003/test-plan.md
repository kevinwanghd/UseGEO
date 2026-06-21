# Test Plan: Partner Lead Monetization Foundation

## 测试用例

### TC-A 未勾选 consent
- 提交 lead，partner_consent 空。
- 预期：partner_consent=no，lead_quality=cold。

### TC-B 高意向 consent
- partner_consent=yes，help_intent=open_to_specialist，support_level=small_project，timeline=need_help_soon。
- 预期：lead_quality=hot。

### TC-C 普通 consent
- partner_consent=yes，但 just_researching/not_hiring。
- 预期：lead_quality=cold 或 warm（按规则）。

### TC-D 后台列
- 后台 usegeo_lead 列表显示 Partner Consent / Lead Quality / Budget / Region。

### TC-E 邮件
- 邮件正文包含 Partner Consent / Lead Quality / Budget / Region。

## 自动验证

- `node --check wordpress/theme/usegeo/assets/js/scanner.js`
- `python DeliverHQ/change-requests/CR-003/evidence/static_verify.py`

## 人工 QA

- 提交一条 consent=no lead。
- 提交一条 consent=yes hot lead。
- 后台确认字段和邮件。
