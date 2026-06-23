# Acceptance Spec: Commercial Conversion Layer

## CR-ID
CR-023

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
| CommercialRoute | theme-rendered public commercial route | slug, title, sections, cta |
| SampleReport | 示例诊断报告 | score, dimensions, evidence, recommended_next_steps |
| PartnerOffer | partner/sponsor 前台说明 | lead_definition, sponsor_inventory, beta_terms |
| HomepageCommercialModule | 首页商业闭环模块 | learn, scan, decide, cta |
| VerificationResult | 本地/生产验证证据 | mode, passed, checks |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| CommercialRoute.slug | enum | ai-visibility-audit / sample-report / partners | 无 | theme intercept render |
| SampleReport.score | integer | 示例值必须标注 sample | 72 | 不伪造真实客户 |
| PartnerOffer.lead_definition | array | consent=yes, hot/warm, business email, scanner snapshot | 无 | partner lead 定义 |
| PartnerOffer.sponsor_inventory | array | homepage / scanner_result / article_sidebar | 无 | sponsor 库存 |
| HomepageCommercialModule.cta | url | `/ai-visibility-audit/` 和 `/sample-report/` | 无 | 首页承接 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| CommercialRoute | missing | missing→rendered→verified | theme route intercept deployed | verified |
| HomepageCommercialModule | absent | absent→rendered→verified | front-page module added | verified |

### Data Invariants（数据不变式）
- 不承诺 guaranteed AI ranking。
- 不把 UseGEO 描述成自营完整 GEO 服务商。
- partner follow-up 必须基于 explicit consent。
- 不新增 payment 或自动转发 lead。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Commercial route renderer | GET | `/ai-visibility-audit/`, `/sample-report/`, `/partners/` | theme intercept 输出页面 |
| Homepage commercial module | PHP template part | `front-page.php` | scanner 后展示商业闭环 |
| Local verifier | CLI | `verify_commercial_conversion.py --mode local` | 静态验证 routes/templates/css |
| Production verifier | CLI | `verify_commercial_conversion.py --mode production` | 抓取生产 URL 验证 |

### Input Schema
```json
{"mode":"local | production","routes":["/ai-visibility-audit/","/sample-report/","/partners/"]}
```

### Output Schema
```json
{"mode":"local","passed":true,"checks":[{"name":"routes_registered","passed":true}]}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| route_missing | N/A | 商业 route 缺失 | 修复 route renderer |
| missing_consent_copy | N/A | 缺 consent 说明 | 修复页面 copy |
| missing_partner_offer | N/A | partner 页面缺 lead/sponsor 定义 | 修复页面 |
| ranking_claim | N/A | 出现 guaranteed ranking claim | 删除不当承诺 |

### Idempotency & Side Effects
- **幂等性**：verifier 可重复运行，只覆盖 evidence JSON。
- **副作用**：route renderer 只输出页面；不写数据库、不发消息。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Public routes | 无 | GET |
| Local verifier | 本地文件读写 | 仓库权限 |
| Production verifier | 公开 URL GET | 无认证 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: `/ai-visibility-audit/` 商业落地页可访问
- **Given** 用户想知道自己网站 AI visibility 缺口
- **When** 打开 `/ai-visibility-audit/`
- **Then** 页面说明 free scan、sample report、partner follow-up consent、CTA 到 scanner
- **Measurable Success** production status=200 且包含 key markers

### 场景 2: `/sample-report/` 展示可理解交付物
- **Given** 用户不确定提交后会得到什么
- **When** 打开 `/sample-report/`
- **Then** 页面展示 sample score、5 维度、evidence、top gaps、recommended next steps
- **Measurable Success** verifier 检测 sample report markers

### 场景 3: `/partners/` 解释 partner/sponsor 商业模式
- **Given** potential partner 或 sponsor 想理解合作方式
- **When** 打开 `/partners/`
- **Then** 页面展示 lead definition、3 个 sponsor placements、beta/no guarantee、apply CTA
- **Measurable Success** verifier 检测 lead definition + sponsor inventory markers

### 场景 4: 首页说明商业闭环
- **Given** 用户从首页进入
- **When** scanner 模块后继续阅读
- **Then** 能看到 Learn / Scan / Decide / Partner follow-up 的闭环说明
- **Measurable Success** production homepage 包含 commercial module markers

### 场景 5: 不产生外部副作用
- **Given** 本 CR 部署
- **When** 用户访问新页面
- **Then** 不发送外部消息、不自动转发 lead、不新增 payment
- **Measurable Success** verifier 静态检测无 external send markers

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 可用性 | Routes | 3 个新 route 生产 200 |
| 合规 | Consent | partner follow-up 文案明确 consent |
| 商业清晰度 | Offer | sample report + partner/sponsor page markers 存在 |
| 安全 | Side effects | 不发送外部消息，不写数据库 |
| 可测试性 | verifier | local mode 必须 PASS |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| Scanner | 内部 | 已有 | Hermes |
| Partner lead ops | 内部 | CR-020 已完成 | Hermes |
| Sponsor inventory | 内部 | CR-019 已完成 | Hermes |
| Production upload/cache purge | 外部人工 | 待 Kevin 确认 | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | UseGEO 商业模式是内容流量 + scanner + consent lead + partner/sponsor/affiliate/lead resale | PRD / Kevin memory | Kevin | 2026-06-22 |
| F2 | 竞品商业页普遍有 free audit、sample report、partner/agency offer、pricing or package anchor | web research | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | theme-rendered pages 比 WP REST 创建页面更适合当前快速验证 | P1 | local/production route verifier | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否立即上传生产主题包并清缓存 | P0 | Kevin | 2026-06-22 | resolved：本 CR 先本地完成并打包，生产上传需 Kevin 确认 |

---

## 可测试性
- [x] route registration 可静态验证
- [x] homepage module 可静态验证
- [x] commercial copy markers 可生产验证
- [x] consent/no fake ranking 可静态验证

**SpecGate 状态**：READY
