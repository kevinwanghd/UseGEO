# Acceptance Spec: Partner & Sponsor Prospecting

## CR-ID
CR-017

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-SPONSOR-INVENTORY
  prd_hash: 22b739cd2479
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|---|---|---|
| PartnerProspect | partner/sponsor 目标 | company, website, contact, category, fit, outreach_status |
| SponsorOffer | sponsor beta offer | placement, price_draft, audience, disclaimers |
| LeadReferralOffer | lead referral offer | lead_definition, price_draft, referral_rules, compliance |
| OutreachQueueItem | 首批待审 outreach | company, category, template, send_status |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| PartnerProspect.company | string | 必填，50 个唯一公司 | 无 | 不重复 |
| PartnerProspect.category | enum | SEO agency / content strategy agency / GEO consultant / AI visibility tool | 无 | 4 类覆盖 |
| PartnerProspect.fit | enum | high / medium / low | medium | high >=20 |
| OutreachQueueItem.send_status | enum | ready_for_kevin_review_not_sent | 无 | 本 CR 不发送 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| PartnerProspect | drafted | verified | verifier 通过 | verified |
| OutreachQueueItem | queued | sent | Kevin 后续明确确认发送 | queued_not_sent |

### Data Invariants（数据不变式）
- 不发送任何邮件/DM。
- contact 不明确时必须写 TBD，不伪造。
- sponsor offer 必须标注 beta，不承诺流量。
- lead referral 必须要求 consent=yes。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Local Prospect Verifier | CLI | `verify_partner_sponsor.py --mode local` | 验证 prospect/offer/outreach assets |

### Input Schema
```json
{"prospects":"outputs/partner-prospect-list.json","sponsor_offer":"outputs/sponsor-offer.md","lead_referral_offer":"outputs/lead-referral-offer.md","outreach_queue":"outputs/first-20-outreach-queue.json"}
```

### Output Schema
```json
{"passed":true,"checks":[{"name":"prospect_count","passed":true}]}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| insufficient_prospects | N/A | prospect <50 | 补充列表 |
| insufficient_high_fit | N/A | high fit <20 | 补充高相关 |
| external_send_detected | N/A | 出现 sent 状态 | 回滚为 queued |

### Idempotency & Side Effects
- **幂等性**：verifier 只写 evidence JSON。
- **副作用**：不发送邮件/DM，不调用外部 API。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Local verifier | 本地文件读写 | 仓库权限 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: Prospect list 可维护
- **Given** prospect list 已创建
- **When** verifier 运行
- **Then** 至少 50 个目标，字段完整，company 唯一
- **Measurable Success** prospect_count >=50, duplicate_count=0

### 场景 2: 高相关目标充足
- **Given** prospects 有 fit 字段
- **When** verifier 运行
- **Then** 至少 20 个 high fit，且 4 类 category 都覆盖
- **Measurable Success** high_fit_count >=20, category_missing_count=0

### 场景 3: Sponsor / lead offer 完整
- **Given** sponsor/lead offer docs 已创建
- **When** verifier 运行
- **Then** sponsor offer 包含 3 个 placement、价格草案、受众说明、beta/no guarantee；lead offer 包含 consent/hot/warm/business email/scanner snapshot/报价/合规
- **Measurable Success** offer_missing_count=0

### 场景 4: 首批 outreach 仅排队不发送
- **Given** first-20 queue 已创建
- **When** verifier 运行
- **Then** 20 个 queue item 均 send_status=ready_for_kevin_review_not_sent
- **Measurable Success** sent_count=0, queue_count=20

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 数据量 | prospects | >=50 |
| 数据质量 | high fit | >=20 |
| 类别覆盖 | category | 4/4 |
| 外部副作用 | sent | 0 |
| 合规 | consent | lead offer 必须要求 consent=yes |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| Sponsor slots | 内部 | 已有 CR-005 基础能力 | Hermes |
| Partner lead consent/pipeline | 内部 | 已有 CR-003/CR-004 基础能力 | Hermes |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | CR-017 范围包含 prospect list、sponsor offer、lead referral offer | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F2 | 发送 outreach 需要 Kevin 最终确认 | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | contact 可先用 TBD，不编造联系人 | P1 | verifier 接受 TBD | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否发送首批 20 个 outreach | P0 | Kevin | 2026-06-22 | resolved：本 CR 不发送，只排队 |

---

## 可测试性
- [x] prospect 数量可自动检查
- [x] offer 完整性可自动检查
- [x] send_status 可自动检查

**SpecGate 状态**：READY
