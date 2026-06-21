# Test Plan: Partner Lead Export / Pipeline

## 测试用例

### TC-A 默认 partner_status
- 创建新 lead。
- 预期：partner_status 默认为 new。

### TC-B 后台编辑状态
- 管理员打开 lead 详情页。
- 修改 partner_status=contacted，partner_notes=测试备注。
- 保存。
- 预期：meta 保存成功，刷新后仍显示。

### TC-C 后台筛选
- 在 usegeo_lead 列表选择 partner_consent=yes / lead_quality=hot / partner_status=new。
- 预期：列表只显示匹配 lead。

### TC-D CSV 导出
- 管理员点击 Export Partner Leads CSV。
- 预期：CSV 只包含 consent=yes 且 lead_quality=hot/warm 的 lead。
- 预期：CSV 字段包含 work_email、geo_score、weakest_dimension、partner_status 等。
- 预期：CSV 不包含 scan_findings_summary / scan_llm_summary 全文。

### TC-E 权限/nonce
- 未登录或缺 nonce 访问导出 action。
- 预期：拒绝访问。

## 自动验证

- `node --check wordpress/theme/usegeo/assets/js/scanner.js`
- `python DeliverHQ/change-requests/CR-004/evidence/static_verify.py`

## 人工 QA

- 后台保存 partner_status / partner_notes。
- 导出 CSV 并检查只包含授权 hot/warm lead。
