# UseGEO 工作交接文档（2026-06-21）

> 用途：在新的 Hermes/AI 窗口继续当前 UseGEO 工作。  
> 项目路径：`D:\Code\UseGEO`  
> GitHub：`https://github.com/kevinwanghd/UseGEO`  
> 当前分支：`master`

---

## 1. 当前商业定位（必须遵守）

Kevin 已明确：UseGEO 不是自营 GEO 服务公司。

UseGEO 的商业模式是：

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

后续建议、PRD、CR、文案都必须围绕：

- 学习内容获取流量
- 广告 / 赞助 / affiliate
- partner referral
- lead resale
- Scanner 用于信任、分流、lead qualification

不要默认建议 Kevin 亲自交付完整 GEO 审计 / 咨询 / Fix Sprint 服务。

---

## 2. 当前本地 Git 状态

最后检查时：

```text
## master...origin/master
?? DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md
```

说明：

- `master` 已与远端同步。
- 当前只有一个未跟踪文件：
  `DeliverHQ/docs/SEO-GEO-GROWTH-TASK-LIST.md`
- 本交接文档创建后也会是未跟踪文件，除非后续提交。

建议新窗口开始后先执行：

```bash
cd /d/Code/UseGEO
git status --short --branch
```

---

## 3. 已完成并生产验证的 CR

### CR-LEGACY-SCAN：老项目逆向基线

状态：完成。

产物：

```text
DeliverHQ/change-requests/CR-LEGACY-SCAN/
```

包含：

- `reverse-spec-candidates.yml`
- `human-review-checklist.md`
- `acceptance-spec.md`
- `traceability.yml`
- `known-deviations.md`

用途：整站基线，不直接开发。

---

### CR-001：Real Scanner v1

状态：完成，生产验证通过。

实现：

- `/wp-json/usegeo/v1/scan`
- SSRF 防护
- HTML 抓取/解析
- 5 维度评分
- findings
- evidence 区
- lead snapshot 保存
- 后台 GEO Score / Weakest Dimension
- 邮件通知

生产验证：通过。

---

### CR-002：LLM Summary + 代理 base_url

状态：完成，生产验证通过。

生产配置已由 Kevin 在 `wp-config.php` 中添加：

```php
define('USEGEO_LLM_API_KEY', "<Kevin 已配置，勿询问/勿泄漏>");
define('USEGEO_LLM_BASE_URL', 'https://chat.aiprox.net');
define('USEGEO_LLM_MODEL', 'gpt-5.4-mini');
```

注意：不要让 Kevin 在聊天里贴 key。后续如需改 key，让 Kevin 自己在 Hostinger File Manager / `wp-config.php` 修改。

生产验证：

- `llm_summary.available=true`
- summary 正常返回
- secret scan 通过

---

### CR-003：Partner Lead Monetization Foundation

状态：完成，生产验证通过。

实现：

- partner_consent
- budget_range
- region
- lead_quality hot/warm/cold
- 邮件显示 partner 字段
- 后台显示 partner 字段

生产验证：通过。

测试 lead：

- `kevin+usegeo-cr003-cold@example.com`
- `kevin+usegeo-cr003-hot@example.com`

---

### CR-004：Partner Lead Export / Pipeline

状态：完成，生产验证通过。

实现：

- 后台筛选：partner_consent / lead_quality / partner_status
- lead 详情编辑：partner_status / partner_notes
- CSV 导出：只导出 consent=yes + hot/warm
- 需要 `manage_options` + nonce

生产验证：Kevin 已确认后台正常。

---

### CR-005：Sponsor / Ad Inventory Foundation

状态：完成，生产验证通过。

实现：

- Appearance → UseGEO Sponsors
- 三个 placement：homepage / scanner_result / article_sidebar
- sponsor card：Sponsored 标签 + title + description + CTA + rel sponsored

生产验证：

- active=true 显示
- active=false 隐藏
- 链接安全属性正确

---

### CR-006：Content SEO Cluster Foundation

状态：完成，生产验证通过。

实现：

- 首页 SEO cluster 区块
- 三个入口：
  - `/geo-checklist/`
  - `/ai-visibility-tools/`
  - `/best-geo-agencies/`
- CTA 回 Scanner

生产验证：通过。

---

### CR-007：Publish GEO Checklist Page

状态：完成，生产验证通过。

页面：

```text
https://usegeo.blog/geo-checklist/
```

页面已通过 Application Password 自动创建。

---

### CR-008：Publish AI Visibility Tools Page

状态：完成，生产验证通过。

页面：

```text
https://usegeo.blog/ai-visibility-tools/
```

---

### CR-009：Publish Best GEO Agencies Page

状态：完成，生产验证通过。

页面：

```text
https://usegeo.blog/best-geo-agencies/
```

---

## 4. 生产站点当前状态

生产站点：

```text
https://usegeo.blog
```

已具备：

- 首页真实 Scanner
- LLM Summary
- Evidence 区
- Lead 保存 scan snapshot
- Partner consent / lead quality
- Partner pipeline / CSV 导出
- Sponsor slots
- SEO cluster 首页入口
- 三个基础 money pages：
  - GEO Checklist
  - AI Visibility Tools
  - Best GEO Agencies

Cloudflare/Hostinger 可能会拦 agent browser，公开验证优先用 `curl` / Python HTTP。

---

## 5. 安全事项

### Application Password

Kevin 之前为自动发布内容创建过：

```text
hermes-cr007
```

后续需要确认是否已经撤销。

新窗口应提醒 Kevin：

```text
请确认 WordPress Profile → Application Passwords 中 hermes-cr007 已撤销。
```

如尚未撤销，应让 Kevin 立即撤销。

### 生产 key

不要让 Kevin 发真实 LLM key 到聊天。

如果需要验证配置：

- 只验证 `llm_summary.available=true`
- 不打印 key
- 不读取 `wp-config.php` 的 secret 行

---

## 6. 已创建的重要文档

### Growth task list

路径：

```text
D:\Code\UseGEO\DeliverHQ\docs\SEO-GEO-GROWTH-TASK-LIST.md
```

内容：

- SEO/GEO 获流量总 backlog
- CR-010 到 CR-020 建议拆分
- 48 个具体任务
- 推荐执行顺序

这个文件当前未提交，建议下一步提交。

### 本交接文档

路径：

```text
D:\Code\UseGEO\DeliverHQ\docs\HANDOFF-2026-06-21-usegeo-growth.md
```

---

## 7. 推荐下一步

最关键下一步：

```text
CR-010 Expand Money Pages
```

原因：

- 当前三张 money pages 已发布，但内容仍偏薄；
- 想通过 SEO/GEO 获取稳定流量，需要把它们扩写到可排名级别；
- 它们会承接后续所有文章、广告、lead 和 partner referral。

CR-010 范围建议：

1. 扩写 `/geo-checklist/`
2. 扩写 `/ai-visibility-tools/`
3. 扩写 `/best-geo-agencies/`
4. 三页增加统一底部 CTA
5. 三页增加 FAQ 区

验收目标：

- 每页 2500–3500 英文词
- 至少 5–7 FAQ
- 至少 2 个 Scanner CTA
- 三页互链
- 不承诺 AI ranking
- 不承诺 UseGEO 自营完整服务

---

## 8. 新窗口建议启动语

可以直接对新窗口说：

```text
读取 D:\Code\UseGEO\DeliverHQ\docs\HANDOFF-2026-06-21-usegeo-growth.md，继续 UseGEO 工作。先确认 git status，然后提交 SEO-GEO-GROWTH-TASK-LIST.md 和交接文档，再启动 CR-010 Expand Money Pages。
```

---

## 9. 验证命令速查

```bash
cd /d/Code/UseGEO

git status --short --branch

# Scanner API
python DeliverHQ/change-requests/CR-002/evidence/verify_llm_config.py --mode llm
python DeliverHQ/change-requests/CR-002/evidence/verify_llm_config.py --mode secret-scan

# CR-001 静态验证
python DeliverHQ/change-requests/CR-001/evidence/static_verify.py

# 内容页验证（示例）
python - <<'PY'
import urllib.request
for slug in ['geo-checklist','ai-visibility-tools','best-geo-agencies']:
    html = urllib.request.urlopen(f'https://usegeo.blog/{slug}/?t=verify', timeout=30).read().decode(errors='replace')
    print(slug, len(html), slug.replace('-', ' ')[:10])
PY
```
