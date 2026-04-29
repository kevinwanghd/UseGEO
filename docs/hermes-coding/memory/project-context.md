# Project Context

## 目的
沉淀 UseGEO 项目的长期稳定背景信息，作为 Hermes 执行任务时的项目级记忆。

---

## 项目身份
- 项目名称：`UseGEO`
- 项目根目录：`D:/Code/UseGEO`
- 当前主要开发项目：`UseGEO`
- 项目定位：`面向 GEO / AIGC 营销场景的 WordPress-first MVP 站点`
- 当前阶段：`MVP`
- 当前目标：`尽快上线一个可验证主转化闭环的产品型站点`

---

## 当前稳定技术判断
- UseGEO 当前明确采用 **WordPress-first** 路线
- 当前运行时代码的默认主入口是：`wordpress/theme/usegeo/`
- Scanner 和 ROI 当前都在 theme 内实现
- 默认不优先插件化
- 默认不优先做 shortcode 体系

---

## 目录职责的长期判断
- 运行时代码主目录：`wordpress/theme/usegeo/`
- 可复用模块目录：`wordpress/theme/usegeo/template-parts/`
- 样式目录：`wordpress/theme/usegeo/assets/css/`
- 脚本目录：`wordpress/theme/usegeo/assets/js/`
- 文档主目录：`docs/`
- 原型 / 上游目录：`src/`

### 当前对 `src/` 的稳定判断
`src/` 当前不是运行时主入口。
默认不要把 `src/` 当成当前线上行为的真实来源。

---

## 高风险区域的长期记忆
- `wordpress/theme/usegeo/functions.php`
- 首页 Hero
- Scanner 模块
- 留资表单
- Lead Capture 流程
- `assets/css/base.css`
- 文章页 CTA
- 共享 `template-parts`

---

## Hermes 在 UseGEO 的默认工作方式
1. 先读 `source-of-truth.md`
2. 再读 `project-map.md`
3. 根据 `ROUTING.md` 选择任务分支
4. 默认从 `wordpress/theme/usegeo/` 找运行时代码
5. 默认最小改动
6. 第一次失败后先分析，不盲试
7. 未验证前不宣称完成
