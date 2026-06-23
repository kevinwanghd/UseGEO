# Request: Commercial Design Upgrade

## CR-ID
CR-024

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
CR-023 已上线最小商业转化层，但页面仍偏“说明页”，和 ClayHog / Scrunch / Veza / My Web Audit 这类竞品相比仍显粗糙。需要把商业页升级为更像能卖钱的产品页：hero 有 audit input / report preview，sample report 更像真实交付物，partners 更像 partner/sponsor beta offer。

## 业务目标
提高商业可信度和转化意图，让访问者能在 10 秒内理解：UseGEO 可以免费扫描、给出样例报告、在明确 consent 后匹配工具/专家；让潜在 partner/sponsor 看到可购买的 lead/inventory 形态。

## 功能描述
- 升级 `/ai-visibility-audit/` hero，加入 URL input mock + report preview panel。
- 升级 `/sample-report/`，做成更真实的 report deliverable：score、dimensions、evidence、top gaps、handoff。
- 升级 `/partners/`，加入 beta package cards、inventory cards、partner lead object。
- 升级首页 commercial loop 为 Scan → Report → Match 更强叙事模块。
- 不接支付、不发外部消息、不自动转发 lead。

## 非功能需求
- 不改变 URL / 表单字段 / scanner API。
- 不承诺 AI ranking。
- 不伪造客户 logo / 收入增长 / case study。
- 保持静态页面，可生产验证。

## 优先级
P0

## 验收标准（高层）
- `/ai-visibility-audit/` 包含 URL input、audit preview、score card、CTA。
- `/sample-report/` 包含 report mockup、5 dimensions、evidence table、handoff section。
- `/partners/` 包含 beta packages、lead object、sponsor placements、apply CTA。
- 首页包含 upgraded Scan → Report → Match commercial loop。
- 本地 verifier PASS；生产上传后 production verifier PASS。
