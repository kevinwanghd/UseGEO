# Request: Publish Tools & Commercial Cluster

## CR-ID
CR-014

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已完成 basics 和 tactical 内容集群。下一步需要发布 tools / commercial cluster，承接更高商业意图的读者：正在比较 AI visibility tools、GEO tools、ChatGPT visibility checker、GEO agency 和 GEO audit 的人。

## 业务目标
发布 5 篇商业意图文章，为 sponsor / affiliate / partner referral / lead resale 准备内容入口，同时保持编辑可信度，不做虚假 Top 10 或未标注赞助。

## 功能描述
按 CR-012 内容模板生成并发布 5 篇 WordPress posts：
1. Best AI Visibility Tools
2. GEO Tools: What to Look For
3. ChatGPT Visibility Checker: What It Can and Cannot Measure
4. How to Choose a GEO Agency
5. GEO Audit: What Should Be Included?

## 非功能需求
- 每篇 1200–1800 英文词。
- 每篇至少 4 FAQ、1 Scanner CTA、3 内链。
- 不虚构具体 Top 10，不偏向单一工具，不承诺精准 ChatGPT 推荐排名，不承诺 UseGEO 自营完整交付。
- 发布脚本只从 process env 读取 WP_USER/WP_APP_PASSWORD，不落盘。

## 约束条件
- 不修改 WordPress theme 代码。
- Sponsor / affiliate 后续可插入，但本 CR 不写未确认赞助。
- 优先按 slug 查询已有 post，避免 duplicate `-2`。

## 优先级
P1

## 验收标准（高层）
- 5 个生产 URL 均 HTTP 200。
- 每篇 1200–1800 英文词、FAQ >=4、CTA >=1、internal links >=3。
- Best tools 先写分类，不乱排具体 Top 10。
- GEO tools 有采购标准和 red flags，不偏向单一工具。
- ChatGPT checker 合规措辞，不声称精准检测所有推荐排名。
- Agency / audit 文章适合 partner lead 转化，但不承诺 UseGEO 自营完整服务。
