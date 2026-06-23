# Acceptance Spec: Partner Lead Export / Pipeline

## CR-ID
CR-004

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-PARTNER-LEADS
  prd_hash: 7891c116cc71
```

---

## 1. Data Spec

| 实体 | 说明 | 字段 |
|---|---|---|
| PartnerPipeline | lead 的 partner 运营状态 | partner_status / partner_notes |
| PartnerLeadExport | CSV 导出 | consent=yes hot/warm lead |

字段约束：

| 字段 | 类型 | 约束 |
|---|---|---|
| partner_status | enum | new / reviewing / contacted / sold / rejected，默认 new |
| partner_notes | text | optional，后台可编辑 |

不变式：

1. partner_consent=no 的 lead 不得导出。
2. CSV 不包含 scan_findings_summary / scan_llm_summary 全文。
3. 导出必须具备 manage_options 权限和 nonce。

---

## 2. Interface Spec

### Admin filters

在 `edit.php?post_type=usegeo_lead` 增加筛选：

- Partner Consent
- Lead Quality
- Partner Status

### Lead meta box

新增可编辑字段：

- partner_status select
- partner_notes textarea

### CSV Export

新增 admin-post action：

```text
admin-post.php?action=usegeo_export_partner_leads&_wpnonce=...
```

CSV 字段：

```text
lead_id,work_email,company_type,company_size,website_input,geo_score,weakest_dimension,partner_consent,lead_quality,budget_range,region,help_intent,support_level,timeline,partner_status,submitted_at
```

---

## 3. Behavior Spec

### 场景 A：默认 partner_status

- Given：新 lead 创建。
- Then：partner_status 默认为 new。

### 场景 B：后台编辑状态

- Given：管理员打开 lead 详情页。
- When：修改 partner_status 和 partner_notes 并保存。
- Then：meta 保存成功。

### 场景 C：筛选 lead

- Given：后台 lead 列表。
- When：选择 partner_consent=yes / lead_quality=hot / partner_status=new。
- Then：列表只显示匹配 lead。

### 场景 D：CSV 导出

- Given：管理员点击导出 partner leads。
- Then：CSV 只包含 partner_consent=yes 且 lead_quality in hot/warm 的 lead。

### 场景 E：权限/nonce

- Given：未登录或无 nonce 请求导出 URL。
- Then：拒绝访问。

### 绝不能发生

1. 未授权 lead 不得导出。
2. CSV 不得包含 findings/LLM summary 全文。
3. 非管理员不得导出。

---

## 4. 非功能验收

- 导出最多一次性处理 500 条 lead。
- CSV Content-Type 为 `text/csv; charset=UTF-8`。
- 不引入外部服务。

---

## 5. 依赖与风险

- 依赖 CR-003 字段：partner_consent / lead_quality / budget_range / region。
- 风险：误导出未授权 lead；通过强制 consent filter 收敛。
