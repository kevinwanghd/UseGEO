# Design Spec: Partner Lead Export / Pipeline

## Scope

B端后台增强，不改前台。

## 后台列表

新增筛选器：

- Partner Consent: all / yes / no
- Lead Quality: all / hot / warm / cold
- Partner Status: all / new / reviewing / contacted / sold / rejected

新增导出按钮：

`Export Partner Leads CSV`

按钮位置：usegeo_lead 列表顶部筛选栏附近。

## Lead 详情页

新增：

- Partner Status select
- Partner Notes textarea

## CSV

只导出授权 lead，不导出长文本 scan summary。

## 视觉规范

沿用 WordPress admin 原生 form-table / tablenav 风格。
