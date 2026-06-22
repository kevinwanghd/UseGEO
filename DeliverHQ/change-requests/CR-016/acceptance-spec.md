# Acceptance Spec: Distribution Launch System

## CR-ID
CR-016

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-CONTENT-LIBRARY
  prd_hash: 77f775574a7a
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|---|---|---|
| LinkedInDraft | LinkedIn 分发草稿 | article, type, draft |
| XDraft | X/Twitter 短帖 | article, draft |
| CommunityDraft | Reddit/Indie Hackers 草稿 | article, draft |
| PartnerProspect | partner/sponsor 目标 | company, website, category, contact, fit, outreach_status |
| OutreachTemplate | 冷启动模板 | channel, subject, body |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| LinkedInDraft.type | enum | pov/checklist/before_after | 无 | 每篇 3 类各 1 条 |
| XDraft.draft | string | 每篇至少 5 条 | 无 | 每条可独立成立 |
| CommunityDraft.draft | string | 不硬广，不直接 spam | 无 | 先讲方法/发现 |
| PartnerProspect.company | string | 至少 20 个目标 | 无 | contact 可 TBD |
| OutreachTemplate.body | string | 至少 2 套 | 无 | email / DM |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| DistributionAsset | drafted | verified | verifier 通过 | verified |

### Data Invariants（数据不变式）
- 不自动发布到外部平台。
- Community drafts 不直接 spam 链接。
- 不承诺 AI ranking 或精准推荐追踪。
- Partner contacts 不伪造，未知字段用 TBD。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Local Distribution Verifier | CLI | `verify_distribution.py --mode local` | 验证分发素材完整性 |

### Input Schema
```json
{"linkedin_drafts":"outputs/linkedin-drafts.json","x_drafts":"outputs/x-drafts.json","community_drafts":"outputs/community-drafts.json","partner_prospects":"outputs/partner-prospect-list.json"}
```

### Output Schema
```json
{"passed":true,"checks":[{"name":"linkedin_count","passed":true}]}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| missing_asset | N/A | 缺少输出文件 | 补齐后重跑 |
| insufficient_drafts | N/A | draft 数不足 | 补齐 draft |
| spam_risk | N/A | 社区帖硬广或直接 spam | 改写 |

### Idempotency & Side Effects
- **幂等性**：verifier 只写 evidence JSON。
- **副作用**：不发外部平台消息，不发邮件。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Local verifier | 本地文件读写 | 仓库权限 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: LinkedIn 拆分完整
- **Given** 已有 sample articles
- **When** verifier 运行
- **Then** 每篇至少 3 条 LinkedIn draft，覆盖 pov/checklist/before_after
- **Measurable Success** missing LinkedIn draft count = 0

### 场景 2: X/Twitter 短帖完整
- **Given** 已有 sample articles
- **When** verifier 运行
- **Then** 每篇至少 5 条 X draft，至少 1 条包含 scanner CTA
- **Measurable Success** missing X draft count = 0

### 场景 3: Community drafts 不硬广
- **Given** Reddit/Indie Hackers 草稿存在
- **When** verifier 扫描
- **Then** 草稿先讲方法/发现，不直接 spam 链接
- **Measurable Success** spam_risk count = 0

### 场景 4: Partner outreach 可执行
- **Given** partner template 和 prospect list 存在
- **When** verifier 运行
- **Then** prospect list 至少 20 个，模板至少 email + DM 两套，tracking fields 完整
- **Measurable Success** prospect_count >= 20, template_count >= 2

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 分发完整性 | LinkedIn | 每篇 >=3 |
| 分发完整性 | X | 每篇 >=5 |
| 社区安全 | spam | 0 spam risk |
| Partner ops | prospect | >=20 |
| 外部副作用 | publish/send | 0 |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| 已发布内容文章 | 内部 | 已就绪 | Hermes |
| CONTENT-TEMPLATE.md | 内部 | 已就绪 | Hermes |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | CR-016 范围是分发模板和冷启动素材，不自动发布 | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | sample drafts 基于 CR-015 use-case articles 先打样 | P2 | verifier 检查 draft 数 | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否直接发 LinkedIn/X/Reddit 或邮件 | P0 | Kevin | 2026-06-22 | resolved：不发，只生成草稿 |

---

## 可测试性
- [x] draft 数量可自动化检查
- [x] spam risk 可关键字扫描
- [x] 不产生外部副作用

**SpecGate 状态**：READY
