# UseGEO WordPress 集成方案

## 1. 目标

`UseGEO` 当前阶段采用 **WordPress-first** 的实现方式。

目标是：

- 尽量使用 WordPress 原生能力承载页面
- 用一个自定义轻主题完成 MVP
- 不提前引入重前端框架或复杂插件体系
- 在保证后续扩展性的前提下，优先缩短首版交付路径

## 2. 最终选择

当前确认方案：

- 页面结构：`自定义轻主题`
- 工具集成：`MVP 写死在主题里`
- 技术原则：`能用 WordPress 的，尽量全部 WordPress`

这意味着：

- 首页、文章页、Footer 由主题模板承载
- 品牌 AI 曝光度诊断器和 ROI 工具先作为主题组件实现
- 后续只有在主题内复用已经明显失控时，才考虑抽 shortcode 或插件

## 3. 为什么这样做

相比“插件优先”或“前后端分离”，当前方案更适合 UseGEO 的原因是：

- MVP 的重点是尽快上线并验证转化闭环
- 首页本质是产品型 landing page，主题模板控制力最高
- 文章页、内容管理、SEO、路由都属于 WordPress 强项
- 两个工具都不复杂，没必要一开始就做插件体系

## 4. 代码放置规则

运行时代码以 `wordpress/theme/usegeo` 为核心。

建议结构：

```text
wordpress/
  theme/
    usegeo/
      style.css
      functions.php
      index.php
      front-page.php
      single.php
      page.php
      header.php
      footer.php
      inc/
      template-parts/
        hero/
        scanner/
        roi/
        content-hub/
        cta/
      assets/
        css/
        js/
        images/
```

`src/` 仍保留，但当前阶段定位为：

- 前端源文件区
- 原型代码区
- 将来如需构建流程时的上游目录

当前 MVP **不以 `src/` 作为运行时主入口**。

## 5. 页面集成方式

### 首页

- 使用 `front-page.php`
- 作为主 landing page 模板
- 直接组合 Hero、诊断器、价值区、内容区、Footer

### 文章详情页

- 使用 `single.php`
- 主阅读区由主题模板承载
- 侧边栏 CTA 或 ROI 工具通过 `template-parts` 引入

### 静态页面

- 使用 `page.php`
- 用于 About、Privacy、Cookie 等页面

## 6. 组件集成方式

两个核心工具在 MVP 阶段都放在主题内：

- `template-parts/scanner/`
- `template-parts/roi/`

调用方式统一使用：

```php
get_template_part('template-parts/scanner/scanner');
get_template_part('template-parts/roi/roi');
```

这样做的好处：

- 模块边界清晰
- 页面内可复用
- 不需要提前进入插件抽象

## 7. 样式和脚本策略

### 样式

- 全局变量和基础样式放 `assets/css/`
- 页面级和模块级样式按需拆分
- 优先使用 WordPress enqueue 统一加载

### 脚本

- 工具交互脚本放 `assets/js/`
- 不引入重前端框架
- 原生 JS 足以满足当前诊断器和 ROI 工具的需要

## 8. 什么时候才允许抽插件

只有出现以下情况之一，才考虑把模块从主题中抽到 `wordpress/plugins/`：

- 同一个工具需要跨多个页面或多个站点复用
- 后台需要独立配置界面
- 组件生命周期开始独立于主题演进
- 主题内维护成本已经明显高于插件化收益

在此之前，**默认不抽插件**。

## 9. 所有开发 Agent 必须遵守

以下规则对所有开发 agent 生效：

1. 运行时代码优先写入 `wordpress/theme/usegeo`
2. 不引入 React、Vue 或其他重前端框架
3. 不在 MVP 第一阶段把工具做成独立插件
4. 不把 HTML/CSS/JS 散落在仓库根目录
5. 页面级结构写模板，复用模块写 `template-parts`
6. 全局样式变量必须统一管理，不能每个模块各写一套颜色和间距
7. 改首页 Hero、表单、核心工具、全局样式时，必须遵守 PR 和 commit 标准

## 10. 当前实施顺序

建议按以下顺序开发：

1. 先搭 `usegeo` 主题骨架
2. 接入全局样式和 assets enqueue
3. 完成 `front-page.php`
4. 完成 Hero 与主诊断器
5. 完成留资区
6. 再补 ROI 工具与文章页侧边栏

## 11. 一句话结论

**UseGEO 当前按自定义轻主题开发，页面和工具都优先放在主题内，等复用复杂度上来后再考虑 shortcode 或插件化。**
