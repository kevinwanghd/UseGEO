# Acceptance Spec: Publish GEO Checklist Page

## CR-ID
CR-007

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-CONTENT-LIBRARY
  prd_hash: "77f775574a7a"
```

---

## 1. Data Spec

Entity: WordPress Page

字段：title / slug / status / content

## 2. Interface Spec

- Title: `Generative Engine Optimization Checklist`
- Slug: `geo-checklist`
- Status: `publish`
- Content source: `outputs/geo-checklist-content.html`

## 3. Behavior Spec

### 场景 1: 页面发布
**审查结果**: ✅ PASS
- `https://usegeo.blog/geo-checklist/` 返回 200。

### 场景 2: Checklist 内容
**审查结果**: ✅ PASS
- 页面包含 6 个 section：Entity clarity / Content structure / Proof signals / Comparison readiness / AI extractability / Next-step clarity。

### 场景 3: Scanner CTA
**审查结果**: ✅ PASS
- 页面至少包含 2 个 `/#scanner` CTA。

### 场景 4: 商业内链
**审查结果**: ✅ PASS
- 页面包含 `/ai-visibility-tools/` 与 `/best-geo-agencies/` 链接。

## 4. 非功能验收

页面内容不承诺 UseGEO 自营完整服务，不承诺 AI 推荐排名。

## 5. 风险

目标页面发布需要 WordPress 后台权限。
