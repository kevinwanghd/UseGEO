# Acceptance Spec: Lead Ops Enhancements

## CR-ID
CR-020

## 来源锚点（derived_from）
```yaml
derived_from:
  prd_section: PRD-PARTNER-LEADS
  prd_hash: 5a64ade53a77
```

## 1. Data Spec（数据规格）

### Entities（实体）
| 实体名 | 说明 | 关键字段 |
|---|---|---|
| LeadQualityState | lead quality 自动和人工状态 | lead_quality_auto, lead_quality_override, lead_quality |
| PartnerStatusBulkAction | 后台批量状态动作 | action, post_ids, partner_status |
| PartnerLeadExportFilter | CSV 日期过滤 | start_date, end_date, partner_consent, lead_quality |
| PartnerNotesLog | partner notes 追加历史 | timestamp, user, note |

### Fields & Constraints（字段与约束）
| 实体.字段 | 类型 | 限制条件 | 默认值 | 说明 |
|---|---|---|---|---|
| LeadQualityState.lead_quality_auto | enum | hot / warm / cold | cold | 自动计算值 |
| LeadQualityState.lead_quality_override | enum | 空 / hot / warm / cold | 空 | 人工覆盖值 |
| LeadQualityState.lead_quality | enum | hot / warm / cold | cold | 有 override 时等于 override，否则等于 auto |
| PartnerStatusBulkAction.partner_status | enum | reviewing / contacted / sold / rejected | 无 | bulk action 不提供 new，避免误回退 |
| PartnerLeadExportFilter.start_date | date string | YYYY-MM-DD 或空 | 空 | 导出起始日期 |
| PartnerLeadExportFilter.end_date | date string | YYYY-MM-DD 或空 | 空 | 导出结束日期 |
| PartnerNotesLog.note | string | 非空才 append | 无 | 不覆盖旧备注 |

### State Transitions（状态转换）
| 实体 | 初始状态 | 允许转换 | 触发条件 | 终态 |
|---|---|---|---|---|
| LeadQualityState | auto | auto→override→auto | 管理员设置或清空 override | auto/override |
| PartnerStatusBulkAction | selected | selected→updated | WP bulk action 提交 | updated |
| PartnerNotesLog | existing | existing→appended | 管理员提交新备注 | appended |

### Data Invariants（数据不变式）
- CSV 导出必须保留 `partner_consent=yes` 且 `lead_quality in hot,warm` 的约束。
- `lead_quality_auto` 不应被人工覆盖写坏。
- `partner_notes_log` 只能追加，不覆盖旧日志。
- Bulk action 仅作用于 `usegeo_lead` post type。

---

## 2. Interface Spec（接口规格）

### API Signature
| 接口 | 方法 | 路径 | 说明 |
|---|---|---|---|
| Lead meta box | POST | post.php lead edit | 保存 lead_quality_override、partner_status、partner note append |
| Lead bulk action | POST | edit.php bulk action | 批量更新 partner_status |
| Partner CSV export | GET | `admin-post.php?action=usegeo_export_partner_leads` | 支持 start_date/end_date |
| Local verifier | CLI | `verify_lead_ops.py --mode local` | 验证代码和约束 |
| Production verifier | CLI | `verify_lead_ops.py --mode production` | 验证公开主题文件已部署 |

### Input Schema
```json
{
  "lead_quality_override": "" ,
  "partner_status": "reviewing",
  "partner_note_append": "text",
  "start_date": "2026-06-01",
  "end_date": "2026-06-22"
}
```

### Output Schema
```json
{
  "mode": "local",
  "passed": true,
  "checks": [{"name": "lead_quality_override", "passed": true}]
}
```

### Error Codes
| 错误码 | HTTP状态 | 说明 | 客户端处理 |
|---|---|---|---|
| missing_override | N/A | 缺少 lead_quality override 支持 | 修复 admin.php |
| missing_bulk_action | N/A | 缺少 partner_status bulk action | 修复 admin.php |
| missing_date_filter | N/A | CSV 缺少 start/end date | 修复 export |
| notes_overwrite | N/A | 备注覆盖旧值 | 改为 append log |

### Idempotency & Side Effects
- **幂等性**：verifier 只写 evidence JSON。
- **副作用**：后台保存会更新 post meta；CSV export 只读；production verifier 只读公开文件。

### Permission Requirements
| 接口 | 需要权限 | 验证方式 |
|---|---|---|
| Lead meta save | edit_post | WordPress save_post 权限 |
| Bulk action | edit_posts | WordPress bulk action |
| CSV export | manage_options | current_user_can + nonce |

---

## 3. Behavior Spec（行为规格）

### 场景 1: lead_quality 自动值和人工 override 分开记录
- **Given** lead 创建时自动计算出 lead_quality_auto
- **When** 管理员设置 lead_quality_override
- **Then** lead_quality 使用 override，但 lead_quality_auto 保持原值
- **Measurable Success** verifier 检测到 `lead_quality_auto`、`lead_quality_override`、`usegeo_resolve_effective_lead_quality`

### 场景 2: 清空 override 回到自动值
- **Given** lead 已有人工作 override
- **When** 管理员选择空 override
- **Then** lead_quality 回到 lead_quality_auto
- **Measurable Success** save handler 中空 override 分支使用 auto fallback

### 场景 3: partner_status 支持批量操作
- **Given** 后台列表选中多条 usegeo_lead
- **When** 执行 bulk action `Mark partner status: contacted`
- **Then** selected leads 的 partner_status 更新为 contacted
- **Measurable Success** verifier 检测 bulk action filter 和 handler

### 场景 4: CSV export 支持日期过滤
- **Given** URL 带 start_date / end_date
- **When** 执行 partner lead CSV export
- **Then** WP_Query 增加 date_query，同时仍保留 consent=yes + hot/warm
- **Measurable Success** verifier 检测 date_query、start_date、end_date 和 consent/hot/warm 约束

### 场景 5: Partner notes append log 不覆盖旧备注
- **Given** lead 已有 partner_notes_log
- **When** 管理员提交新备注
- **Then** 新备注 append 到日志，旧日志保留
- **Measurable Success** verifier 检测 append helper 和没有直接用新备注覆盖旧日志

---

## 非功能验收
| 维度 | 指标 | 验收标准 |
|---|---|---|
| 安全 | CSV consent | 导出不包含 consent=no lead |
| 权限 | 后台保存 | current_user_can 检查保留 |
| 可维护性 | 数据存储 | 使用 post meta，不新增表 |
| 可测试性 | verifier | local mode 必须 PASS |

---

## 依赖项
| 依赖 | 类型 | 状态 | 责任人 |
|---|---|---|---|
| Partner Lead foundation | 内部 | CR-003/CR-004 已完成 | Hermes |
| Sponsor reporting | 内部 | CR-019 已完成 | Hermes |

---

## 模糊点与待确认项

### Facts（已确认事实）
| # | 事实 | 来源 | 确认人 | 日期 |
|---|---|---|---|---|
| F1 | CR-020 范围包含 lead_quality override、partner_status bulk、CSV date filter、notes append log | SEO-GEO-GROWTH-TASK-LIST.md | Kevin | 2026-06-22 |
| F2 | Partner Lead PRD 要求 consent lead 可导出且可跟踪 partner_status | PRD-PARTNER-LEADS | Kevin | 2026-06-22 |

### Assumptions（假设前提）
| # | 假设 | 风险级别 | 验证方式 | 验证期限 | 状态 |
|---|---|---|---|---|---|
| A1 | Bulk action 只需要更新 partner_status，不需要批量 notes | P2 | CR 范围 | 2026-06-22 | verified |
| A2 | CSV 日期过滤基于 post_date/submitted date 即可 | P2 | WP_Query date_query | 2026-06-22 | verified |

### Open Questions（待确认问题）
| # | 问题 | 阻断级别 | 负责人 | 截止日期 | 状态 |
|---|---|---|---|---|---|
| Q1 | 是否立即上传生产主题包并清缓存 | P0 | Kevin | 2026-06-22 | resolved：本 CR 先本地完成并打包，生产上传需 Kevin 确认 |

---

## 可测试性
- [x] lead_quality override 可静态验证
- [x] bulk action 可静态验证
- [x] CSV date filter 可静态验证
- [x] notes append log 可静态验证
- [x] production deploy 后可通过 public theme file 验证

**SpecGate 状态**：READY
