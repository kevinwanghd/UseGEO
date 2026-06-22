# Acceptance Spec: Sponsor Reporting & Inventory Tracking

## CR-ID
CR-019

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-SPONSOR-INVENTORY
  prd_hash: 99fc8a94c518
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|---|---|---|
| SponsorSlot | 后台配置的 sponsor slot | placement, active, title, description, cta_label, url, start_date, end_date |
| SponsorTrackingEvent | 前端 dataLayer event | event, placement, sponsor_url |
| SponsorRenderDecision | slot 展示判断结果 | active, has_title, has_url, start_date, end_date, today, visible |
| VerificationResult | 本地/生产验证证据 | mode, passed, checks |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| SponsorSlot.placement | enum | homepage / scanner_result / article_sidebar | 无 | 3 个固定位置 |
| SponsorSlot.active | enum | yes / no | no | no 不展示 |
| SponsorSlot.start_date | date string | `YYYY-MM-DD` 或空 | 空 | 空表示立即可展示 |
| SponsorSlot.end_date | date string | `YYYY-MM-DD` 或空 | 空 | 空表示长期展示 |
| SponsorTrackingEvent.event | enum | sponsor_click / sponsor_impression | 无 | GTM 可读取 |
| SponsorTrackingEvent.placement | enum | homepage / scanner_result / article_sidebar | 无 | impression 只记录该字段 |
| SponsorTrackingEvent.sponsor_url | url | click event 必填，impression 禁止 | 无 | click 用于 sponsor reporting |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| SponsorSlot | inactive | inactive→active→expired | 管理员保存 active/date；end_date 早于 today | inactive/active/expired |
| SponsorTrackingEvent | pending | pending→pushed | 用户点击或 slot 首次进入 viewport | pushed |

### Data Invariants（数据不变式）
- 所有 sponsor 外链必须包含 `rel="noopener sponsored nofollow"`。
- `sponsor_click` 必须包含 placement 和 sponsor_url。
- `sponsor_impression` 只能记录 placement，不记录 sponsor_url 或用户身份。
- end_date 早于当前日期时 sponsor slot 不展示。
- start_date / end_date 为空时不阻断长期展示。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Sponsor Admin Form | POST | Appearance → UseGEO Sponsors | 保存 active/title/description/CTA/url/start_date/end_date |
| Frontend tracking helper | JS | `window.usegeoTrackEvent` | push dataLayer event |
| Local verifier | CLI | `verify_sponsor_reporting.py --mode local` | 验证代码、事件、日期、rel 约束 |
| Production verifier | CLI | `verify_sponsor_reporting.py --mode production` | 验证公开 JS/HTML 已部署 |

### Input Schema
```json
{
  "mode": "local | production",
  "placements": ["homepage", "scanner_result", "article_sidebar"]
}
```

### Output Schema
```json
{
  "mode": "local",
  "passed": true,
  "checks": [{"name": "sponsor_click_event", "passed": true}]
}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| missing_click_event | N/A | tracking.js 缺少 sponsor_click | 修复 JS |
| missing_impression_event | N/A | tracking.js 缺少 sponsor_impression | 修复 JS |
| unsafe_rel | N/A | sponsor link rel 不含 nofollow/sponsored | 修复 PHP |
| date_window_missing | N/A | 后台或 render 缺少 start/end date | 修复 admin/render |

### Idempotency & Side Effects
- **幂等性**：verifier 可重复运行，只覆盖 CR-019 evidence JSON。
- **副作用**：前端事件只 push 到 dataLayer；无网络请求、无发送外部消息。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Sponsor Admin Form | manage_options | WordPress 后台权限 |
| Local verifier | 本地文件读写 | 仓库权限 |
| Production verifier | 公开 URL GET | 无认证 |

---

## 3. Behavior Spec（行为规格）

### 场景 1: sponsor click event 可被 GTM 读取
- **Given** sponsor card link 被点击
- **When** `tracking.js` click handler 捕获 `.usegeo-sponsor-card a`
- **Then** dataLayer push `sponsor_click`，包含 placement 和 sponsor_url
- **Measurable Success** local verifier 检测到 `sponsor_click`、`placement`、`sponsor_url`

### 场景 2: sponsor impression 只记录 placement
- **Given** sponsor card 进入 viewport
- **When** IntersectionObserver callback 第一次触发
- **Then** dataLayer push `sponsor_impression`，只包含 placement，不包含 sponsor_url
- **Measurable Success** local verifier 确认 impression event block 不读取 sponsor_url

### 场景 3: 后台日期窗口控制展示
- **Given** sponsor slot active=yes 且配置 start_date/end_date
- **When** 当前日期早于 start_date 或晚于 end_date
- **Then** sponsor slot 不展示
- **Measurable Success** local verifier 检测到 `usegeo_is_sponsor_slot_in_active_window` 并覆盖 start/end date 判断

### 场景 4: 日期为空时长期展示
- **Given** sponsor slot active=yes 且 start_date/end_date 为空
- **When** render slot
- **Then** 日期窗口不阻断展示
- **Measurable Success** 默认 slot 包含空 start_date/end_date，render 只在日期不满足时 return

### 场景 5: sponsor 外链安全
- **Given** sponsor card 渲染外链
- **When** 用户点击外链
- **Then** 链接使用 target blank 且 rel 包含 noopener sponsored nofollow
- **Measurable Success** local verifier 检测 rel 字符串完整

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 性能 | Observer 数量 | 只初始化 sponsor card observer；不轮询 |
| 安全 | 外链 rel | 100% sponsor links 包含 noopener sponsored nofollow |
| 隐私 | impression payload | 不包含 URL、email、user id |
| 可维护性 | 存储 | 继续复用 `usegeo_sponsor_slots` option，不新增表 |
| 可测试性 | verifier | local mode 必须 PASS |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| Sponsor Inventory v1 | 内部 | CR-005 已完成 | Hermes |
| tracking.js / dataLayer helper | 内部 | 已存在 | Hermes |
| GTM 容器配置 | 外部 | 不阻断本 CR | Kevin |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | CR-019 范围包含 click event、impression、start/end date、rel sponsored | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F2 | Sponsor inventory 已有 homepage/scanner_result/article_sidebar 三个 placement | CR-005 代码 | Hermes | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | impression event 只需前端 dataLayer push，不需要后端存储 | P1 | 验收范围“GTM 可看到事件” | 2026-06-22 | verified |
| A2 | start/end date 使用站点当前日期 `Y-m-d` 即可，不需要时区 UI | P2 | WordPress current_time 静态实现 | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否立即上传生产主题包并清缓存 | P0 | Kevin | 2026-06-22 | resolved：本 CR 先本地完成并打包，生产上传需 Kevin 确认 |

---

## 可测试性
- [x] click event 可静态验证
- [x] impression event 可静态验证
- [x] date window 可静态验证
- [x] rel nofollow/sponsored 可静态验证
- [x] production deploy 后可通过 public JS/HTML 验证

**SpecGate 状态**：READY
