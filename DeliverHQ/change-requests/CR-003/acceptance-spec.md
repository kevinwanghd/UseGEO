# Acceptance Spec: Partner Lead Monetization Foundation

## CR-ID
CR-003

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-PARTNER-LEADS
  prd_hash: 7891c116cc71
```

---

## 1. Data Spec（数据规格）

### Entities

| 实体名 | 说明 | 关键字段 |
|---|---|---|
| PartnerLeadConsent | 用户是否授权 UseGEO 转介信息 | partner_consent |
| LeadQualification | lead 可转介质量判断 | lead_quality / budget_range / region |
| UseGEOLead | 现有 lead CPT | work_email / scanner_query / scan_* / partner_* |

### Fields & Constraints

| 字段 | 类型 | 约束 | 说明 |
|---|---|---|---|
| partner_consent | enum | yes/no，默认 no | 是否允许分享给相关 GEO/SEO partner |
| budget_range | enum | empty / under_500 / 500_1500 / 1500_5000 / 5000_plus / not_sure | 预算区间 |
| region | enum | empty / north_america / europe / asia_pacific / other / global_remote | 地区/时区 |
| lead_quality | enum | hot / warm / cold | 内部运营分级，不展示给用户 |

### Data Invariants

1. `partner_consent=no` 时，`lead_quality` 必须为 `cold`。
2. 未勾选 consent 的 lead 不得被标记为可转介。
3. `lead_quality=hot` 必须满足 consent=yes 且 help_intent/support_level/timeline 均为高意向组合。
4. 旧 lead 缺少 partner 字段时，后台显示 `—` 或默认 cold，不做批量回填。

---

## 2. Interface Spec（接口规格）

### Frontend Form

在 `template-parts/scanner/scanner.php` 的 Full Diagnostic 表单中新增：

- `partner_consent` checkbox
- `budget_range` select
- `region` select

用户文案必须表达：只有勾选授权后，UseGEO 才可能把信息分享给相关 GEO/SEO partner。

### Backend Save

`usegeo_handle_lead_capture` 保存：

- `partner_consent`
- `budget_range`
- `region`
- `lead_quality`

### Admin Columns

`usegeo_lead_admin_columns` 新增：

- Partner Consent
- Lead Quality
- Budget
- Region

### Email

`usegeo_send_lead_notification` 增加：

- Partner Consent
- Lead Quality
- Budget
- Region

---

## 3. Behavior Spec（行为规格）

### 场景 A：未勾选 consent

- Given：用户提交 lead，未勾选 partner_consent。
- When：后端保存 lead。
- Then：`partner_consent=no`，`lead_quality=cold`，后台 Partner Consent 显示 no。

### 场景 B：高意向 consent lead

- Given：用户勾选 partner_consent，help_intent=prioritized_next_steps 或 open_to_specialist，support_level=small_project 或 ongoing_support，timeline=next_quarter 或 need_help_soon。
- When：后端保存 lead。
- Then：`lead_quality=hot`。

### 场景 C：普通 consent lead

- Given：用户勾选 partner_consent，但不满足 hot 规则。
- When：后端保存 lead。
- Then：`lead_quality=warm`，除非 help_intent=exploring_geo 且 support_level=not_hiring，此时可为 cold。

### 场景 D：后台展示

- Given：lead 已创建。
- When：管理员查看 usegeo_lead 列表或详情。
- Then：能看到 Partner Consent / Lead Quality / Budget / Region。

### 场景 E：邮件通知

- Given：lead 创建成功且邮件发送。
- When：管理员收到邮件。
- Then：邮件正文包含 Partner Consent / Lead Quality / Budget / Region。

### 绝不能发生

1. 绝不允许没有 consent 的 lead 被标记为 hot/warm 可转介。
2. 绝不允许前端文案暗示 UseGEO 自己承诺完整 GEO 服务交付。
3. 绝不允许为 partner lead 放宽 nonce / email / scanner_query 校验。

---

## 4. 非功能验收

- 不新增外部 partner webhook。
- 不自动发送用户信息给第三方。
- 不破坏 scan snapshot、GEO Score、Weakest Dimension、邮件通知现有能力。
- 所有新字段只在 lead CPT meta 中保存。

---

## 5. 依赖与风险

- 风险：partner consent 文案不清导致合规风险。
- 收敛：checkbox 文案必须明确“may share with relevant GEO/SEO partners”。
- 风险：lead_quality 规则过粗。
- 收敛：v1 只做最小规则，后续按真实 lead 质量迭代。
