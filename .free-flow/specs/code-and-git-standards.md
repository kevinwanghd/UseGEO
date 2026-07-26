# UseGEO 代码目录与 Git / PR 标准

## 1. 目标

这份文档只解决两个问题：

1. 开发代码放哪里。
2. 提交、分支、PR 怎么做才不乱。

原则是：

- 结构清楚
- 适合当前 MVP
- 不提前设计过重的工程体系

## 2. 推荐代码目录

`UseGEO` 当前建议采用下面这套结构：

```text
UseGEO/
  docs/
  src/
    modules/
      geo-visibility-scanner/
      roi-calculator/
    shared/
      styles/
      utils/
      components/
    templates/
      homepage/
      article-sidebar/
  wordpress/
    theme/
    plugins/
  assets/
    images/
    icons/
  tests/
```

## 3. 每个目录的职责

## 3.1 `src/`

这里放“你真正写的前端源代码”。

### `src/modules/`

放核心业务模块：

- `geo-visibility-scanner/`
- `roi-calculator/`

每个模块里建议包含：

- 结构代码
- 模块样式
- 模块交互逻辑

### `src/shared/`

放复用内容：

- `styles/`：全局变量、通用样式
- `utils/`：通用函数
- `components/`：按钮、输入框、卡片、表单块、CTA 块

### `src/templates/`

放页面级组合结构，不放底层组件：

- `homepage/`
- `article-sidebar/`

## 3.2 `wordpress/`

这里放和 WordPress 集成有关的内容。

### `wordpress/theme/`

放主题模板相关代码，例如：

- 首页模板
- 文章模板
- Footer 模板

### `wordpress/plugins/`

放插件或 shortcode 封装，例如：

- 诊断工具 shortcode
- 侧边栏 CTA shortcode

## 3.3 `assets/`

只放静态资源：

- 图片
- 图标
- Logo

不要把业务逻辑 JS 放这里。

## 3.4 `tests/`

当前阶段不用复杂，但建议至少保留目录，用于：

- 手工测试清单
- 简单脚本验证
- 后续 UI 校验

## 4. 当前阶段最小落地建议

如果你现在就开始做 MVP，我建议先只创建这些目录：

```text
src/
  modules/
  shared/
  templates/
wordpress/
  theme/
  plugins/
assets/
tests/
```

这样足够支撑后续开发，而且不会显得臃肿。

## 5. 文件放置规则

### 放 `src/modules/` 的内容

- 诊断器逻辑
- ROI 计算逻辑
- 模块内部样式
- 模块状态管理

### 放 `src/shared/` 的内容

- 颜色变量
- spacing 变量
- 通用按钮样式
- 表单输入组件
- 通用工具函数

### 放 `wordpress/` 的内容

- WordPress 模板
- shortcode 封装
- WP 挂载逻辑

### 不应该出现的情况

- 把所有 HTML/CSS/JS 全堆在根目录
- 把 WordPress 模板和业务模块混在一起
- 同一个模块拆散到多个不相关目录

## 6. Git 分支标准

当前建议使用非常简单的分支规则：

- `main`
- `feature/<topic>`
- `fix/<topic>`
- `docs/<topic>`

示例：

- `feature/homepage-hero`
- `feature/geo-scanner-mvp`
- `fix/mobile-layout`
- `docs/prd-refine`

## 7. Commit 标准

建议直接使用简化版 conventional commits：

- `feat`: 新功能
- `fix`: 修复问题
- `docs`: 文档修改
- `refactor`: 重构
- `style`: 纯样式调整
- `chore`: 杂项维护

格式：

```text
type(scope): summary
```

示例：

- `feat(homepage): add hero section skeleton`
- `feat(scanner): add loading and result states`
- `fix(mobile): correct sticky cta spacing`
- `docs(engineering): add development plan`
- `style(homepage): refine card spacing and typography`

## 8. Commit 粒度标准

每次提交应满足：

- 解决一个明确问题
- 能说清楚改了什么
- 尽量可回滚

不要这样提交：

- `update`
- `fix stuff`
- `final version`
- `misc changes`

也不要把完全无关的修改塞进一个 commit。

## 9. PR 标准

如果当前仓库会走 PR 流程，建议每个 PR 都包含这 5 项：

1. 目的
2. 改动范围
3. 页面或组件影响
4. 验证方式
5. 风险或未完成项

推荐模板：

```md
## Summary
- 这次解决了什么问题

## Changes
- 改了哪些页面 / 模块

## Verification
- 如何验证
- 是否做了移动端检查

## Risks / Follow-ups
- 已知限制
- 下一步要做什么
```

## 10. 什么情况下必须开 PR

即使团队很小，也建议以下情况走 PR：

- 改首页 Hero
- 改核心诊断工具逻辑
- 改表单和转化链路
- 改 WordPress 集成方式
- 改全局样式变量

如果只是：

- 文案错别字
- 单个样式小修

可以直接提交，但仍建议遵守 commit 标准。

## 11. 合并前最低检查

每次合并前至少确认：

1. 桌面端页面没坏
2. 移动端主路径可用
3. 主 CTA 正常
4. 工具输入和结果显示正常
5. 没有把调试代码留在线上版本

## 12. 当前推荐做法

对 `UseGEO` 当前阶段，我建议这样执行：

1. 先按本文件创建基础目录。
2. 所有业务代码先写进 `src/`。
3. 所有 WordPress 适配放 `wordpress/`。
4. 每个功能一个独立分支。
5. 每个功能至少有 1 个清晰 commit。

## 13. 一句话标准

**业务代码放 `src`，WordPress 适配放 `wordpress`，提交用清晰的 `feat/fix/docs`，PR 只讲目的、改动、验证、风险。**
