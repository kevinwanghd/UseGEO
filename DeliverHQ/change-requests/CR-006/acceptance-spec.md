# Acceptance Spec: Content SEO Cluster Foundation

## CR-ID
CR-006

## 来源锚点（derived_from）

```yaml
derived_from:
  prd_section: PRD-CONTENT-LIBRARY
  prd_hash: "77f775574a7a"
```

---

## 1. Data Spec

Entity: SEOClusterCard，字段：title / description / url / cta_label。

## 2. Interface Spec

新增模板：`template-parts/content-hub/seo-cluster.php`，由 `front-page.php` 引入。

## 3. Behavior Spec

### 场景 1: 首页显示 SEO cluster
**审查结果**: ✅ PASS
- 首页展示 3 个商业内容入口。

### 场景 2: 链接正确
**审查结果**: ✅ PASS
- links 指向 `/geo-checklist/`、`/ai-visibility-tools/`、`/best-geo-agencies/`。

### 场景 3: 不承诺自营服务
**审查结果**: ✅ PASS
- 文案聚焦学习、工具、目录和 partner discovery。

### 场景 4: Scanner CTA 保留
**审查结果**: ✅ PASS
- 区块包含回到 `/#scanner` 的 CTA。

## 4. 非功能验收

移动端卡片可换行；不影响首页现有模块。

## 5. 风险

目标页面尚未发布，可能 404；这是后续内容 CR 的范围。
