# Request: Expand Money Pages

## CR-ID
CR-010

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已发布三个 SEO cluster landing pages：`/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/`。当前页面偏薄，只能作为入口占位，难以支撑 SEO/GEO 流量、AI 摘取、Scanner 转化和 partner referral / lead resale 商业闭环。

## 业务目标
把三张 money pages 扩展为可排名、可被 AI 引用、可承接商业转化的英文长页：
- 承接 GEO / AI visibility / GEO agency 等商业搜索意图；
- 通过清晰章节、FAQ、内链和 CTA 引导访问者运行 Scanner；
- 保持 UseGEO 的真实定位：学习内容 + Scanner + partner referral / sponsor / affiliate / lead resale，不承诺自营完整服务。

## 功能描述
扩写并发布以下三个 WordPress 页面内容：
1. `/geo-checklist/`：GEO checklist / AI visibility checklist；
2. `/ai-visibility-tools/`：AI visibility tools / GEO tools buying guide；
3. `/best-geo-agencies/`：best GEO agencies / GEO agency evaluation guide。

每页需要包含：搜索意图匹配的 H1/intro、主体章节、至少 5 个 FAQ、至少 2 个 Scanner CTA、三页互链、底部 partner/expert follow-up CTA，以及避免虚假承诺的风险表述。

## 非功能需求
- 性能要求：内容发布不引入新主题代码、不新增前端脚本，公开页面 HTTP 200；单页 HTML 响应体可被 `curl` 获取。
- 安全要求：不提交 WordPress Application Password、LLM key 或任何生产 secret；不创建真实生产 lead；发布动作仅更新目标三页内容。
- 可用性要求：三个公开 URL 保持原 slug，不创建 `-2` 重复页面；内容中所有内部链接使用公开路径。

## 约束条件
- 不新增/修改项目根 `docs/`；DeliverHQ 产物只放 `DeliverHQ/`。
- 不承诺 AI ranking，不宣称精准检测 ChatGPT/Claude/Gemini 推荐排名。
- 不承诺 UseGEO 自营完整 GEO 咨询、审计或 Fix Sprint 服务。
- 生产发布如需 WordPress Application Password，必须由 Kevin 自行配置/撤销；不在聊天中索要或泄漏 secret。

## 优先级
P0

## 验收标准（高层）
- 三个页面各有 2500–3500 英文词（按文本提取近似统计）。
- 每页至少 5 个 FAQ，FAQ 回答适合搜索和 AI 摘取。
- 每页至少 2 个 Scanner CTA。
- 三页互相内链。
- 页面内容明确 UseGEO 是学习内容、Scanner 和 partner referral 路径，不是自营完整服务承诺。
- 生产 URL `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/` 均返回 200 并包含新内容锚点。

## 附件
- `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md`
- `DeliverHQ/docs/HANDOFF-2026-06-21-usegeo-growth.md`
