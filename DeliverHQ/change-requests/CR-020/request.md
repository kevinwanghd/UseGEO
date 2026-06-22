# Request: Lead Ops Enhancements

## CR-ID
CR-020

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已完成 partner lead 捕获、CSV 导出、sponsor reporting。下一步需要增强后台 lead 运营：人工覆盖 lead_quality、批量更新 partner_status、CSV 按日期过滤、partner notes 保留历史而非覆盖。

## 业务目标
让 Kevin/运营可以更安全地处理 partner lead：自动评分保留、人工判断可覆盖；列表可批量推进状态；导出可按日期收敛；备注以 append log 保存，避免旧备注被覆盖丢失。

## 功能描述
- lead_quality 拆分自动值和人工 override。
- lead 详情页可设置 override；override 为空时使用自动值。
- partner_status 支持后台 bulk action。
- partner lead CSV export 支持 `start_date` / `end_date`。
- partner notes 改为 append log，不覆盖旧备注。

## 非功能需求
- 不修改公开 lead 表单字段。
- 不导出未 consent lead。
- 不新增数据库表，使用 post meta。
- 所有后台写操作必须保留权限/nonce 或 WordPress bulk action 校验。

## 约束条件
- 生产上传/缓存清理仍需 Kevin 确认。
- 本 CR 不做完整 CRM，只做最小运营增强。
- 所有 DeliverHQ 产物必须在 `DeliverHQ/` 内。

## 优先级
P1

## 验收标准（高层）
- lead_quality_auto 与 lead_quality_override 分开保存。
- 人工 override 生效时 lead_quality 使用 override；清空 override 回到自动值。
- partner_status bulk action 可更新 selected leads。
- CSV export 支持 start_date / end_date 且仍只导出 consent=yes + hot/warm lead。
- Partner notes 使用 append log，不覆盖旧备注。
- 本地 verifier PASS；生产上传后公开代码/行为验证 PASS。
