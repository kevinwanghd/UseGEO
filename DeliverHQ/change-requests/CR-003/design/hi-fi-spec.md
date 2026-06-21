# Design Spec: Partner Lead Monetization Foundation

> B 端/表单微改设计规格。复用现有 scanner lead gate UI，不重做页面。

## 1. 前端表单新增字段

位置：Full Diagnostic / expert follow-up 表单中，放在 timeline 字段之后、submit button 之前。

### budget_range

Label: `What budget range would be realistic if you decide to get outside help?`

Options:
- empty: Select one
- under_500: Under $500
- 500_1500: $500–$1,500
- 1500_5000: $1,500–$5,000
- 5000_plus: $5,000+
- not_sure: Not sure yet

### region

Label: `Where is your team primarily based?`

Options:
- empty: Select one
- north_america: North America
- europe: Europe
- asia_pacific: Asia-Pacific
- global_remote: Global / remote
- other: Other

### partner_consent

Checkbox text:

`I agree that UseGEO may share my submission with relevant GEO/SEO partners who can contact me about services that may fit my needs.`

默认未勾选。

## 2. CTA / disclosure 文案

将“UseGEO 自己提供服务”的暗示改为 partner matching：

- Button: `Request expert follow-up`
- Form heading: `Get matched with relevant GEO help`
- Disclosure: `We use your submission to personalize the assessment. If you opt in, we may share it with relevant GEO/SEO partners for follow-up.`

## 3. 后台列

新增列：

- Partner Consent
- Lead Quality
- Budget
- Region

位置：GEO Score / Weakest Dimension 之后，Notification 之前。

## 4. 视觉规范

沿用现有：

- `.usegeo-report-gate__form`
- `.usegeo-community-form__checkbox` 或等价 checkbox 样式
- `.usegeo-report-gate__disclosure`

不新增新视觉系统。

## 5. DesignGate 检查点

- [x] `metadata.yml` 声明 `ui_type: B端`
- [x] 表单新增字段位置明确
- [x] consent 文案明确
- [x] 后台列位置明确
- [x] 无模板变量
