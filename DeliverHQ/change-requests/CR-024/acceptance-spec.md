# Acceptance Spec: Commercial Design Upgrade

## CR-ID
CR-024

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-PARTNER-LEADS
  prd_hash: 7891c116cc71
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|---|---|---|
| AuditHero | audit page hero conversion surface | url_input, primary_cta, report_preview |
| ReportMockup | sample report deliverable | score, dimensions, evidence, recommendations, partner_handoff |
| PartnerCommercialOffer | partner/sponsor offer | beta_packages, lead_object, inventory, apply_cta |
| HomepageCommercialLoop | homepage commercial path | scan, report, match |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| AuditHero.url_input | string | visible input, no backend submit | 空 | 视觉转化入口 |
| ReportMockup.score | integer | sample-labeled | 72 | 不伪造真实客户 |
| ReportMockup.dimensions | array | 5 dimensions | 无 | 与 scanner 维度一致 |
| PartnerCommercialOffer.beta_packages | array | 至少 2 个 | 无 | sponsor/lead referral |
| PartnerCommercialOffer.lead_object | array | consent, quality, email, scanner snapshot | 无 | lead definition |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| CommercialPage | explanatory | explanatory→productized→verified | HTML/CSS upgrade + verifier | verified |

### Data Invariants（数据不变式）
- 不添加支付和自动发送。
- 不承诺 guaranteed AI rankings。
- 不伪造客户 logo、真实收入增长或 case study。
- 所有 partner handoff 必须以 consent 为前提。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Commercial pages | GET | `/ai-visibility-audit/`, `/sample-report/`, `/partners/` | theme-rendered commercial pages |
| Homepage module | PHP template part | `commercial-loop.php` | homepage commercial path |
| Local verifier | CLI | `verify_commercial_design.py --mode local` | 静态验证商业设计升级 |
| Production verifier | CLI | `verify_commercial_design.py --mode production` | 抓取生产页面验证 |

### Input Schema
```json
{"mode":"local | production"}
```

### Output Schema
```json
{"passed":true,"checks":[{"name":"audit_input_present","passed":true}]}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| missing_audit_input | N/A | audit hero 缺 URL input | 修复 commercial page |
| weak_report_mockup | N/A | sample report 不像交付物 | 补 score/dimensions/evidence |
| missing_partner_offer | N/A | partners 页缺 package/lead object | 补 partner offer |
| bad_claim | N/A | 出现 ranking guarantee | 删除 claim |

### Idempotency & Side Effects
- **幂等性**：verifier 只覆盖 evidence JSON。
- **副作用**：页面静态渲染，不发外部消息。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Public pages | 无 | GET |
| Local verifier | 本地文件读写 | 仓库权限 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: audit landing 像工具页而不是说明页
- **Given** 用户打开 `/ai-visibility-audit/`
- **When** hero 渲染
- **Then** 能看到 URL input、report preview、score card 和 scan CTA
- **Measurable Success** verifier 检测 audit input + preview markers

### 场景 2: sample report 像可交付物
- **Given** 用户打开 `/sample-report/`
- **When** report mockup 渲染
- **Then** 能看到 sample score、5 dimensions、evidence table、top gaps、partner handoff
- **Measurable Success** verifier 检测 report markers

### 场景 3: partners 页像商业 offer
- **Given** partner/sponsor 打开 `/partners/`
- **When** 页面渲染
- **Then** 能看到 beta package cards、lead object、sponsor inventory 和 apply CTA
- **Measurable Success** verifier 检测 partner offer markers

### 场景 4: 首页商业闭环更强
- **Given** 用户在首页 scanner 后继续阅读
- **When** commercial loop 渲染
- **Then** 看见 Scan → Report → Match 路径和两个商业 CTA
- **Measurable Success** verifier 检测 upgraded homepage markers

### 场景 5: 仍无高风险副作用
- **Given** 本 CR 部署
- **When** 访问页面
- **Then** 不支付、不发邮件、不自动 partner forwarding
- **Measurable Success** verifier 检测无 external side-effect markers

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 商业清晰度 | markers | audit/report/partner/homepage markers 全 PASS |
| 合规 | claims | no guaranteed ranking |
| 安全 | side effects | no mail/post/remote calls |
| 可测试性 | verifier | local mode 必须 PASS |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| CR-023 Commercial Conversion Layer | 内部 | 已生产上线 | Hermes |
| Scanner | 内部 | 已有 | Hermes |
| Production upload/cache purge | 外部人工 | 待 Kevin 确认 | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | Kevin 认为当前商业页和竞品相比粗糙 | 用户反馈 | Kevin | 2026-06-22 |
| F2 | CR-023 已上线但仍是最小说明型页面 | production verifier | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | 先强化静态商业表达比接支付/表单更优先 | P1 | production route verifier | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否立即上传生产主题包并清缓存 | P0 | Kevin | 2026-06-22 | resolved：本 CR 先本地完成并打包，生产上传需 Kevin 确认 |

---

## 可测试性
- [x] audit input 可验证
- [x] sample report markers 可验证
- [x] partner package markers 可验证
- [x] homepage loop markers 可验证

**SpecGate 状态**：READY
