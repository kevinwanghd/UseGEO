# UseGEO Commercialization Handoff（2026-06-23）

> 用途：给下一轮 Hermes / Codex / 人工继续 UseGEO 商业化工作。  
> 项目路径：`D:\Code\UseGEO`  
> GitHub：`git@github.com:kevinwanghd/UseGEO.git`  
> 当前可信远端：以 SSH `git ls-remote git@github.com:kevinwanghd/UseGEO.git refs/heads/master` 为准。`origin` HTTPS tracking 仍可能 stale。

---

## 1. 当前定位

UseGEO 不是 Kevin 自营完整 GEO 服务公司。

当前商业闭环：

```text
GEO 学习内容
→ SEO/GEO 流量
→ 免费 Scanner
→ sample report / evidence
→ explicit consent lead
→ hot/warm/cold lead 分级
→ partner pipeline / CSV 导出
→ sponsor slots / affiliate / partner referral / lead resale
```

后续所有产品、页面、文案、outreach 都必须遵守：

- 不承诺 guaranteed AI ranking。
- 不承诺 UseGEO 自己交付完整 GEO audit / consulting / fix sprint。
- partner follow-up 必须基于 explicit consent。
- 不自动转发 lead，不自动发 outreach，除非 Kevin 明确确认。

---

## 2. 当前 Git 状态

最后确认时：

```text
本地 HEAD / SSH remote master: 6b8500fac296848229e26a0c4fcc92bddeef2db4
最新提交: 6b8500f test: record CR-024 production verification
```

验证命令：

```bash
cd /d/Code/UseGEO
git status --short --branch
git ls-remote git@github.com:kevinwanghd/UseGEO.git refs/heads/master
git log --oneline -8
```

注意：`git status` 仍可能显示类似：

```text
## master...origin/master [ahead N]
```

这是因为 `origin` 是 HTTPS tracking，历史上 Windows 到 GitHub HTTPS/TLS 不稳定。判断是否已 push，必须看 SSH `ls-remote`。

当前已知未跟踪文件：

```text
DeliverHQ/docs/HANDOFF-2026-06-22-usegeo-growth-batch.md
```

不要随手删除；如需清理，先问 Kevin。

---

## 3. 已完成事项总览

### 3.1 内容与流量承接

已完成：

- CR-010：Expand Money Pages
  - `/geo-checklist/`
  - `/ai-visibility-tools/`
  - `/best-geo-agencies/`
- CR-011：Publish GEO Basics Cluster
- CR-012：Content Template & Publishing System
- CR-013：Publish Tactical GEO Cluster
- CR-014：Publish Tools & Commercial Cluster
- CR-015：Publish GEO Use Case Cluster

结果：UseGEO 已有基础主题内容、tactical 内容、commercial/tools 内容、use-case 内容和 3 个 money pages。

---

### 3.2 分发与商业验证资产

已完成：

- CR-016：Distribution Launch System
  - LinkedIn drafts
  - X/Twitter drafts
  - Community drafts
  - Partner outreach template
  - 无外部发送

- CR-017：Partner & Sponsor Prospecting
  - 50 个 partner/sponsor prospect
  - 20 个 high-fit
  - sponsor offer
  - lead referral offer
  - first-20 outreach queue
  - 无外部发送

注意：这些是资产准备，不代表已经联系任何 partner。

---

### 3.3 站内 SEO/GEO 技术基础

已完成并生产验证：

- CR-018：On-site SEO/GEO Technical Foundation

主要改动：

- sitemap / robots / title / meta / internal links audit
- `inc/seo.php` 安全 JSON-LD：Organization / WebSite / WebPage / Article / FAQPage
- FAQ schema 仅镜像可见 FAQ
- article template 增加 3 个 money page links
- WordPress ZIP 上传并生产验证

生产验证结果：PASS。

---

### 3.4 Sponsor reporting / inventory tracking

已完成并生产验证：

- CR-019：Sponsor Reporting & Inventory Tracking

主要改动：

- sponsor click event：`sponsor_click`，包含 `placement`、`sponsor_url`
- sponsor impression event：`sponsor_impression`，只记录 `placement`
- sponsor slot start/end date
- 到期自动隐藏
- sponsor link rel：`noopener sponsored nofollow`

生产验证结果：PASS。

---

### 3.5 Lead ops 增强

已完成并生产验证：

- CR-020：Lead Ops Enhancements

主要改动：

- `lead_quality_auto` 和 `lead_quality_override` 分开保存
- override 为空时回到 auto quality
- partner_status bulk action
- CSV export 支持 `start_date` / `end_date`
- partner notes append log，不覆盖旧备注

生产 smoke 验证：PASS。

---

### 3.6 文章页和公开页视觉统一

已完成并生产验证：

- CR-021：Article Page Conversion Redesign
- CR-022：Public Page Visual Unification

主要改动：

- 所有文章页走新 `single.php` / `single.css`
- 去掉重复 H1
- TOC 只收 H2，不收 FAQ H3 长问题
- 减少重复 CTA
- 保留 scanner / sponsor / money links
- 普通页 / money pages 统一 `page.php` + `base.css`
- `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/` 均 H1 count = 1

生产验证结果：PASS。

---

### 3.7 商业转化层

已完成并生产验证：

- CR-023：Commercial Conversion Layer
- CR-024：Commercial Design Upgrade

新增前台商业页面：

- `/ai-visibility-audit/`
- `/sample-report/`
- `/partners/`

首页新增商业闭环模块：

```text
Scan → Report → Match
```

CR-024 将商业页从“说明页”升级为更产品化页面：

- `/ai-visibility-audit/`
  - audit input mock
  - readiness preview
  - sample score card

- `/sample-report/`
  - report mockup
  - score
  - 5 dimensions
  - evidence table
  - partner handoff

- `/partners/`
  - lead object
  - sponsor beta
  - lead referral beta
  - sponsor inventory

生产验证结果：PASS。

---

## 4. 生产已验证的关键页面

当前已上线并验证的关键路径：

```text
/
/geo-checklist/
/ai-visibility-tools/
/best-geo-agencies/
/geo-for-content-marketing-teams/
/ai-visibility-audit/
/sample-report/
/partners/
```

CR-024 生产验证结果：

- `/ai-visibility-audit/`：audit input + readiness preview present
- `/sample-report/`：report mockup present
- `/partners/`：partner packages + lead object present
- `/`：commercial loop present
- no bad claims：PASS

---

## 5. 最新上传包

最近几轮 ZIP：

```text
D:\Code\UseGEO\dist\usegeo-theme-cr022-public-unification-fix-20260622-190334.zip
D:\Code\UseGEO\dist\usegeo-theme-cr023-commercial-conversion-20260622-213126.zip
D:\Code\UseGEO\dist\usegeo-theme-cr024-commercial-design-20260623-090330.zip
```

当前生产已经上传并验证的是：

```text
D:\Code\UseGEO\dist\usegeo-theme-cr024-commercial-design-20260623-090330.zip
```

---

## 6. 当前仍未完成 / 建议下一步

### P0：CR-025 Partner/Sponsor Outreach Validation

这是下一步最值得做的事。

目标：验证是否真的有人愿意合作、赞助、买 lead。

范围建议：

1. 从 CR-017 的 50 个 prospect 里筛 20 个可联系目标。
2. 补 contact path：contact form / business email / LinkedIn company / partner page。
3. 不抓私人邮箱，不伪造联系人。
4. 生成最终 outreach queue：
   - company
   - category
   - contact path
   - offer type
   - message variant
   - send_status = `ready_for_kevin_review_not_sent`
5. 准备 3 套消息：
   - AI visibility tool sponsor
   - GEO/SEO agency lead referral
   - consultant / expert follow-up partner
6. 等 Kevin 明确确认后才发送。

验收标准：

- 至少 20 个可联系目标。
- 每个目标有明确 contact path。
- 每条 message 对应一种合作 offer。
- 没有外部发送副作用。
- 没有 fake personalization。

---

### P1：CR-026 Analytics & Conversion Tracking Check

目标：确认商业漏斗事件能被看到。

范围建议：

- `/ai-visibility-audit/` CTA click
- `/sample-report/` CTA click
- `/partners/` apply CTA click
- homepage commercial loop click
- scanner submit / scanner complete
- lead form submit attempt
- partner consent lead
- sponsor_click / sponsor_impression 已有，需复查 GTM 是否可见

验收标准：

- GTM / GA4 能看到关键事件。
- 能区分 commercial routes。
- 不重复注入 GTM/GA4。

---

### P1：CR-027 Commercial Copy Tightening

目标：把商业页文案从“能说明”进一步变成“能成交”。

范围建议：

- `/ai-visibility-audit/`
- `/sample-report/`
- `/partners/`
- 首页 commercial loop

重点补：

- who this is for
- what happens after scan
- when to request partner follow-up
- what we never share without consent
- beta sponsor / lead referral offer

建议在 CR-025 outreach 后，根据真实反馈再做。

---

### P2：CR-028 Homepage Deep Conversion Redesign

首页目前结构未深改，只加了 commercial loop。后续可以单独重做首页：

- hero 直接放 scanner input
- sample report preview
- commercial path
- methodology proof
- partner/sponsor CTA

不建议在 CR-025 前做，因为还不知道真实转化主对象是用户、agency、tool vendor 还是 sponsor。

---

## 7. 不建议现在做的事

1. 不要继续批量写内容。
2. 不要做完整 SaaS dashboard。
3. 不要接支付。
4. 不要做自动 partner matching。
5. 不要继续只做视觉优化。
6. 不要未确认就对外发 outreach。

---

## 8. 下一轮启动建议语

可以直接对新 session 说：

```text
读取 D:\Code\UseGEO\DeliverHQ\docs\HANDOFF-2026-06-23-usegeo-commercialization.md，继续 UseGEO 商业化工作。先确认 git status 和远端 SSH head；注意 origin HTTPS tracking 可能 stale。下一步启动 CR-025 Partner/Sponsor Outreach Validation，不对外发送，只生成 ready_for_kevin_review_not_sent 的 20 个 outreach queue。
```

---

## 9. 操作注意事项

- Python 使用：`C:/Users/kevin/AppData/Local/Programs/Python/Python312/python.exe`
- WordPress HTTPS / Python urllib 偶发 TLS EOF，生产 verifier 可用 Node fetch fallback。
- 上传主题 ZIP 需要 Kevin 通过 WordPress admin 手动上传；agent browser 会被 Cloudflare check 挡住。
- Git push 用 SSH：

```bash
git push git@github.com:kevinwanghd/UseGEO.git master
git ls-remote git@github.com:kevinwanghd/UseGEO.git refs/heads/master
```

- 不依赖 `origin/master` 的 ahead/behind。
- 不要删除未跟踪 handoff 文件，除非 Kevin 明确确认。

---

## 10. 当前一句话结论

UseGEO 已从“内容站 + scanner 原型”推进到“内容流量 + scanner + sample report + partner/sponsor 商业转化层”的可验证 MVP。下一步不是继续建页面，而是做 CR-025：用 20 个高相关 partner/sponsor prospects 验证是否有人愿意合作或购买 lead / sponsor inventory。
