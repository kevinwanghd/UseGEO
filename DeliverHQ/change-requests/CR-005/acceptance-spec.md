# Acceptance Spec: Sponsor / Ad Inventory Foundation

## CR-ID
CR-005

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-SPONSOR-INVENTORY
  prd_hash: 22b739cd2479
```

---

## 1. Data Spec

| 实体 | 说明 | 字段 |
|---|---|---|
| SponsorSlot | 手动赞助位配置 | placement / active / title / description / cta_label / url |

字段约束：placement ∈ homepage / scanner_result / article_sidebar；active 为布尔；url 必须 http/https；title/description/cta_label 为纯文本。

## 2. Interface Spec

后台：Appearance → UseGEO Sponsors 页面，配置三个 placement。前台：`usegeo_render_sponsor_slot($placement)` 渲染卡片。

## 3. Behavior Spec

### 场景 1: active sponsor 显示
**审查结果**: ✅ PASS
- Given placement active=true 且 url/title 非空；Then 前台显示 Sponsored 卡片。

### 场景 2: inactive sponsor 不显示
**审查结果**: ✅ PASS
- Given placement active=false；Then 前台不输出卡片。

### 场景 3: 外链安全
**审查结果**: ✅ PASS
- Then 链接使用 `esc_url`、`target=_blank`、`rel=noopener sponsored`。

### 场景 4: 后台保存
**审查结果**: ✅ PASS
- Then 只有 manage_options 用户可保存，且必须 nonce。

### 场景 5: 不伪装推荐
**审查结果**: ✅ PASS
- Then 卡片必须显示 Sponsored 标记。

## 4. 非功能验收

不接广告平台；不自动竞价；不统计结算；仅手动配置赞助位。

## 5. 风险

Sponsor 文案需由 Kevin 审核；本 CR 只提供展示和配置能力。
