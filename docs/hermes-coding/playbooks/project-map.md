# Project Map

## 目的
说明 UseGEO 项目的主要结构、运行时代码位置、关键入口、高风险区域和默认修改边界。

---

## 项目概览
- 项目名称：`UseGEO`
- 项目根目录：`D:/Code/UseGEO`
- 主要用途：`基于 WordPress 的 GEO / AIGC 营销站点 MVP`
- 主要技术栈：`WordPress + 自定义轻主题 + PHP + CSS + Vanilla JS`
- 运行平台：`WordPress`
- 部署方式：`当前以 WordPress 主题集成为核心，运行时代码优先放在 theme 内`

---

## 目录结构概览
- `docs/`：项目文档主目录，包含产品、工程、报告文档
- `src/`：前端源文件区 / 原型区 / 上游开发区，当前 **不是运行时主入口**
- `tests/`：测试相关目录
- `wordpress/`：WordPress 运行时相关目录
- `wordpress/theme/usegeo/`：**当前运行时代码主目录**
- `wordpress/theme/usegeo/template-parts/`：可复用页面模块
- `wordpress/theme/usegeo/assets/css/`：主题样式
- `wordpress/theme/usegeo/assets/js/`：主题交互脚本
- `wordpress/plugins/`：插件目录，当前 MVP 默认不优先走插件化

---

## 关键入口

### 应用入口
- 运行时主目录：`wordpress/theme/usegeo/`
- 首页模板：`wordpress/theme/usegeo/front-page.php`
- 标准页面模板：`wordpress/theme/usegeo/page.php`
- 文章详情模板：`wordpress/theme/usegeo/single.php`
- 默认索引模板：`wordpress/theme/usegeo/index.php`
- 通用头尾模板：`header.php` / `footer.php`

### 接口入口
- 主题内表单提交入口通过 WordPress `admin_post` 机制接入
- Lead capture 处理函数位置：`wordpress/theme/usegeo/functions.php`

### 配置入口
- 主题主逻辑入口：`wordpress/theme/usegeo/functions.php`
- 主题基础声明文件：`wordpress/theme/usegeo/style.css`
- 仓库内 agent 规则：`CLAUDE.md`
- 文档导航：`README_DOCS.md`

---

## 核心模块

### 模块 1：首页 Landing Page
- 目录：`front-page.php`、`template-parts/hero/`、`template-parts/scanner/`、`template-parts/value/`、`template-parts/content-hub/`、`template-parts/roi/`
- 作用：承载 MVP 首页、Hero、诊断器、价值说明、内容区和 ROI 工具
- 修改风险：高

### 模块 2：文章详情页与侧边 CTA
- 目录：`single.php`、`template-parts/cta/sidebar-cta.php`
- 作用：承载文章阅读路径和内容转化辅助入口
- 修改风险：中高

### 模块 3：Lead Capture 与主题基础逻辑
- 目录：`functions.php`
- 作用：主题初始化、资源加载、Lead post type、表单提交流程
- 修改风险：很高

---

## 高风险区域
- `wordpress/theme/usegeo/functions.php`
- 首页 Hero
- Scanner 模块
- 留资表单
- `assets/css/base.css`
- 文章页 CTA
- 共享 `template-parts`

---

## 常改位置
- UI 修改：`front-page.php`、`template-parts/`、`assets/css/`
- 交互修改：`assets/js/scanner.js`、`assets/js/roi.js`、`assets/js/tracking.js`
- 主题逻辑修改：`functions.php`
- 文档修改：`docs/product/`、`docs/engineering/`、`docs/reports/`

---

## Hermes 使用说明
1. 先确认任务是否属于 WordPress 运行时代码
2. 若是运行时任务，优先进入 `wordpress/theme/usegeo/`
3. 不要默认把 `src/` 当运行时主入口
4. 默认最小改动，不跨模块扩散修改
5. 不确定配置来源时，先看 `source-of-truth.md`
