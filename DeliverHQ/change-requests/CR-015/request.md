# Request: Publish GEO Use Case Cluster

## CR-ID
CR-015

## 提出人
Kevin

## 提出日期
2026-06-22

## 需求背景
UseGEO 已发布 basics、tactical、tools/commercial 内容集群。下一步需要按具体用户场景发布 Use Case 内容集群，让不同读者理解 GEO 在自己业务类型里的落地方式，并导向 Scanner、money pages、tools/agency 内容和 partner referral。

## 业务目标
发布 5 篇 Use Case 文章：B2B SaaS、Agencies、Ecommerce Brands、Developer Tools、Content Marketing Teams。

## 功能描述
按 CR-012 内容模板生成并发布 5 篇 WordPress posts。每篇包含 Quick answer、use-case specific sections、Checklist、Example workflow、Common mistakes、FAQ、internal links、Scanner CTA 和 partner referral angle。

## 非功能需求
- 每篇 1200–1800 英文词。
- 每篇至少 4 FAQ、1 Scanner CTA、3 内链。
- 不把 ecommerce 强套 B2B SaaS，不承诺 AI ranking，不承诺 UseGEO 自营完整交付。
- 发布脚本只从 process env 读取 WP_USER/WP_APP_PASSWORD，不落盘。

## 约束条件
- 不修改 WordPress theme 代码。
- 优先按 slug 查询已有 post，避免 duplicate `-2`。

## 优先级
P1

## 验收标准（高层）
- 5 个生产 URL 均 HTTP 200。
- 每篇 1200–1800 英文词、FAQ >=4、CTA >=1、internal links >=3。
- 每篇包含对应 use case marker：B2B SaaS homepage/comparison/proof；agency sponsor angle；ecommerce not SaaS; devtools docs/comparison/community proof；content team workflow.
- forbidden claim scan 0 命中。
