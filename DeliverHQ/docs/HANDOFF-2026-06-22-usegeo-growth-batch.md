# UseGEO Growth Batch Handoff（2026-06-22）

> 用途：重启电脑 / 新 Hermes 窗口后继续 UseGEO 后续 CR。  
> 项目路径：`D:\Code\UseGEO`  
> GitHub：`git@github.com:kevinwanghd/UseGEO.git` / `https://github.com/kevinwanghd/UseGEO`  
> 当前本地分支：`master`

---

## 1. 当前商业定位（继续遵守）

UseGEO 不是 Kevin 自营完整 GEO 服务公司。

UseGEO 的商业闭环是：

```text
GEO 学习内容
→ SEO/GEO 流量
→ 真实 Scanner
→ LLM Summary
→ Partner consent lead
→ Hot/Warm/Cold lead 分级
→ Partner pipeline / CSV 导出
→ Sponsor slots / affiliate / lead resale
```

后续所有 PRD / CR / 文案 / 分发 / outreach 都必须围绕：

- 学习内容获取流量
- Scanner 建立信任和 qualification
- partner referral / lead resale
- sponsor / affiliate
- 不默认承诺 UseGEO 亲自交付完整 GEO audit / consulting / fix sprint

---

## 2. 当前 Git / 远端状态

最后确认时：

```text
本地 HEAD: 1abd0d3 docs: add partner sponsor prospecting assets
SSH remote master: 1abd0d3c05b4ebdf81c07e2e2a389c99d6e80bdf
```

`git status --short --branch` 仍可能显示：

```text
## master...origin/master [ahead 7]
```

这不是因为没有 push 成功，而是因为 `origin` 配的是 HTTPS，而当前 Windows 到 GitHub HTTPS/TLS 握手失败，导致 `origin/master` remote-tracking ref 没刷新。实际我已经用 SSH 成功推送到 GitHub。

重启后建议先执行：

```bash
cd /d/Code/UseGEO

git status --short --branch
git log --oneline -12
git ls-remote git@github.com:kevinwanghd/UseGEO.git refs/heads/master
```

如果需要让本地 tracking 状态恢复干净，优先改 origin 为 SSH：

```bash
git remote set-url origin git@github.com:kevinwanghd/UseGEO.git
git fetch origin
git status --short --branch
```

不要继续依赖 HTTPS push/fetch，除非 TLS 问题恢复。

---

## 3. 安全状态：WordPress Application Password

Kevin 明确决定：

> 先保留当前 WordPress Application Password，等所有内容 / 商业验证 CR 做完后再撤销。

执行规则：

1. 只通过单次命令的 process-local env 使用。
2. 不写入 `.env`、脚本、evidence、markdown、git history。
3. 每个提交前继续扫描密码片段，要求：

```text
SECRET_TOKEN_HITS 0
```

4. 所有内容批量 CR 结束后，提醒 Kevin 立刻撤销该 Application Password。

注意：本文档不记录密码本体。重启后如需继续发 WordPress 内容，Kevin 需要重新提供/配置临时密码，仍不能落盘。

---

## 4. 已完成的 CR 总览

### CR-010：Expand Money Pages

状态：完成，生产已发布并验收。

页面：

- https://usegeo.blog/geo-checklist/
- https://usegeo.blog/ai-visibility-tools/
- https://usegeo.blog/best-geo-agencies/

Git commit：

```text
43484cd content: expand UseGEO money pages
```

生产验收：PASS。

---

### CR-011：Publish GEO Basics Cluster

状态：完成，生产已发布并验收。

文章：

- https://usegeo.blog/what-is-geo-generative-engine-optimization/
- https://usegeo.blog/geo-vs-seo-ai-content-differences/
- https://usegeo.blog/what-is-ai-visibility/
- https://usegeo.blog/why-your-brand-does-not-show-up-in-chatgpt-recommendations/
- https://usegeo.blog/how-ai-search-chooses-which-brands-to-mention/

Git commit：

```text
7f04ae6 content: publish GEO basics cluster
```

生产验收：PASS。

---

### CR-012：Content Template & Publishing System

状态：完成。无需生产部署。

关键产物：

```text
DeliverHQ/docs/CONTENT-TEMPLATE.md
DeliverHQ/change-requests/CR-012/outputs/article-brief-template.yml
DeliverHQ/change-requests/CR-012/outputs/article-html-skeleton.html
DeliverHQ/change-requests/CR-012/outputs/publishing-checklist.md
DeliverHQ/change-requests/CR-012/outputs/faq-jsonld-plan.md
DeliverHQ/change-requests/CR-012/evidence/verify_content_template.py
```

Git commit：

```text
d7a48ea docs: add UseGEO content publishing template
```

本地验收：PASS。

后续内容 CR 应继续复用 `DeliverHQ/docs/CONTENT-TEMPLATE.md`。

---

### CR-013：Publish Tactical GEO Cluster

状态：完成，生产已发布并验收。

文章：

- https://usegeo.blog/how-to-improve-entity-clarity-for-ai-search/
- https://usegeo.blog/how-to-build-comparison-pages-for-geo/
- https://usegeo.blog/how-proof-signals-help-ai-recommend-your-brand/
- https://usegeo.blog/schema-markup-for-ai-visibility/
- https://usegeo.blog/how-to-make-your-homepage-easier-for-llms-to-understand/

Git commit：

```text
06c7264 content: publish tactical GEO cluster
```

生产验收：PASS。

---

### CR-014：Publish Tools & Commercial Cluster

状态：完成，生产已发布并验收。

文章：

- https://usegeo.blog/best-ai-visibility-tools/
- https://usegeo.blog/geo-tools-what-to-look-for/
- https://usegeo.blog/chatgpt-visibility-checker-what-it-can-and-cannot-measure/
- https://usegeo.blog/how-to-choose-a-geo-agency/
- https://usegeo.blog/geo-audit-what-should-be-included/

Git commit：

```text
78aeaa3 content: publish tools commercial cluster
```

生产验收：PASS。

---

### CR-015：Publish GEO Use Case Cluster

状态：完成，生产已发布并验收。

文章：

- https://usegeo.blog/geo-for-b2b-saas/
- https://usegeo.blog/geo-for-agencies/
- https://usegeo.blog/geo-for-ecommerce-brands/
- https://usegeo.blog/geo-for-developer-tools/
- https://usegeo.blog/geo-for-content-marketing-teams/

Git commit：

```text
9cbad81 content: publish GEO use case cluster
```

生产验收：PASS。

---

### CR-016：Distribution Launch System

状态：完成。无外部发布 / 无外部发送。

关键产物：

```text
DeliverHQ/change-requests/CR-016/outputs/linkedin-template.md
DeliverHQ/change-requests/CR-016/outputs/x-twitter-template.md
DeliverHQ/change-requests/CR-016/outputs/community-template.md
DeliverHQ/change-requests/CR-016/outputs/partner-outreach-template.md
DeliverHQ/change-requests/CR-016/outputs/linkedin-drafts.json
DeliverHQ/change-requests/CR-016/outputs/x-drafts.json
DeliverHQ/change-requests/CR-016/outputs/community-drafts.json
DeliverHQ/change-requests/CR-016/outputs/partner-prospect-list.json
```

数量：

- LinkedIn drafts：15 条
- X/Twitter drafts：25 条
- Community drafts：5 条
- Partner seed list：20 条

Git commit：

```text
1c05dca docs: add UseGEO distribution launch system
```

本地验收：PASS。

注意：没有发 LinkedIn / X / Reddit / Indie Hackers / 邮件 / DM。

---

### CR-017：Partner & Sponsor Prospecting

状态：完成。无外部发送。

关键产物：

```text
DeliverHQ/change-requests/CR-017/outputs/partner-prospect-list.json
DeliverHQ/change-requests/CR-017/outputs/partner-prospect-list.csv
DeliverHQ/change-requests/CR-017/outputs/first-20-outreach-queue.json
DeliverHQ/change-requests/CR-017/outputs/sponsor-offer.md
DeliverHQ/change-requests/CR-017/outputs/lead-referral-offer.md
DeliverHQ/change-requests/CR-017/outputs/outreach-templates.md
```

验收结果：

- prospect_count = 50
- high_fit_count = 20
- category coverage = 4/4
  - SEO agency
  - content strategy agency
  - GEO consultant
  - AI visibility tool
- first-20 queue = 20
- sent_count = 0

Git commit：

```text
1abd0d3 docs: add partner sponsor prospecting assets
```

本地验收：PASS。

注意：没有发送邮件/DM。联系人未核实的地方使用 `TBD`，不要直接发送。

---

## 5. 仍未开始 / 下一步计划

### CR-018：On-site SEO/GEO Technical Foundation

状态：未开始。建议下一步做。

范围来自 `SEO-GEO-GROWTH-TASK-LIST.md`：

1. sitemap 检查
   - `/sitemap.xml` 或插件 sitemap 可访问
   - 新页面被包含

2. 基础 schema 策略
   - Organization schema
   - Article schema
   - FAQ schema
   - 不写虚假数据
   - 至少 money pages 有基本 schema

3. 内链审计
   - 3 money pages 互链
   - 20 篇文章都链接 money pages
   - 首页链接 3 money pages

4. 页面 title / meta description 规范
   - money pages 有唯一 title/meta
   - 不堆关键词

建议 CR-018 做成“技术审计 + 最小实现/规划”混合：

- 先 audit 当前生产 sitemap / robots / money page HTML / article HTML / title/meta / internal links。
- 如果主题已支持 schema 或插件已有 schema，先验证，不重复实现。
- 如果没有 schema，先设计最小安全 schema 输出方案，必要时再进入代码改造。
- 禁止 fake schema：不能 invent rating/review/author credential。

---

### CR-019：Sponsor Reporting & Inventory Tracking

状态：未开始。

范围：

1. sponsor click event
   - `sponsor_click`
   - placement
   - sponsor_url
   - GTM 可看到事件

2. sponsor impression 标记
   - 不影响性能
   - 只记录 placement

3. sponsor 后台 start/end date
   - 到期自动隐藏
   - 未设置时长期展示

4. sponsor slot nofollow/sponsored 控制
   - 默认 `rel=sponsored`
   - 外链安全

建议等 CR-018 完成后再做，因为 CR-019 可能涉及主题代码和前端 tracking。

---

### CR-020：Lead Ops Enhancements

状态：未开始。

范围：

1. lead_quality 手动覆盖
   - 自动 quality 和人工 override 分开记录

2. partner_status 批量操作
   - 后台 bulk action 可用

3. CSV 导出增加日期过滤
   - start_date / end_date

4. Partner notes 历史记录
   - append log
   - 不覆盖旧备注

建议 CR-019 后做，因为这是后台运营增强，风险比内容 CR 高，需要更严格验证。

---

## 6. 重要技术/流程注意事项

### 6.1 Python 路径

当前可用 Python：

```text
C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe
```

DeliverHQ `verification-manifest.yml` 里不要写 MSYS `/c/Users/.../python.exe`，QualityGate 里可能失败。使用 Windows-native forward-slash：

```text
C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe ...
```

### 6.2 WordPress REST / HTTPS

Windows 上 `curl` / Python `urllib` 对 `usegeo.blog` 偶发 TLS handshake 失败。Node 24 的内置 `fetch` 更稳定。

后续 WordPress REST 发布优先使用 Node `.mjs` 脚本：

```bash
export WP_USER='kevin.wang.hd@gmail.com'
export WP_APP_PASSWORD=$(printf '%s' '<Kevin 临时 Application Password>')
node DeliverHQ/change-requests/CR-XXX/evidence/publish_xxx.mjs
```

生产 fetch verifier 如 Python TLS 失败，也可在 Python verifier 中调用 Node fetch。

### 6.3 GitHub push

HTTPS remote 当前有 TLS 问题。SSH 可用。

推送用：

```bash
git push git@github.com:kevinwanghd/UseGEO.git master
```

如果要修复本地 tracking：

```bash
git remote set-url origin git@github.com:kevinwanghd/UseGEO.git
git fetch origin
```

### 6.4 Worktree

`init_cr.py` 多次提示 worktree limit reached：

```text
Maximum worktrees limit reached (10)
```

我后续 CR 都直接在主工作区开发并提交。因为这些 CR 大多是 DeliverHQ 文档/内容产物，风险可控。

如果后续 CR-018/019/020 涉及主题代码，建议先清理旧 worktrees 或手动创建隔离 worktree。

不要在未确认的情况下删除 worktree；如果要清理，先列清单给 Kevin 确认。

---

## 7. 重启后建议启动语

可以直接对新窗口说：

```text
读取 D:\Code\UseGEO\DeliverHQ\docs\HANDOFF-2026-06-22-usegeo-growth-batch.md，继续 UseGEO 工作。先确认 git status 和远端 SSH head；注意 origin HTTPS tracking 可能 stale。接下来启动 CR-018 On-site SEO/GEO Technical Foundation，继续遵守 DeliverHQ 所有产物放 DeliverHQ/ 的规则。
```

---

## 8. 重启后第一组命令

```bash
cd /d/Code/UseGEO

git status --short --branch
git log --oneline -12
git ls-remote git@github.com:kevinwanghd/UseGEO.git refs/heads/master

# 可选：修复 origin tracking
# git remote set-url origin git@github.com:kevinwanghd/UseGEO.git
# git fetch origin
# git status --short --branch
```

如果继续做 CR-018，先读：

```text
DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md lines 563-596
DeliverHQ/docs/CONTENT-TEMPLATE.md
DeliverHQ/docs/PRD.md
DeliverHQ/change-requests/CR-017/outputs/sponsor-offer.md
DeliverHQ/change-requests/CR-017/outputs/lead-referral-offer.md
```

---

## 9. 当前结论

已经完成一大批内容与商业验证基础设施：

- 流量承接：money pages
- 主题权威：basics / tactical / tools / use cases
- 生产流程：content template
- 分发冷启动：social/community/outreach drafts
- 商业验证：partner/sponsor prospects + offer drafts

下一阶段应该从“继续发内容”切到“站内 SEO/GEO 技术增强 + 追踪能力”：

1. CR-018：On-site SEO/GEO Technical Foundation
2. CR-019：Sponsor Reporting & Inventory Tracking
3. CR-020：Lead Ops Enhancements

这三个会更偏主题代码 / 站内技术 / 后台运营，建议比内容 CR 更谨慎验证。
