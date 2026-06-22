# Request: Partner & Sponsor Prospecting

## CR-ID
CR-017

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已完成内容集群、分发系统和 sponsor slot 基础能力。下一步需要建立 partner/sponsor 商业验证资产：prospect list、sponsor offer、lead referral offer 和首批 outreach queue。

## 业务目标
建立 50 个 partner/sponsor prospect，至少 20 个高相关；设计 beta sponsor offer；设计 lead referral offer；准备 20 个首批 outreach queue，但不发送，等待 Kevin 最终确认。

## 功能描述
新增：
- `partner-prospect-list.json/csv`
- `first-20-outreach-queue.json`
- `sponsor-offer.md`
- `lead-referral-offer.md`
- `outreach-templates.md`
- `verify_partner_sponsor.py`

## 非功能需求
- prospect list 至少 50 个目标。
- 至少 20 个 fit=high。
- 覆盖 SEO agencies、content agencies、GEO consultants、AI visibility tools。
- sponsor offer 包含 homepage / scanner result / article sidebar，且是 beta offer，不承诺流量。
- lead referral offer 明确定义 consent=yes、hot/warm、business email、scanner snapshot。
- 不发送任何邮件/DM，不抓取私人邮箱，不伪造 contact。

## 约束条件
- 外部发送需要 Kevin 最终确认；本 CR 只准备 queue。
- contact 未核实时写 TBD，不编造联系人。
- 不使用 WordPress Application Password。

## 优先级
P1

## 验收标准（高层）
- 50 个 prospect，至少 20 high fit。
- 4 类 category 均有覆盖。
- sponsor offer 有价格草案、展示位置、受众说明、beta/no traffic guarantee 说明。
- lead referral offer 有报价草案、转介规则、合规说明。
- first-20 queue 存在且 send_status=ready_for_kevin_review_not_sent。
